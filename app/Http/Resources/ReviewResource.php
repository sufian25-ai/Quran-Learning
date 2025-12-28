<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'rating' => $this->rating,
            'comment' => $this->comment,
            'ratings_breakdown' => $this->ratings_breakdown,
            'is_verified' => $this->is_verified,
            'helpful_count' => $this->helpful_count,
            'student' => $this->whenLoaded('student', fn() => [
                'id' => $this->student->id,
                'name' => $this->student->name,
                'avatar_url' => $this->student->avatar_url,
            ]),
            'admin_response' => $this->admin_response,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
