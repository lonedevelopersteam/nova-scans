<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MangaResource extends JsonResource
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
            // Data utama
            'id' => $this->resource->ID,
            'created' => $this->resource->post_date,
            'content' => $this->resource->post_content,
            'title' => $this->resource->post_title,
            'status' => $this->resource->post_status,
            'slug' => $this->resource->post_name,

            // Data dari meta (sejajar dengan field utama)
            'cover' => $this->resource->cover,
            'isHot' => isset($metaValues['ero_hot']) ?? false,
            'type' => $metaValues['ero_type'] ?? null,
            'mangaStatus' => $metaValues['ero_status'] ?? null,
            'author' => $metaValues['ero_author'] ?? null,
            'artist' => $metaValues['ero_artist'] ?? null,
            'published' => $metaValues['ero_published'] ?? null,
            'serialization' => $metaValues['ero_serialization'] ?? null,
            'score' => $metaValues['ero_score'] ?? null,
            'japanese' => $metaValues['ero_japanese'] ?? null,
            'image' => $metaValues['ero_image'] ?? null,
            'isProject' => isset($metaValues['ero_project']) ?? false,
            'iddb' => $metaValues['iddb'] ?? null,
            'todayViewCount' => $metaValues['ts_today_view_count'] ?? 0,
            'weeklyViewCount' => $metaValues['ts_weekly_view_count'] ?? 0,
            'monthlyViewCount' => $metaValues['ts_monthly_view_count'] ?? 0,
            'totalViewCount' => $metaValues['wpb_post_views_count'] ?? 0,
        ];
    }
}
