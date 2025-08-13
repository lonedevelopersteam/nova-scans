<?php

use App\Http\Controllers\BookmarkSeriesController;
use App\Http\Controllers\ChapterHistoryController;
use App\Http\Controllers\MangaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('api_key')->group(function(){
    Route::prefix('v1/users')->group(function(){
        Route::post("/register-admin", [UserController::class, 'registerAdmin']);
        Route::post("/register-editor", [UserController::class, 'registerEditor']);
        Route::post("/register", [UserController::class, 'register']);
        Route::post("/login-admin-editor", [UserController::class, 'loginAdminEditor']);
        Route::post("/login", [UserController::class, 'login']);
        Route::post("/otp", [UserController::class, 'sendOtp']);
        Route::post("/check-otp", [UserController::class, 'checkOtp']);

        Route::get("/admin-exists", [UserController::class, 'checkAdminUsersExist']);

        Route::put("/check-login", [UserController::class, 'checkLogin']);
        Route::put("/password", [UserController::class, 'updatePw']);

        Route::delete("/logout", [UserController::class, 'logout']);
    });

    Route::prefix('v1/manga')->group(function(){
        Route::get("/popularToday", [MangaController::class, 'popularToday']);
        Route::get("/latest", [MangaController::class, 'latest']);
        Route::get("/projectAll", [MangaController::class, 'projectAll']);
        Route::get("/search", [MangaController::class, 'searchManga']);
        Route::get("/recommendation", [MangaController::class, 'recommendation']);
        Route::get("/heroSlider", [MangaController::class, 'heroSlider']);
        Route::get("/readingPage/{slugChapter}", [MangaController::class, 'readingPage']);
        Route::get("/seriesDetail/{slugSeries}", [MangaController::class, 'seriesDetail']);

        Route::post("/bookmark/", [BookmarkSeriesController::class, 'bookmarkSeries']);
    });
});

Route::middleware('token')->group(function(){
    Route::prefix('v1/users')->group(function(){
        Route::get("/", [UserController::class, 'getUsers']);
        Route::get("/search", [UserController::class, 'searchUsers']);

        Route::delete("/{id}", [UserController::class, 'deleteUser']);
    });

    Route::prefix('v1/manga')->group(function(){
        Route::post("/chapterHistory", [ChapterHistoryController::class, 'chapterHistory']);
        Route::get("/chapterHistory/{slugSeries}", [ChapterHistoryController::class, 'getChapterHistory']);
        Route::post("/bookmark", [BookmarkSeriesController::class, 'bookmarkSeries']);
        Route::get("/bookmark/{userId}", [MangaController::class, 'getBookmark']);
    });
});
