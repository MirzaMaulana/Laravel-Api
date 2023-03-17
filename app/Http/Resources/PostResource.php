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
            'content' => $this->content,
            'like' => $this->likes ? $this->likes->count() : null,
            'tags' => $this->tag ? $this->tag : null,
            'created_by' => $this->created_by,
            'created_at' => $this->createdAtFormat
        ];
    }
}
