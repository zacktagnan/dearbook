<?php

use App\Http\Controllers\ArchiveManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\CommentReactionController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckGroupMembership;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/u/{user:username}', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/u/{user:username}/posts/{id}', [PostController::class, 'show'])->name('post.show')
        ->middleware(CheckGroupMembership::class);
    Route::get('/g/{group:slug}/{tabIndex?}', [GroupController::class, 'profile'])->name('group.profile');
});

Route::middleware('auth')->group(function () {
    Route::prefix('profile')->as('profile.')->group(function () {
        // Route::post('/update-images', [ProfileController::class, 'updateImages'])->name('update-images');
        Route::post('/update-cover-image', [ProfileController::class, 'updateCoverImage'])->name('update-cover-image');
        Route::post('/update-avatar-image', [ProfileController::class, 'updateAvatarImage'])->name('update-avatar-image');
        // Route::get('', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('', [ProfileController::class, 'update'])->name('update');
        Route::delete('', [ProfileController::class, 'destroy'])->name('destroy');
        Route::get('/media/download-attachment/{attachment}', [ProfileController::class, 'downloadAttachment'])->name('media.download-attachment');
    });

    Route::prefix('post')->as('post.')->group(function () {
        Route::post('', [PostController::class, 'store'])->name('store');
        Route::put('/{post}', [PostController::class, 'update'])->name('update');
        Route::delete('/{entity}/{to}', [PostController::class, 'destroy'])->name('destroy');
        Route::get('/destroy-from-management/{id}/{from}', [PostController::class, 'destroyFromManagement'])->name('destroy-from-management');
        Route::post('/destroy-from-management-all-selected', [PostController::class, 'destroyFromManagementAllSelected'])->name('destroy-from-management-all-selected');
        Route::get('/restore/{id}/{from}', [PostController::class, 'restore'])->name('restore');
        Route::post('/restore-all-selected', [PostController::class, 'restoreAllSelected'])->name('restore-all-selected');
        Route::get('/force-destroy/{id}/{from}', [PostController::class, 'forceDestroy'])->name('force-destroy');
        Route::get('/force-destroy-from-detail/{id}', [PostController::class, 'forceDestroyFromDetail'])->name('force-destroy-from-detail');
        Route::post('/force-destroy-all-selected', [PostController::class, 'forceDestroyAllSelected'])->name('force-destroy-all-selected');
        Route::get('/archive/{id}/{from}', [PostController::class, 'archive'])->name('archive');
        Route::post('/archive-all-selected', [PostController::class, 'archiveAllSelected'])->name('archive-all-selected');

        Route::get('/download-attachment/{attachment}', [PostController::class, 'downloadAttachment'])->name('download-attachment');
        Route::post('/generate-content', [PostController::class, 'generateContent'])->name('generate-content');
        Route::post('/fetch-url-preview', [PostController::class, 'fetchUrlPreview'])->name('fetch-url-preview');

        Route::post('/{post}/pin-unpin', [PostController::class, 'pinUnpin'])->name('pin-unpin');
        Route::post('/get-pinned', [PostController::class, 'getPinned'])->name('get-pinned');

        Route::post('/{post}/reaction', [PostReactionController::class, 'reaction'])->name('reaction');

        Route::post('/{post}/comment', [PostCommentController::class, 'store'])->name('comment.store');
        Route::put('/comment/{comment}', [PostCommentController::class, 'update'])->name('comment.update');
        Route::delete('/comment/{entity}/{to}', [PostCommentController::class, 'destroy'])->name('comment.destroy');
        Route::get('/comment/download-attachment/{attachment}', [PostCommentController::class, 'downloadAttachment'])->name('comment.download-attachment');
    });

    Route::prefix('comment')->as('comment.')->group(function () {
        Route::post('/{comment}/reaction', [CommentReactionController::class, 'reaction'])->name('reaction');
    });

    Route::prefix('archive-management')->as('archive-management.')->group(function () {
        Route::get('/', [ArchiveManagementController::class, 'index'])->name('index');
        Route::get('/activity-log-posts', [ArchiveManagementController::class, 'activityLogPosts'])->name('activity-log-posts');
        Route::get('/archived-posts', [ArchiveManagementController::class, 'archivedPosts'])->name('archived-posts');
        Route::get('/trashed-posts', [ArchiveManagementController::class, 'trashedPosts'])->name('trashed-posts');

        Route::get('/trashed-groups', [ArchiveManagementController::class, 'trashedGroups'])->name('trashed-groups');

        Route::get('/notify-process-ending/{processType}', [ArchiveManagementController::class, 'notifyProcessEnding'])->name('notify.process.ending');
    });

    Route::prefix('group')->as('group.')->group(function () {
        Route::post('', [GroupController::class, 'store'])->name('store');
        Route::post('/update-cover-image', [GroupController::class, 'updateCoverImage'])->name('update-cover-image');
        Route::post('/update-thumbnail-image', [GroupController::class, 'updateThumbnailImage'])->name('update-thumbnail-image');
        Route::patch('{group}', [GroupController::class, 'update'])->name('update');
        Route::delete('{group}', [GroupController::class, 'destroy'])->name('destroy');

        Route::get('/restore/{id}/{from}', [GroupController::class, 'restore'])->name('restore');
        Route::post('/restore-all-selected', [GroupController::class, 'restoreAllSelected'])->name('restore-all-selected');
        // Route::get('/force-destroy/{id}/{from}', [GroupController::class, 'forceDestroy'])->name('force-destroy');
        // Route::post('/force-destroy-all-selected', [GroupController::class, 'forceDestroyAllSelected'])->name('force-destroy-all-selected');
        // Route::get('/archive/{id}/{from}', [GroupController::class, 'archive'])->name('archive');
        // Route::post('/archive-all-selected', [GroupController::class, 'archiveAllSelected'])->name('archive-all-selected');

        Route::post('/invite-users/{group:slug}', [GroupController::class, 'inviteUsers'])->name('invite-users');
        Route::get('/accept-invitation/{token}', [GroupController::class, 'acceptInvitation'])->name('accept-invitation');
        Route::post('/join/{group:slug}', [GroupController::class, 'join'])->name('join');
        Route::post('/request-join/{group:slug}', [GroupController::class, 'requestJoin'])->name('request-join');
        Route::post('/request-approve-or-not/{group:slug}', [GroupController::class, 'requestApproveOrNot'])->name('request-approve-or-not');
        Route::post('/change-role/{group:slug}', [GroupController::class, 'changeRole'])->name('change-role');
        Route::delete('/remove-member/{group:slug}', [GroupController::class, 'removeMember'])->name('remove-member');
    });

    Route::prefix('user')->as('user.')->group(function () {
        Route::post('/follow-unfollow/{user}', [UserController::class, 'followUnfollow'])->name('follow-unfollow');
    });

    Route::get('global-search/{keywords?}', [SearchController::class, 'search'])->name('global-search');
});

require __DIR__ . '/auth.php';
