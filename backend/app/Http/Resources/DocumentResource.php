<?php

namespace App\Http\Resources;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Trên route công khai, user mặc định không phải Sanctum → phải chỉ rõ guard để nhận Bearer token.
        $uid = $request->user('sanctum')?->id;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'published_year' => $this->published_year,
            'isbn' => $this->isbn,
            'language' => $this->language,
            'pages' => $this->pages,
            'file_url' => $this->file_url,
            'cover_image' => $this->cover_image,
            'view_count' => $this->view_count,
            'download_count' => $this->download_count,
            'is_featured' => $this->is_featured,
            'avg_rating' => isset($this->avg_rating) ? round((float) $this->avg_rating, 1) : null,
            'rating_count' => $this->whenCounted('ratings'),
            'is_favorited' => $uid
                ? Favorite::query()->where('user_id', $uid)->where('document_id', $this->id)->exists()
                : false,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'reviews' => RatingResource::collection($this->whenLoaded('ratings')),
            'uploaded_by' => new UserResource($this->whenLoaded('uploader')),
            'status' => $this->status,
            'proposed_by' => $this->proposed_by,
            'reviewed_by' => $this->reviewed_by,
            'reviewed_at' => $this->reviewed_at?->toIso8601String(),
            'rejection_reason' => $this->rejection_reason,
            'proposer' => $this->whenLoaded('proposer', fn () => ['id' => $this->proposer->id, 'name' => $this->proposer->name, 'email' => $this->proposer->email]),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
