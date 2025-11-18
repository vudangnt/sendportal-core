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
        // Convert category and location text to tags/locations arrays
        $dataArray = $data->toArray();
        
        // Handle category text -> tags
        // Support both single value and comma-separated values: "IT, Developer, Laravel"
        if (isset($dataArray['category']) && is_string($dataArray['category']) && trim($dataArray['category']) !== '') {
            if (!isset($dataArray['tags']) || !is_array($dataArray['tags'])) {
                $dataArray['tags'] = [];
            }
            
            // Parse comma-separated values
            $categories = array_map('trim', explode(',', $dataArray['category']));
            $categories = array_filter($categories, function($cat) {
                return !empty($cat);
            });
            
            // Merge with existing tags array
            $dataArray['tags'] = array_merge($dataArray['tags'], $categories);
            unset($dataArray['category']);
        }
        
        // Handle location text -> locations
        // Support both single value and comma-separated values: "Ho Chi Minh, Ha Noi"
        if (isset($dataArray['location']) && is_string($dataArray['location']) && trim($dataArray['location']) !== '') {
            if (!isset($dataArray['locations']) || !is_array($dataArray['locations'])) {
                $dataArray['locations'] = [];
            }
            
            // Parse comma-separated values
            $locations = array_map('trim', explode(',', $dataArray['location']));
            $locations = array_filter($locations, function($loc) {
                return !empty($loc);
            });
            
            // Merge with existing locations array
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

    public function delete(int $workspaceId, Subscriber $subscriber): bool
    {
        return DB::transaction(function () use ($workspaceId, $subscriber) {
            $subscriber->tags()->detach();
            return $this->subscribers->destroy($workspaceId, $subscriber->id);
        });
    }
}
