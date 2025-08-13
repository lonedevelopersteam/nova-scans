<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBookmarkRequest;
use App\Models\BookmarkSeries;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookmarkSeriesController extends Controller
{
    public function bookmarkSeries(AddBookmarkRequest $request): void
    {
        $data = $request->validated();

        $bookmark = BookmarkSeries::where('user_id', $data['user_id'])
            ->where('slug_series', $data['slug_series'])
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            throw new HttpResponseException(response([
                "success" => true,
                "message" => "Series bookmark removed."
            ], 200));
        } else {
            $newBookmark = new BookmarkSeries($data);
            $newBookmark->save();
            throw new HttpResponseException(response([
                "success" => true,
                "message" => "Series successfully bookmarked.",
            ], 201));
        }
    }
}
