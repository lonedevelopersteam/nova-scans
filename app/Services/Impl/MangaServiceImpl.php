<?php

namespace App\Services\Impl;

use App\Models\BookmarkSeries;
use App\Models\WpPostMeta;
use App\Models\WpPosts;
use App\Models\WpTermRelationships;
use App\Models\WpTerms;
use App\Services\MangaService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class MangaServiceImpl implements MangaService
{
    public function popularToday(bool $clearCache): array
    {
        $limit = 10;
        $cacheKey = "popular:today:limit:$limit";

        if ($clearCache) {
            $this->forgetCacheValue($cacheKey);
            $this->clearCoverCaches();

            $freshData = $this->fetchPopularToday($limit, true);
            $this->setCacheValue($cacheKey, $freshData);

            return $freshData;
        }

        return $this->getCacheValue($cacheKey, function () use ($limit) {
            return $this->fetchPopularToday($limit, false);
        });
    }
    public function latest(int $limit = 20, bool $clearCache = false): array
    {
        $cacheKey = "latest:manga:limit:{$limit}";

        if ($clearCache) {
            $this->forgetCacheValue($cacheKey);
            $this->clearCoverCaches();

            $freshData = $this->fetchLatestManga($limit, $clearCache);
            $this->setCacheValue($cacheKey, $freshData);

            return $freshData;
        }

        return $this->getCacheValue($cacheKey, function () use ($limit) {
            return $this->fetchLatestManga($limit, false);
        });
    }
    public function projectAll(int $limit = 20, bool $clearCache = false): array
    {
        $cacheKey = "project:all:limit:{$limit}";

        if ($clearCache) {
            $this->forgetCacheValue($cacheKey);
            $this->clearCoverCaches();

            $freshData = $this->fetchProjectAll($limit, $clearCache);
            $this->setCacheValue($cacheKey, $freshData);

            return $freshData;
        }

        return $this->getCacheValue($cacheKey, function () use ($limit) {
            return $this->fetchProjectAll($limit, false);
        });
    }
    public function search(?string $title, ?array $genres, bool $clearCache = false): array
    {
        if (is_array($genres) && !empty($genres)) {
            sort($genres);
            $genresString = implode(',', $genres);
        } else {
            $genresString = 'all';
        }

        $cacheKey = "search:title:" . ($title ?? 'all') . ":genres:" . $genresString;

        if ($clearCache) {
            $this->forgetCacheValue($cacheKey);
            $this->clearCoverCaches();

            $freshData = $this->fetchSearch($title, $genres);
            $this->setCacheValue($cacheKey, $freshData);

            return $freshData;
        }

        return $this->getCacheValue($cacheKey, function () use ($title, $genres) {
            return $this->fetchSearch($title, $genres);
        });
    }
    public function recommendation(int $limit): array
    {
        return $this->fetchRecommendation($limit);
    }
    public function heroSliders(int $limit = 5, bool $clearCache = false): array
    {
        $cacheKey = "hero:sliders:limit:{$limit}";

        if ($clearCache) {
            $this->forgetCacheValue($cacheKey);
            $this->clearCoverCaches();

            $freshData = $this->fetchHeroSliders($limit);
            $this->setCacheValue($cacheKey, $freshData, 604800);

            return $freshData;
        }

        return $this->getCacheValue($cacheKey, function () use ($limit) {
            return $this->fetchHeroSliders($limit);
        });
    }
    public function readingPage(string $slugChapter, bool $clearCache): array
    {
        $cacheKey = "reading:page:$slugChapter";

        if ($clearCache) {
            $this->forgetCacheValue($cacheKey);
            $this->clearCoverCaches();

            $freshData = $this->fetchReadingPage($slugChapter);
            $this->setCacheValue($cacheKey, $freshData);

            return $freshData;
        }

        return $this->getCacheValue($cacheKey, function () use ($slugChapter) {
            return $this->fetchReadingPage($slugChapter);
        });
    }
    public function seriesDetail(string $slugSeries, bool $clearCache): array
    {
        $cacheKey = "slug:series:$slugSeries";

        if ($clearCache) {
            $this->forgetCacheValue($cacheKey);
            $this->clearCoverCaches();

            $freshData = $this->fetchSeriesDetail($slugSeries, true);
            $this->setCacheValue($cacheKey, $freshData);

            return $freshData;
        }

        return $this->getCacheValue($cacheKey, function () use ($slugSeries) {
            return $this->fetchSeriesDetail($slugSeries, false);
        });
    }
    public function bookmark(string $userId, bool $clearCache): array
    {
        return $this->fetchBookmark($userId, $clearCache);
    }
    public function clearCache(): bool
    {
        try {
            Log::info("Attempting to clear all data from Redis...");

            // Jalankan perintah FLUSHALL untuk menghapus semua keys
            Redis::flushall();

            Log::info("All Redis data has been cleared successfully.");
            return true;
        } catch (Exception $e) {
            // Tangkap error jika Redis tidak bisa dijangkau atau perintah gagal
            Log::error("Failed to clear all Redis data: " . $e->getMessage());
            return false;
        }
    }

    // Helper Function
    private function fetchPopularToday(int $limit, bool $clearChildCache): array
    {
        $posts = WpPosts::where('post_type', 'manga')
            ->where('post_status', 'publish')
            ->with('meta')
            ->join('wp_postmeta', 'wp_posts.ID', '=', 'wp_postmeta.post_id')
            ->where('wp_postmeta.meta_key', 'wpb_post_views_count')
            ->orderBy('wp_postmeta.meta_value', 'desc')
            ->limit($limit)
            ->get();

        return $posts->map(function ($post) use ($clearChildCache) {
            $coverUrl = $this->getCover($post->ID, $clearChildCache);
            $post['cover'] = $coverUrl;
            $post['chapters'] = $this->getChaptersBySlug($post->post_name, $clearChildCache, 3);
            return $post;
        })->toArray();
    }
    private function fetchLatestManga(int $limit, bool $clearChildCache): array
    {
        $posts = WpPosts::where('post_type', 'manga')
            ->where('post_status', 'publish')
            ->with('meta')
            ->orderBy('post_modified', 'desc')
            ->limit($limit)
            ->get();

        return $posts->map(function ($post) use ($clearChildCache) {
            $coverUrl = $this->getCover($post->ID, $clearChildCache);
            $post['cover'] = $coverUrl;
            $post['chapters'] = $this->getChaptersBySlug($post->post_name, $clearChildCache, 3);
            return $post;
        })->toArray();
    }
    private function fetchProjectAll(int $limit, bool $clearChildCache): array
    {
        $posts = WpPosts::where('post_type', 'manga')
            ->where('post_status', 'publish')
            ->with('meta')
            ->whereHas('meta', function ($query) {
                $query->where('meta_key', 'ero_project')
                    ->where('meta_value', 1);
            })
            ->orderBy('post_modified', 'desc')
            ->limit($limit)
            ->get();

        return $posts->map(function ($post) use ($clearChildCache) {
            $coverUrl = $this->getCover($post->ID, $clearChildCache);
            $post['cover'] = $coverUrl;
            $post['chapters'] = $this->getChaptersBySlug($post->post_name, $clearChildCache, 3);
            return $post;
        })->toArray();
    }
    private function fetchSearch(?string $title, ?array $genres): array
    {
        $query = WpPosts::where('post_type', 'manga')
            ->where('post_type', 'manga')
                ->where('post_status', 'publish')
            ->with('meta');

        if ($title) {
            $query->where('post_title', 'like', '%' . $title . '%');
        }

        if (!empty($genres)) {
            $query->whereHas('genres', function ($q) use ($genres) {
                $q->whereIn('name', $genres);
            });
        }

        $posts = $query->get();

        return $posts->map(function ($post) {
            $coverUrl = $this->getCover($post->ID, false);
            $post['cover'] = $coverUrl;
            $post['chapters'] = $this->getChaptersBySlug($post->post_name, false, 3);
            return $post;
        })->toArray();
    }
    private function fetchRecommendation(int $limit): array
    {
        $posts = WpPosts::where('post_type', 'manga')
            ->where('post_status', 'publish')
            ->with('meta')
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        return $posts->map(function ($post) {
            $coverUrl = $this->getCover($post->ID, false);
            $post['cover'] = $coverUrl;
            $post['chapters'] = $this->getChaptersBySlug($post->post_name, true, 1);
            return $post;
        })->toArray();
    }
    private function fetchHeroSliders(int $limit): array
    {
        $topCount = 10;

        $posts = WpPosts::where('post_type', 'manga')
            ->where('post_status', 'publish')
            ->with('meta')
            ->with('genres')
            ->join('wp_postmeta', 'wp_posts.ID', '=', 'wp_postmeta.post_id')
            ->where('wp_postmeta.meta_key', 'ts_monthly_view_count')
            ->orderByRaw('CAST(wp_postmeta.meta_value AS UNSIGNED) DESC')
            ->limit($topCount)
            ->get();

        $posts = $posts->shuffle()->take($limit);

        return $posts->map(function ($post) {
            $coverUrl = $this->getCover($post->ID, false);
            $post['cover'] = $coverUrl;
            $post['chapters'] = $this->getChaptersBySlug($post->post_name, false, 3);
            $post['genres'] = $post->genres->pluck('name')->implode(', ');
            return $post;
        })->toArray();
    }
    private function fetchReadingPage(string $slug): ?array
    {
        $post = WpPosts::where('post_name', $slug)
            ->with('meta')
            ->first();

        if (!$post) {
            return null;
        }

        $chapterMeta = $post->meta->firstWhere('meta_key', 'ero_chapter');

        $termRelationship = WpTermRelationships::where('object_id', $post->ID)
            ->with('termTaxonomy.term')
            ->first();

        $slugSeries = null;
        if ($termRelationship && $termRelationship->termTaxonomy && $termRelationship->termTaxonomy->term) {
            $slugSeries = $termRelationship->termTaxonomy->term->slug;
        }

        $otherChapters = $slugSeries ? $this->fetchChaptersBySlugData($slugSeries, null) : [];

        return [
            'chapters' => $post->post_content,
            'chapterNum' => $chapterMeta ? $chapterMeta->meta_value : null,
            'slugSeries' => $slugSeries,
            'time' => $post->post_date,
            'otherChapters' => $otherChapters,
        ];
    }
    private function fetchSeriesDetail(string $slugSeries, bool $clearCache): array
    {
        $post = WpPosts::where('post_name', $slugSeries)
            ->where('post_type',  'manga')
            ->with('meta')
            ->with('genres')
            ->first();

        if (!$post) {
            return [];
        }

        $coverUrl = $this->getCover($post->ID, $clearCache);
        $chapters = $this->getChaptersBySlug($post->post_name, $clearCache, null);

        return [
            'ID' => $post->ID,
            'post_title' => $post->post_title,
            'post_content' => $post->post_content,
            'slug' => $post->post_name,
            'cover' => $coverUrl,
            'genres' => $post->genres->pluck('name')->implode(', '),
            'chapters' => $chapters,
            'meta' => $post->meta->toArray(),
        ];
    }
    private function fetchBookmark(string $userId, bool $clearCache): array
    {
        $bookmarks = BookmarkSeries::where('user_id', $userId)->get();
        $series = [];

        if (count($bookmarks) > 0) {
            foreach ($bookmarks as $bookmark) {
                $series[] = $this->fetchSeriesDetail($bookmark->slug_series, $clearCache);
            }
        }

        return $series;
    }
    private function getChaptersBySlug(string $slug, bool $clearCache, ?int $limit): array
    {
        $cacheKey = "chapters:slug:{$slug}";

        if ($clearCache) {
            $this->forgetCacheValue($cacheKey);

            $freshChapters = $this->fetchChaptersBySlugData($slug, $limit);
            $this->setCacheValue($cacheKey, $freshChapters);

            return $freshChapters;
        }

        return $this->getCacheValue($cacheKey, function () use ($slug, $limit) {
            return $this->fetchChaptersBySlugData($slug, $limit);
        });
    }
    private function fetchChaptersBySlugData(string $slug, ?int $limit = null): array
    {
        $term = WpTerms::where('slug', $slug)->first();

        if (!$term) {
            return [];
        }

        // Ambil semua data terlebih dahulu tanpa ordering
        $termRelationships = WpTermRelationships::whereHas('termTaxonomy', function ($query) use ($term) {
            $query->where('term_id', $term->term_id);
        })->get();

        // Map ke array chapters dengan chapterNum
        $chapters = $termRelationships->map(function ($relationship) {
            return $this->getSingleChapterById($relationship->object_id);
        })->filter()->values();

        // Sort berdasarkan chapterNum dari terbesar ke terkecil
        $sortedChapters = $chapters->sortByDesc(function ($chapter) {
            return (int) $chapter['chapterNum']; // Cast ke integer untuk sorting numerik
        })->values();

        // Terapkan limit jika ada
        if ($limit > 0) {
            $sortedChapters = $sortedChapters->take($limit);
        }

        return $sortedChapters->toArray();
    }
    private function getSingleChapterById(int $id, bool $showChapter = false): ?array
    {
        $post = WpPosts::where('id', $id)
            ->with('meta')
            ->first();

        if (!$post) {
            return null;
        }

        $chapterMeta = $post->meta->firstWhere('meta_key', 'ero_chapter');

        $result = [
            'chapterNum' => $chapterMeta ? $chapterMeta->meta_value : null,
            'time' => $post->post_date,
            'slug' => $post->post_name,
        ];

        if ($showChapter) {
            $result['chapters'] = $post->post_content;
        }

        return $result;
    }
    private function getCover(int $idPostParent, bool $clearCache): ?string
    {
        $cacheKey = "cover:{$idPostParent}";

        if ($clearCache) {
            $this->forgetCacheValue($cacheKey);

            $freshCover = $this->fetchCoverData($idPostParent);
            $this->setCacheValue($cacheKey, $freshCover);

            return $freshCover;
        }

        return $this->getCacheValue($cacheKey, function () use ($idPostParent) {
            return $this->fetchCoverData($idPostParent);
        });
    }
    private function fetchCoverData(int $idPostParent): ?string
    {
        $post = WpPosts::where('post_parent', $idPostParent)
            ->first();

        if ($post) {
            return $post->guid;
        }

        $meta = WpPostMeta::where('post_id', $idPostParent)
            ->where('meta_key', 'ero_image')
            ->first();

        return $meta ? $meta->meta_value : null;
    }
    private function isRedisAvailable(): bool
    {
        try {
            $redis = Redis::connection();

            $redis->ping();

            return true;
        } catch (Exception $e) {
            Log::warning('Redis connection failed: ' . $e->getMessage());
            return false;
        }
    }
    private function getCacheValue(string $key, callable $callback)
    {
        try {
            return Cache::store('redis')->remember($key, 3600, $callback); // 1 hour = 3600 seconds
        } catch (Exception $e) {
            Log::warning("Redis cache operation failed for key {$key}: " . $e->getMessage());
            return $callback();
        }
    }
    private function setCacheValue(string $key, $value, int $duration = 3600): void
    {
        if (!$this->isRedisAvailable()) {
            return;
        }

        try {
            Cache::store('redis')->put($key, $value, $duration);
        } catch (Exception $e) {
            Log::warning("Failed to set cache for key {$key}: " . $e->getMessage());
        }
    }
    private function forgetCacheValue(string $key): void
    {
        if (!$this->isRedisAvailable()) {
            return;
        }

        try {
            Cache::store('redis')->forget($key);
        } catch (Exception $e) {
            Log::warning("Failed to forget cache for key {$key}: " . $e->getMessage());
        }
    }
    private function clearCoverCaches(): void
    {
        if (!$this->isRedisAvailable()) {
            Log::info('Redis not available, skipping cover cache clear');
            return;
        }

        try {
            $keys = Redis::keys('cover:*');

            if (!empty($keys)) {
                Redis::del($keys);
            }
        } catch (Exception $e) {
            Log::warning('Failed to clear cover caches: ' . $e->getMessage());
        }
    }

}
