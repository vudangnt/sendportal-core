<?php

namespace Sendportal\Base\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Level extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'update_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
