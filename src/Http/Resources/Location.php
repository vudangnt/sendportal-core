<?php

namespace Sendportal\Base\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Sendportal\Base\Http\Resources\Subscriber as SubscriberResource;

class Location extends JsonResource
{

    /**
     * code: location code
     * type: city, country, state
     */

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'parent_id' => $this->parent_id,
            'subscribers' => SubscriberResource::collection($this->whenLoaded('subscribers')),
            'created_at' => $this->created_at->toDateTimeString(),
            'update_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
