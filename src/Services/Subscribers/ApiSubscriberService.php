<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Subscribers;

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
            $values = array_map('trim', explode(',', $dataArray['category']));
            $values = array_filter($values, fn($v) => !empty($v));
            $dataArray['tags'] = array_merge($dataArray['tags'], $values);
            unset($dataArray['category']);
        }

        // Handle location text -> locations
        if (isset($dataArray['location']) && is_string($dataArray['location']) && trim($dataArray['location']) !== '') {
            if (!isset($dataArray['locations']) || !is_array($dataArray['locations'])) {
                $dataArray['locations'] = [];
            }
            $locations = array_map('trim', explode(',', $dataArray['location']));
            $locations = array_filter($locations, fn($loc) => !empty($loc));
            $dataArray['locations'] = array_merge($dataArray['locations'], $locations);
            unset($dataArray['location']);
        }

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

        $values = array_map('trim', explode(',', $stringValue));
        $values = array_filter($values, fn($val) => !empty($val));
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
