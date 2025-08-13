<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeriesDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->resource = (object) $this->resource;

        $metaValues = [];
        if (isset($this->resource->meta) && is_array($this->resource->meta)) {
            $metaValues = array_column($this->resource->meta, 'meta_value', 'meta_key');
        }

        return [
            'title' => $this->resource->post_title,
            'cover' => $this->resource->cover,
            'sinopsis' => $this->resource->post_content,
            'slug' => $this->resource->slug,
            'badge' => isset($metaValues['ero_hot']) ? "Hot" : null,
            'rating' => $metaValues['ero_score'] ??  0,
            'status' => $metaValues['ero_status'] ?? null,
            'serialization' => $metaValues['ero_serialization'] ?? null,
            'genre' => $this->resource->genres,
            'chapters' => $this->resource->chapters
        ];
    }
}
