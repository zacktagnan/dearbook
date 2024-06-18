<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/u/{user:username}', [ProfileController::class, 'index'])->name('profile.index');
});

Route::middleware('auth')->group(function () {
    Route::prefix('profile')->as('profile.')->group(function () {
        // Route::post('/update-images', [ProfileController::class, 'updateImages'])->name('update-images');
        Route::post('/update-cover-image', [ProfileController::class, 'updateCoverImage'])->name('update-cover-image');
        Route::post('/update-avatar-image', [ProfileController::class, 'updateAvatarImage'])->name('update-avatar-image');
        // Route::get('', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('', [ProfileController::class, 'update'])->name('update');
        Route::delete('', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('post')->as('post.')->group(function () {
        Route::post('', [PostController::class, 'store'])->name('store');
        Route::put('/{post}', [PostController::class, 'update'])->name('update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
        Route::get('/download-attachment/{attachment}', [PostController::class, 'downloadAttachment'])->name('download-attachment');
        Route::post('/{post}/reaction', [PostController::class, 'reaction'])->name('reaction');

        Route::get('/{post}/all-reactions-users', [PostController::class, 'allReactionsUsers'])->name('all-reactions-users');
        Route::get('/{post}/type-reactions-users/{type}', [PostController::class, 'typeReactionsUsers'])->name('type-reactions-users');
    });
});

require __DIR__ . '/auth.php';
