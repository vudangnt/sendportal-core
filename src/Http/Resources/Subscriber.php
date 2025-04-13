<?php

namespace Sendportal\Base\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Sendportal\Base\Http\Resources\Tag as TagResource;
use Sendportal\Base\Http\Resources\Location as LocationResource;

class Subscriber extends JsonResource
{
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'locations' => LocationResource::collection($this->whenLoaded('locations')),
            'unsubscribed_at' => $this->unsubscribed_at ? $this->unsubscribed_at->toDateTimeString() : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
