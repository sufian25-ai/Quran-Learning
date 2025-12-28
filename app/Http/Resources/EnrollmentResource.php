<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
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
            'type' => $this->type,
            'status' => $this->status,
            'progress_percentage' => $this->progress_percentage,
            'classes_attended' => $this->classes_attended,
            'classes_total' => $this->classes_total,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'formatted_amount' => $this->formatted_amount,
            'billing_cycle' => $this->billing_cycle,
            'start_date' => $this->start_date->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'next_billing_date' => $this->next_billing_date?->toDateString(),
            'course' => $this->whenLoaded('course', fn() => [
                'id' => $this->course->id,
                'title' => $this->course->title,
                'slug' => $this->course->slug,
                'thumbnail' => $this->course->thumbnail_url,
                'level' => $this->course->level,
                'category' => $this->course->category,
            ]),
            'batch' => $this->whenLoaded('batch', fn() => $this->batch ? [
                'id' => $this->batch->id,
                'name' => $this->batch->name,
                'schedule' => $this->batch->schedule,
                'formatted_schedule' => $this->batch->formatted_schedule,
                'teacher' => $this->batch->teacher ? [
                    'id' => $this->batch->teacher->id,
                    'name' => $this->batch->teacher->name,
                    'avatar_url' => $this->batch->teacher->avatar_url,
                ] : null,
            ] : null),
            'certificate' => $this->whenLoaded('certificate', fn() => $this->certificate ? [
                'id' => $this->certificate->id,
                'certificate_number' => $this->certificate->certificate_number,
                'issued_at' => $this->certificate->issued_at->toDateString(),
                'pdf_url' => $this->certificate->pdf_url,
            ] : null),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
