<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Pet API Resource
 *
 * Transforms Pet model data into a consistent JSON structure for API responses.
 */
class PetResource extends JsonResource
{
    /**
     * Transform the resource into an array
     *
     * @param  Request  $request  The HTTP request instance
     * @return array<string, mixed> Formatted pet data
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'photo_urls' => $this->photo_urls ?? [],
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
