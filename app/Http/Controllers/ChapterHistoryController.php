<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddChapterHistoryRequest;
use App\Http\Resources\ChapterHistoryCollection;
use App\Models\ChapterHistory;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class ChapterHistoryController extends Controller
{
    public function chapterHistory(AddChapterHistoryRequest $request): void
    {
        $validatedData = $request->validated();
        $items = $validatedData['data'];

        $userId = $items[0]['user_id'];

        $existingHistories = ChapterHistory::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        $existingItems = $existingHistories->map(function ($history) {
            return "$history->slug_series|$history->slug_chapter";
        })->toArray();

        $newHistoryRecords = [];
        $filteredNewItems = [];

        foreach ($items as $item) {
            $itemKey = "{$item['slug_series']}|{$item['slug_chapter']}";
            if (!in_array($itemKey, $existingItems) && !in_array($itemKey, $filteredNewItems)) {
                $filteredNewItems[] = $itemKey;
                $newHistoryRecords[] = [
                    'user_id' => $item['user_id'],
                    'slug_series' => $item['slug_series'],
                    'slug_chapter' => $item['slug_chapter'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $combinedHistories = $existingHistories->toArray();
        foreach ($newHistoryRecords as $record) {
            $combinedHistories[] = (object) $record;
        }

        if (count($combinedHistories) > 3) {
            $toDeleteCount = count($combinedHistories) - 3;
            $historiesToDelete = array_slice($combinedHistories, 0, $toDeleteCount);

            $idsToDelete = collect($historiesToDelete)->pluck('id')->filter()->toArray();
            if (!empty($idsToDelete)) {
                ChapterHistory::destroy($idsToDelete);
            }
        }

        if (!empty($newHistoryRecords)) {
            ChapterHistory::insert($newHistoryRecords);
        }

        throw new HttpResponseException(response([
            "success" => true,
            "message" => "Chapter history updated successfully.",
        ], 200));
    }
    public function getChapterHistory(string $slugSeries, Request $request): void {
        $userId = $request->input('user_id');

        $chapters = ChapterHistory::where('user_id', $userId)
            ->where('slug_series', $slugSeries)
            ->orderBy('created_at', 'desc')
            ->get();

        if (!$chapters) {
            throw new HttpResponseException(response([
                "success" => true,
                "message" => "Chapter history not found.",
            ], 404));
        }

        throw new HttpResponseException(response([
            "success" => true,
            "message" => "Successfully fetched chapter history.",
            "data" => new ChapterHistoryCollection($chapters),
        ], 200));
    }
}
