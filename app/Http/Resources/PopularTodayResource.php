<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopularTodayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->resource = (object) $this->resource;

        $metaValues = [];
        if (isset($this->resource->meta) && is_array($this->resource->meta)) {
            $metaValues = array_column($this->resource->meta, 'meta_value', 'meta_key');
        }

        return [
            'title' => $this->resource->post_title,
            'slug' => $this->resource->post_name,
            'cover' => $this->resource->cover,
            'badge' => isset($metaValues['ero_hot']) ? "Hot" : null,
        ];
    }
}
