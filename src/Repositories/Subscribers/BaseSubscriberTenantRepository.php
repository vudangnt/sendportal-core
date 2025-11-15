<?php

declare(strict_types=1);

namespace Sendportal\Base\Repositories\Subscribers;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Sendportal\Base\Models\Subscriber;
use Sendportal\Base\Repositories\BaseTenantRepository;
use Sendportal\Base\Repositories\LocationTenantRepository;
use Sendportal\Base\Repositories\TagTenantRepository;

abstract class BaseSubscriberTenantRepository extends BaseTenantRepository implements
    SubscriberTenantRepositoryInterface
{
    /** @var string */
    protected $modelName = Subscriber::class;

    /** @var TagTenantRepository */
    protected $tagRepo;

    /** @var LocationTenantRepository */
    protected $locationRepo;

    public function __construct(TagTenantRepository $tagRepo, LocationTenantRepository $locationRepo)
    {
        $this->tagRepo = $tagRepo;
        $this->locationRepo = $locationRepo;
    }

    /**
     * {@inheritDoc}
     */
    public function store($workspaceId, array $data)
    {
        $this->checkTenantData($data);

        /** @var Subscriber $instance */
        $instance = $this->getNewInstance();

        $subscriber = $this->executeSave($workspaceId, $instance, Arr::except($data, ['tags', 'locations']));

        // Only sync tags if its actually present. This means that users must
        // pass through an empty tags array if they want to delete all tags.
        if (isset($data['tags'])) {
            $this->syncTags($instance, Arr::get($data, 'tags', []));
        }
        if (isset($data['locations'])) {
            $this->syncLocations($instance, Arr::get($data, 'locations', []));
        }
        return $subscriber;
    }

    /**
     * Sync Tags to a Subscriber.
     *
     * @param Subscriber $subscriber
     * @param array $tags
     *
     * @return mixed
     */
    public function syncTags(Subscriber $subscriber, array $tags = [])
    {
        $tagIds = $this->normalizeTagIdentifiers($subscriber->workspace_id, $tags);

        return $subscriber->tags()->sync($tagIds);
    }

    public function syncLocations(Subscriber $subscriber, array $locations = [])
    {
        $locationIds = $this->normalizeLocationIdentifiers($subscriber->workspace_id, $locations);

        return $subscriber->locations()->sync($locationIds);
    }

    /**
     * {@inheritDoc}
     */
    public function update($workspaceId, $id, array $data)
    {

        $this->checkTenantData($data);

        $instance = $this->find($workspaceId, $id);

        $subscriber = $this->executeSave($workspaceId, $instance, Arr::except($data, ['tags', 'locations', 'id']));

        // Only sync tags if its actually present. This means that users must
        // pass through an empty tags array if they want to delete all tags.
        if (isset($data['tags'])) {
            $this->syncTags($instance, Arr::get($data, 'tags', []));
        }
        if (isset($data['locations'])) {
            $this->syncLocations($instance, Arr::get($data, 'locations', []));
        }
        return $subscriber;
    }

    /**
     * Return the count of active subscribers
     *
     * @param int $workspaceId
     *
     * @return mixed
     * @throws Exception
     */
    public function countActive($workspaceId): int
    {
        return $this->getQueryBuilder($workspaceId)
            ->whereNull('unsubscribed_at')
            ->count();
    }

    public function getRecentSubscribers(int $workspaceId): Collection
    {
        return $this->getQueryBuilder($workspaceId)
            ->orderBy('created_at', 'DESC')
            ->take(10)
            ->get();
    }

    /**
     * @inheritDoc
     */
    protected function applyFilters(Builder $instance, array $filters = []): void
    {
        $this->applyNameFilter($instance, $filters);
        $this->applyStatusFilter($instance, $filters);
        $this->applyTagFilter($instance, $filters);
        $this->applyLocationFilter($instance, $filters);
    }

    /**
     * Filter by name or email.
     */
    protected function applyNameFilter(Builder $instance, array $filters): void
    {
        if ($name = Arr::get($filters, 'name')) {
            $filterString = '%' . $name . '%';

            $instance->where(static function (Builder $instance) use ($filterString) {
                $instance->where('sendportal_subscribers.first_name', 'like', $filterString)
                    ->orWhere('sendportal_subscribers.last_name', 'like', $filterString)
                    ->orWhere('sendportal_subscribers.email', 'like', $filterString);
            });
        }
    }

    /**
     * Filter by subscription status.
     */
    protected function applyStatusFilter(Builder $instance, array $filters): void
    {
        $status = Arr::get($filters, 'status');

        if ($status === 'subscribed') {
            $instance->whereNull('unsubscribed_at');
        } elseif ($status === 'unsubscribed') {
            $instance->whereNotNull('unsubscribed_at');
        } elseif ($status === 'no_tags') {
            $instance->whereDoesntHave('tags');
        } elseif ($status === 'no_locations') {
            $instance->whereDoesntHave('locations');
        }
    }

    /**
     * Filter by tag.
     */
    protected function applyTagFilter(Builder $instance, array $filters = []): void
    {
        if ($tagIds = Arr::get($filters, 'tags')) {
            $instance->select('sendportal_subscribers.*')
                ->leftJoin(
                    'sendportal_tag_subscriber',
                    'sendportal_subscribers.id',
                    '=',
                    'sendportal_tag_subscriber.subscriber_id'
                )
                ->whereIn('sendportal_tag_subscriber.tag_id', $tagIds)
                ->distinct();
        }
    }

    protected function applyLocationFilter(Builder $instance, array $filters = []): void
    {
        if ($locationIds = Arr::get($filters, 'locations')) {
            $instance->select('sendportal_subscribers.*')
                ->leftJoin(
                    'sendportal_location_subscriber',
                    'sendportal_subscribers.id',
                    '=',
                    'sendportal_location_subscriber.subscriber_id'
                )
                ->whereIn('sendportal_location_subscriber.location_id', $locationIds)
                ->distinct();
        }
    }

    /**
     * @param int $workspaceId
     * @param array<int|string|array<string,mixed>> $tags
     *
     * @return array<int>
     */
    protected function normalizeTagIdentifiers(int $workspaceId, array $tags): array
    {
        return collect($tags)->map(function ($tag) use ($workspaceId) {
            if (is_array($tag)) {
                $possibleId = Arr::get($tag, 'id', Arr::get($tag, 'value'));
                if ($possibleId !== null && $possibleId !== '') {
                    return (int) $possibleId;
                }

                $tag = Arr::get($tag, 'name', Arr::get($tag, 'label'));
            }

            if (is_numeric($tag)) {
                return (int) $tag;
            }

            if (is_string($tag)) {
                $tagName = trim($tag);

                if ($tagName === '') {
                    return null;
                }

                $existing = $this->tagRepo->findBy($workspaceId, 'name', $tagName);

                if (!$existing) {
                    $existing = $this->tagRepo->store($workspaceId, [
                        'name' => $tagName,
                    ]);
                }

                return $existing->id;
            }

            return null;
        })->filter()->unique()->values()->all();
    }

    /**
     * Normalize location identifiers (ID, name string, or array with id/name) to array of location IDs.
     * Creates location if it doesn't exist when provided as a string name.
     *
     * @param int $workspaceId
     * @param array $locations
     * @return array<int>
     */
    protected function normalizeLocationIdentifiers(int $workspaceId, array $locations): array
    {
        return collect($locations)->map(function ($location) use ($workspaceId) {
            if (is_array($location)) {
                $possibleId = Arr::get($location, 'id', Arr::get($location, 'value'));
                if ($possibleId !== null && $possibleId !== '') {
                    return (int) $possibleId;
                }

                $location = Arr::get($location, 'name', Arr::get($location, 'label'));
            }

            if (is_numeric($location)) {
                return (int) $location;
            }

            if (is_string($location)) {
                $locationName = trim($location);

                if ($locationName === '') {
                    return null;
                }

                // Tìm kiếm theo workspace_id trước
                $existing = $this->locationRepo->findBy($workspaceId, 'name', $locationName);

                // Nếu không tìm thấy, tìm kiếm global (vì unique constraint là global)
                if (!$existing) {
                    $existing = $this->locationRepo->getNewInstance()
                        ->where('name', $locationName)
                        ->first();
                }

                // Nếu vẫn không tìm thấy, tạo mới
                if (!$existing) {
                    try {
                        $existing = $this->locationRepo->store($workspaceId, [
                            'name' => $locationName,
                        ]);
                    } catch (\Exception $e) {
                        // Nếu duplicate (có thể do race condition), tìm lại
                        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            $existing = $this->locationRepo->getNewInstance()
                                ->where('name', $locationName)
                                ->first();
                            
                            if (!$existing) {
                                throw $e;
                            }
                        } else {
                            throw $e;
                        }
                    }
                }

                return $existing->id;
            }

            return null;
        })->filter()->unique()->values()->all();
    }
}
