<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MangaMetaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $metaValues = [];
        if (is_array($this->resource)) {
            $metaValues = array_column($this->resource, 'meta_value', 'meta_key');
        }

        return [
            'cover' => $metaValues['ero_cover'] ?? null,
            'isHot' => isset($metaValues['ero_hot']) ?? false,
            'type' => $metaValues['ero_type'] ?? null,
            'status' => $metaValues['ero_status'] ?? null,
            'author' => $metaValues['ero_author'] ?? null,
            'artist' => $metaValues['ero_artist'] ?? null,
            'published' => $metaValues['ero_published'] ?? null,
            'serialization' => $metaValues['ero_serialization'] ?? null,
            'score' => $metaValues['ero_score'] ?? null,
            'japanese' => $metaValues['ero_japanese'] ?? null,
            'image' => $metaValues['ero_image'] ?? null,
            'project' => isset($metaValues['ero_project']) ?? false,
            'iddb' => $metaValues['iddb'] ?? null,
            'viewCount' => [
                'today' => $metaValues['ts_today_view_count'] ?? 0,
                'weekly' => $metaValues['ts_weekly_view_count'] ?? 0,
                'monthly' => $metaValues['ts_monthly_view_count'] ?? 0,
                'total' => $metaValues['wpb_post_views_count'] ?? 0,
            ],
        ];
    }
}
