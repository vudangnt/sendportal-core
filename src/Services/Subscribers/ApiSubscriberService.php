<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Subscribers;

use Sendportal\Base\Models\Tag;
use Sendportal\Base\Support\SkillName;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Sendportal\Base\Events\SubscriberAddedEvent;
use Sendportal\Base\Models\Subscriber;
use Sendportal\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;

class ApiSubscriberService
{
    /** @var SubscriberTenantRepositoryInterface */
    protected $subscribers;

    public function __construct(SubscriberTenantRepositoryInterface $subscribers)
    {
        $this->subscribers = $subscribers;
    }

    /**
     * The API provides the ability for the "store" endpoint to both create a new subscriber or update an existing
     * subscriber, using their email as the key. This method allows us to handle both scenarios.
     *
     * @throws Exception
     */
    public function storeOrUpdate(int $workspaceId, Collection $data): Subscriber
    {
        $dataArray = $data->toArray();

        // Convert comma-separated skills text to skills array
        $dataArray = $this->convertCommaFieldToArray($dataArray, 'skills');

        // Convert comma-separated industries text to industries array
        $dataArray = $this->convertCommaFieldToArray($dataArray, 'industries');

        // Convert comma-separated level text to levels array
        $dataArray = $this->convertCommaFieldToArray($dataArray, 'level', 'levels');

        // Convert comma-separated category text to tags (keep category in tags for backward compatibility)
        if (isset($dataArray['category']) && is_string($dataArray['category']) && trim($dataArray['category']) !== '') {
            if (!isset($dataArray['tags']) || !is_array($dataArray['tags'])) {
                $dataArray['tags'] = [];
            }
            // Tôn trọng ngoặc như nhánh skill: explode(',') trần đã băm
            // "Testing (QA, Tester)" thành hai tag rác trên prod.
            $values = SkillName::splitList($dataArray['category']);
            $dataArray['tags'] = array_merge($dataArray['tags'], $values);
            unset($dataArray['category']);
        }

        // Handle location text -> locations
        if (isset($dataArray['location']) && is_string($dataArray['location']) && trim($dataArray['location']) !== '') {
            if (!isset($dataArray['locations']) || !is_array($dataArray['locations'])) {
                $dataArray['locations'] = [];
            }
            $locations = SkillName::splitList($dataArray['location']);
            $dataArray['locations'] = array_merge($dataArray['locations'], $locations);
            unset($dataArray['location']);
        }

        // Audience: producer nào chưa deploy thì payload không có trường này → AUD_UNKNOWN,
        // KHÔNG đoán bừa. Báo cáo TagTaxonomyReport đếm số này theo nguồn để biết còn
        // producer nào chưa xong (xem spec QĐ-3b).
        $audienceMap = [
            'candidate' => 'AUD_CANDIDATE',
            'job-application' => 'AUD_CANDIDATE',
            'employer' => 'AUD_EMPLOYER',
            'learner' => 'AUD_LEARNER',
            'talenthunter' => 'AUD_TALENTHUNTER',
            'affiliate' => 'AUD_TALENTHUNTER',
        ];
        $audienceCode = $audienceMap[$dataArray['audience'] ?? ''] ?? 'AUD_UNKNOWN';
        unset($dataArray['audience']);

        // KHÔNG dùng firstOrCreate: `workspace_id` cố ý KHÔNG fillable (chống rò tenant),
        // nên mass-assignment sẽ nuốt mất nó. Cả repo gán tenant key thẳng lên instance —
        // xem BaseTenantRepository::executeSave().
        $audienceTag = Tag::where('workspace_id', $workspaceId)
            ->where('code', $audienceCode)
            ->first();

        if ($audienceTag === null) {
            $audienceTag = new Tag();
            $audienceTag->fill([
                'name' => $audienceCode,
                'code' => $audienceCode,
                'dimension' => 'AUD',
                'source' => 'ingest',
            ]);
            $audienceTag->workspace_id = $workspaceId;
            $audienceTag->save();
        }

        // normalizeEntityIdentifiers() nhận lẫn id (số) và tên (chuỗi) — xem
        // BaseSubscriberTenantRepository — nên trộn id vào mảng tên là hợp lệ.
        $dataArray['tags'] = array_merge($dataArray['tags'] ?? [], [$audienceTag->id]);

        $existingSubscriber = $this->subscribers->findBy($workspaceId, 'email', $data['email']);

        if (!$existingSubscriber) {
            $subscriber = $this->subscribers->store($workspaceId, $dataArray);
            event(new SubscriberAddedEvent($subscriber));
            return $subscriber;
        }

        return $this->subscribers->update($workspaceId, $existingSubscriber->id, $dataArray);
    }

    /**
     * Convert a comma-separated text field to an array for the repository to process.
     *
     * @param array $dataArray
     * @param string $fieldName The input field name (e.g., 'skills', 'industries', 'level')
     * @param string|null $targetKey The target array key (e.g., 'skills', 'industries', 'levels'). Defaults to $fieldName.
     * @return array
     */
    protected function convertCommaFieldToArray(array $dataArray, string $fieldName, ?string $targetKey = null): array
    {
        $targetKey = $targetKey ?? $fieldName;

        if (!isset($dataArray[$fieldName])) {
            return $dataArray;
        }

        // If already an array, just move to target key if needed
        if (is_array($dataArray[$fieldName])) {
            if ($fieldName !== $targetKey) {
                $dataArray[$targetKey] = $dataArray[$fieldName];
                unset($dataArray[$fieldName]);
            }
            return $dataArray;
        }

        if (!is_string($dataArray[$fieldName]) || trim($dataArray[$fieldName]) === '') {
            unset($dataArray[$fieldName]);
            return $dataArray;
        }

        // Save the string value before potentially overwriting
        $stringValue = $dataArray[$fieldName];

        if (!isset($dataArray[$targetKey]) || !is_array($dataArray[$targetKey])) {
            $dataArray[$targetKey] = [];
        }

        $values = SkillName::splitList($stringValue);
        $dataArray[$targetKey] = array_merge($dataArray[$targetKey], $values);

        if ($fieldName !== $targetKey) {
            unset($dataArray[$fieldName]);
        }

        return $dataArray;
    }

    public function delete(int $workspaceId, Subscriber $subscriber): bool
    {
        return DB::transaction(function () use ($workspaceId, $subscriber) {
            $subscriber->tags()->detach();
            $subscriber->locations()->detach();
            $subscriber->skills()->detach();
            $subscriber->industries()->detach();
            $subscriber->levels()->detach();
            return $this->subscribers->destroy($workspaceId, $subscriber->id);
        });
    }
}
