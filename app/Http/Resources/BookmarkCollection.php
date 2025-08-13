<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BookmarkCollection extends ResourceCollection
{
    public $collects = BookmarkResource::class;
    public function toArray(Request $request): array
    {
        return [
            $this->collection
        ];
    }
}
