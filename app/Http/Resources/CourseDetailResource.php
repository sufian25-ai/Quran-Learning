<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseDetailResource extends JsonResource
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
            'description' => $this->description,
            'syllabus' => $this->syllabus,
            'thumbnail' => $this->thumbnail_url,
            'video_preview' => $this->video_preview,
            'level' => $this->level,
            'level_label' => $this->getLevelLabel(),
            'category' => $this->category,
            'category_label' => $this->getCategoryLabel(),
            'languages' => $this->languages,
            'duration_weeks' => $this->duration_weeks,
            'classes_per_week' => $this->classes_per_week,
            'class_duration_minutes' => $this->class_duration_minutes,
            'max_students_per_batch' => $this->max_students_per_batch,
            'requirements' => $this->requirements,
            'learning_outcomes' => $this->learning_outcomes,
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
            'batches' => BatchResource::collection($this->whenLoaded('batches')),
            'reviews' => ReviewResource::collection($this->whenLoaded('visibleReviews')),
            'resources' => ResourceItemResource::collection($this->whenLoaded('publicResources')),
            'created_at' => $this->created_at->toISOString(),
        ];
    }

    private function getLevelLabel(): string
    {
        return match ($this->level) {
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            'all_levels' => 'All Levels',
            default => $this->level,
        };
    }

    private function getCategoryLabel(): string
    {
        return match ($this->category) {
            'quran_reading' => 'Quran Reading',
            'tajweed' => 'Tajweed',
            'hifz' => 'Hifz (Memorization)',
            'arabic' => 'Arabic Language',
            'islamic_studies' => 'Islamic Studies',
            default => $this->category,
        };
    }
}
