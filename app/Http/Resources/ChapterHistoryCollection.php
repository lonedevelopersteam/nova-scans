<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChapterHistoryCollection extends ResourceCollection
{
    public $collects = ChapterHistoryResource::class;
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection
        ];
    }
}
