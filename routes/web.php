<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\CommentReactionController;

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

        Route::post('/{post}/reaction', [PostReactionController::class, 'reaction'])->name('reaction');

        Route::post('/{post}/comment', [PostCommentController::class, 'store'])->name('comment.store');
        Route::put('/comment/{comment}', [PostCommentController::class, 'update'])->name('comment.update');
        Route::delete('/comment/{comment}', [PostCommentController::class, 'destroy'])->name('comment.destroy');
        Route::get('/comment/download-attachment/{attachment}', [PostCommentController::class, 'downloadAttachment'])->name('comment.download-attachment');
    });

    Route::prefix('comment')->as('comment.')->group(function () {
        Route::post('/{comment}/reaction', [CommentReactionController::class, 'reaction'])->name('reaction');
    });
});

require __DIR__ . '/auth.php';
