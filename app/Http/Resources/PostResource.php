<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
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
        $includeReplies = $request->input('include_replies', false);

        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'views' => $this->views,
            'like' => $this->likes ? $this->likes->count() : null,
            'tags' => $this->tag ? $this->tag : null,
            'created_by' => $this->created_by,
            'created_at' => $this->createdAtFormat,
        ];

        if ($includeReplies) {
            $data['comment'] = CommentResource::collection($this->comment);
        } else {
            $data['comment'] = CommentResource::collection($this->comment->whereNull('parent_id'));
        }

        return $data;
    }
}
