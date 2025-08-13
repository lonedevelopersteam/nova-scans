<?php

namespace App\Services;

interface MangaService
{
    public function popularToday(bool $clearCache) :array;
    public function latest(int $limit, bool $clearCache) :array;
    public function projectAll(int $limit, bool $clearCache) :array;
    public function search(?string $title, ?array $genres, bool $clearCache) :array;
    public function recommendation(int $limit) :array;
    public function heroSliders(int $limit, bool $clearCache) :array;
    public function readingPage(string $slugChapter, bool $clearCache) :array;
    public function seriesDetail(string $slugSeries, bool $clearCache) :array;
    public function bookmark(string $userId, bool $clearCache) :array;
}
