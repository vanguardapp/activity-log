<?php

namespace Vanguard\UserActivity\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        $agent = app('agent');
        $agent->setUserAgent($this->resource->user_agent);

        return [
            'id' => (int) $this->id,
            'user_id' => (int) $this->user_id,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'device' => $agent->device(),
            'description' => $this->description,
            'created_at' => (string) $this->created_at,
            'additional_data' => $this->additional_data
        ];
    }
}
