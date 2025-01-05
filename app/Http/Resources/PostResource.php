<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_aat' => $this->updated_at,
            'user_id' => $this->user_id,
            'audio_path' => $this->audio_path,
            'tags' => TagResource::collection($this->whenLoaded('tags'))
        ];
    }
}
