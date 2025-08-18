<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookmarkCollection;
use App\Http\Resources\HeroSliderCollection;
use App\Http\Resources\LatestCollection;
use App\Http\Resources\PopularTodayCollection;
use App\Http\Resources\ProjectAllCollection;
use App\Http\Resources\RecommendationCollection;
use App\Http\Resources\SearchResourceCollection;
use App\Http\Resources\SeriesDetailResource;
use App\Services\MangaService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class MangaController extends Controller
{
    private MangaService $mangaService;

    public function __construct(MangaService $service)
    {
        $this->mangaService = $service;
    }

    public function popularToday(Request $request) :void {
        $clearCache = $request->boolean('clear-cache');

        $projects = $this->mangaService->popularToday($clearCache);
        throw new HttpResponseException(response([
            "success" => true,
            "data" => new PopularTodayCollection($projects)
        ], 200));
    }
    public function latest(Request $request) :void {
        $limit = $request->input('limit', 20);
        $clearCache = $request->boolean('clear-cache');

        $projects = $this->mangaService->latest($limit, $clearCache);
        throw new HttpResponseException(response([
            "success" => true,
            "data" => new LatestCollection($projects)
        ], 200));
    }
    public function projectAll(Request $request) :void {
        $limit = $request->input('limit', 20);
        $clearCache = $request->boolean('clear-cache');

        $projects = $this->mangaService->projectAll($limit, $clearCache);
        throw new HttpResponseException(response([
            "success" => true,
            "data" => new ProjectAllCollection($projects)
        ], 200));
    }
    public function searchManga(Request $request) :void {
        $title = $request->input('title');
        $genres = $request->input('genres');
        $clearCache = $request->boolean('clear-cache');

        if (is_string($genres)) {
            $genres = explode(',', $genres);
        }
        if (empty($genres)) {
            $genres = null;
        }

        $projects = $this->mangaService->search($title, $genres, $clearCache);
        throw new HttpResponseException(response([
            "success" => true,
            "data" => new SearchResourceCollection($projects)
        ], 200));
    }
    public function recommendation(Request $request) :void {
        $limit = $request->input('limit', 20);

        $projects = $this->mangaService->recommendation($limit);
        throw new HttpResponseException(response([
            "success" => true,
            "data" => new RecommendationCollection($projects)
        ], 200));
    }
    public function heroSlider(Request $request) :void {
        $limit = $request->input('limit', 5);
        $clearCache = $request->boolean('clear-cache');

        $projects = $this->mangaService->heroSliders($limit, $clearCache);
        throw new HttpResponseException(response([
            "success" => true,
            "data" => new HeroSliderCollection($projects)
        ], 200));
    }
    public function readingPage(string $slugChapter, Request $request) :void {
        $clearCache = $request->boolean('clear-cache');

        $projects = $this->mangaService->readingPage($slugChapter, $clearCache);
        throw new HttpResponseException(response([
            "success" => true,
            "data" => $projects
        ], 200));
    }
    public function seriesDetail(string $slugSeries, Request $request) :void {
        $clearCache = $request->boolean('clear-cache');

        $projects = $this->mangaService->seriesDetail($slugSeries, $clearCache);
        throw new HttpResponseException(response([
            "success" => true,
            "data" => new SeriesDetailResource($projects)
        ], 200));
    }
    public function getBookmark(string $userId, Request $request) :void {
        $clearCache = $request->boolean('clear-cache');

        $projects = $this->mangaService->bookmark($userId, $clearCache);
        throw new HttpResponseException(response([
            "success" => true,
            "data" => new BookmarkCollection($projects)
        ], 200));
    }

    public function clearCache() :void {
        $result = $this->mangaService->clearCache();
         throw new HttpResponseException(response([
            "success" => true,
            "data" => ($result) ? "Success clear manga" : "Failed to clear manga",
        ], 200));
    }
}
