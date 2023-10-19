<?php

namespace Vanguard\UserActivity\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $agent = app('agent');
        $agent->setUserAgent($this->resource->user_agent);

        return [
            'id' => (int) $this->id,
            'user_id' => (int) $this->user_id,
            'user' => $this->user,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'device' => $agent->device(),
            'description' => $this->description,
            'created_at' => (string) $this->created_at
        ];
    }
}
