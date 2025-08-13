<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterHistoryResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $this->resource = (object) $this->resource;

        return [
          "chapter_slug" => $this->resource->slug_chapter
        ];
    }
}
