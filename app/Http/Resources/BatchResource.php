<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'max_students' => $this->max_students,
            'enrolled_students' => $this->enrolled_students,
            'available_slots' => $this->available_slots,
            'start_date' => $this->start_date->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'schedule' => $this->schedule,
            'formatted_schedule' => $this->formatted_schedule,
            'status' => $this->status,
            'price' => $this->effective_price,
            'is_accepting_enrollments' => $this->is_accepting_enrollments,
            'teacher' => $this->whenLoaded('teacher', fn() => [
                'id' => $this->teacher->id,
                'name' => $this->teacher->name,
                'avatar_url' => $this->teacher->avatar_url,
            ]),
        ];
    }
}
