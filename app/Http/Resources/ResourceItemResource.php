<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceItemResource extends JsonResource
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
            'type' => $this->type,
            'url' => $this->url,
            'file_size' => $this->file_size,
            'formatted_size' => $this->formatted_size,
            'is_downloadable' => $this->is_downloadable,
            'download_count' => $this->download_count,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
