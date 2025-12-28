<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'thumbnail' => $this->thumbnail_url,
            'level' => $this->level,
            'category' => $this->category,
            'duration_weeks' => $this->duration_weeks,
            'classes_per_week' => $this->classes_per_week,
            'class_duration_minutes' => $this->class_duration_minutes,
            'pricing' => [
                'group' => (float) $this->price_group,
                'private' => (float) $this->price_private,
                'formatted_group' => $this->formatted_price_group,
                'formatted_private' => $this->formatted_price_private,
            ],
            'rating' => [
                'average' => (float) $this->average_rating,
                'count' => $this->reviews_count,
            ],
            'total_enrollments' => $this->total_enrollments,
            'is_featured' => $this->is_featured,
            'available_batches_count' => $this->whenLoaded('batches', fn() => $this->batches->count()),
        ];
    }
}
