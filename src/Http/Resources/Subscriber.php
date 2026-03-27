<?php

namespace Sendportal\Base\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Sendportal\Base\Http\Resources\Tag as TagResource;
use Sendportal\Base\Http\Resources\Location as LocationResource;
use Sendportal\Base\Http\Resources\Skill as SkillResource;
use Sendportal\Base\Http\Resources\Industry as IndustryResource;
use Sendportal\Base\Http\Resources\Level as LevelResource;

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
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'industries' => IndustryResource::collection($this->whenLoaded('industries')),
            'levels' => LevelResource::collection($this->whenLoaded('levels')),
            'unsubscribed_at' => $this->unsubscribed_at ? $this->unsubscribed_at->toDateTimeString() : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
