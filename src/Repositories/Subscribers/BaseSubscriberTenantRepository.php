<?php

declare(strict_types=1);

namespace Sendportal\Base\Repositories\Subscribers;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Sendportal\Base\Models\Subscriber;
use Sendportal\Base\Repositories\BaseTenantRepository;
use Sendportal\Base\Repositories\IndustryTenantRepository;
use Sendportal\Base\Repositories\LevelTenantRepository;
use Sendportal\Base\Repositories\LocationTenantRepository;
use Sendportal\Base\Repositories\SkillTenantRepository;
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

    /** @var SkillTenantRepository */
    protected $skillRepo;

    /** @var IndustryTenantRepository */
    protected $industryRepo;

    /** @var LevelTenantRepository */
    protected $levelRepo;

    public function __construct(
        TagTenantRepository $tagRepo,
        LocationTenantRepository $locationRepo,
        SkillTenantRepository $skillRepo,
        IndustryTenantRepository $industryRepo,
        LevelTenantRepository $levelRepo
    ) {
        $this->tagRepo = $tagRepo;
        $this->locationRepo = $locationRepo;
        $this->skillRepo = $skillRepo;
        $this->industryRepo = $industryRepo;
        $this->levelRepo = $levelRepo;
    }

    /**
     * {@inheritDoc}
     */
    public function store($workspaceId, array $data)
    {
        $this->checkTenantData($data);

        /** @var Subscriber $instance */
        $instance = $this->getNewInstance();

        $subscriber = $this->executeSave($workspaceId, $instance, Arr::except($data, ['tags', 'locations', 'skills', 'industries', 'levels']));

        if (isset($data['tags'])) {
            $this->syncTags($instance, Arr::get($data, 'tags', []));
        }
        if (isset($data['locations'])) {
            $this->syncLocations($instance, Arr::get($data, 'locations', []));
        }
        if (isset($data['skills'])) {
            $this->syncSkills($instance, Arr::get($data, 'skills', []));
        }
        if (isset($data['industries'])) {
            $this->syncIndustries($instance, Arr::get($data, 'industries', []));
        }
        if (isset($data['levels'])) {
            $this->syncLevels($instance, Arr::get($data, 'levels', []));
        }
        return $subscriber;
    }

    /**
     * Sync Tags to a Subscriber.
     */
    public function syncTags(Subscriber $subscriber, array $tags = [])
    {
        $tagIds = $this->normalizeEntityIdentifiers($subscriber->workspace_id, $tags, $this->tagRepo);
        return $subscriber->tags()->sync($tagIds);
    }

    public function syncLocations(Subscriber $subscriber, array $locations = [])
    {
        $locationIds = $this->normalizeLocationIdentifiers($subscriber->workspace_id, $locations);
        return $subscriber->locations()->sync($locationIds);
    }

    public function syncSkills(Subscriber $subscriber, array $skills = [])
    {
        $skillIds = $this->normalizeEntityIdentifiers($subscriber->workspace_id, $skills, $this->skillRepo);
        return $subscriber->skills()->sync($skillIds);
    }

    public function syncIndustries(Subscriber $subscriber, array $industries = [])
    {
        $industryIds = $this->normalizeEntityIdentifiers($subscriber->workspace_id, $industries, $this->industryRepo);
        return $subscriber->industries()->sync($industryIds);
    }

    public function syncLevels(Subscriber $subscriber, array $levels = [])
    {
        $levelIds = $this->normalizeEntityIdentifiers($subscriber->workspace_id, $levels, $this->levelRepo);
        return $subscriber->levels()->sync($levelIds);
    }

    /**
     * {@inheritDoc}
     */
    public function update($workspaceId, $id, array $data)
    {
        $this->checkTenantData($data);

        $instance = $this->find($workspaceId, $id);

        $subscriber = $this->executeSave($workspaceId, $instance, Arr::except($data, ['tags', 'locations', 'skills', 'industries', 'levels', 'id']));

        if (isset($data['tags'])) {
            $this->syncTags($instance, Arr::get($data, 'tags', []));
        }
        if (isset($data['locations'])) {
            $this->syncLocations($instance, Arr::get($data, 'locations', []));
        }
        if (isset($data['skills'])) {
            $this->syncSkills($instance, Arr::get($data, 'skills', []));
        }
        if (isset($data['industries'])) {
            $this->syncIndustries($instance, Arr::get($data, 'industries', []));
        }
        if (isset($data['levels'])) {
            $this->syncLevels($instance, Arr::get($data, 'levels', []));
        }
        return $subscriber;
    }

    /**
     * Return the count of active subscribers
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
        $this->applySkillFilter($instance, $filters);
        $this->applyIndustryFilter($instance, $filters);
        $this->applyLevelFilter($instance, $filters);
    }

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

    protected function applySkillFilter(Builder $instance, array $filters = []): void
    {
        if ($skillIds = Arr::get($filters, 'skills')) {
            $instance->whereHas('skills', function (Builder $query) use ($skillIds) {
                $query->whereIn('sendportal_skills.id', $skillIds);
            });
        }
    }

    protected function applyIndustryFilter(Builder $instance, array $filters = []): void
    {
        if ($industryIds = Arr::get($filters, 'industries')) {
            $instance->whereHas('industries', function (Builder $query) use ($industryIds) {
                $query->whereIn('sendportal_industries.id', $industryIds);
            });
        }
    }

    protected function applyLevelFilter(Builder $instance, array $filters = []): void
    {
        if ($levelIds = Arr::get($filters, 'levels')) {
            $instance->whereHas('levels', function (Builder $query) use ($levelIds) {
                $query->whereIn('sendportal_levels.id', $levelIds);
            });
        }
    }

    /**
     * Generic method to normalize entity identifiers (tags, skills, industries, levels).
     * Accepts integer IDs, string names (will find-or-create), or arrays with id/name.
     *
     * @param int $workspaceId
     * @param array $items
     * @param BaseTenantRepository $repo
     * @return array<int>
     */
    protected function normalizeEntityIdentifiers(int $workspaceId, array $items, BaseTenantRepository $repo): array
    {
        return collect($items)->map(function ($item) use ($workspaceId, $repo) {
            if (is_array($item)) {
                $possibleId = Arr::get($item, 'id', Arr::get($item, 'value'));
                if ($possibleId !== null && $possibleId !== '') {
                    return (int) $possibleId;
                }
                $item = Arr::get($item, 'name', Arr::get($item, 'label'));
            }

            if (is_numeric($item)) {
                return (int) $item;
            }

            if (is_string($item)) {
                $name = trim($item);
                if ($name === '') {
                    return null;
                }

                $existing = $repo->findBy($workspaceId, 'name', $name);
                if (!$existing) {
                    $existing = $repo->store($workspaceId, [
                        'name' => $name,
                    ]);
                }
                return $existing->id;
            }

            return null;
        })->filter()->unique()->values()->all();
    }

    /**
     * Normalize location identifiers with special handling for global unique names.
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

                $existing = $this->locationRepo->findBy($workspaceId, 'name', $locationName);

                if (!$existing) {
                    $existing = $this->locationRepo->getNewInstance()
                        ->where('name', $locationName)
                        ->first();
                }

                if (!$existing) {
                    try {
                        $existing = $this->locationRepo->store($workspaceId, [
                            'name' => $locationName,
                        ]);
                    } catch (\Exception $e) {
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
