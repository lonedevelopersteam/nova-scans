<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkResource extends JsonResource
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
            'slug' => $this->resource->slug,
            'badge' => isset($metaValues['ero_hot']) ? "Hot" : null,
            'rating' => isset($metaValues['ero_score']) ?? null,
            'chapters' => $this->resource->chapters
        ];
    }
}
