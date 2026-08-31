<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RecentlyViewedController;
use App\Http\Controllers\DashboardController;

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/store', [ItemController::class, 'store'])->name('items.store');
Route::match(['post', 'put'], '/update/{item}', [ItemController::class, 'update'])->name('items.update');
Route::delete('/delete/{item}', [ItemController::class, 'destroy'])->name('items.delete');
Route::post('/bulk-delete', [ItemController::class, 'bulkDelete'])->name('items.bulk-delete');
Route::post('/restore/{id}', [ItemController::class, 'restore'])->name('items.restore');
Route::delete('/force-delete/{id}', [ItemController::class, 'forceDelete'])->name('items.force-delete');
Route::post('/duplicate/{id}', [ItemController::class, 'duplicate'])->name('items.duplicate');
Route::post('/import-csv', [ItemController::class, 'importCsv'])->name('items.import-csv');
Route::get('/export-csv', [ItemController::class, 'exportCsv'])->name('items.export-csv');
Route::get('/show/{id}', [ItemController::class, 'show'])->name('items.show');
Route::delete('/image/{id}/{image}', [ItemController::class, 'deleteImage'])->name('items.delete-image');

Route::get('/trash', [ItemController::class, 'trash'])->name('items.trash');

Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
Route::delete('/activity-logs/{id}', [ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
Route::post('/activity-logs/clear-old', [ActivityLogController::class, 'clearOld'])->name('activity-logs.clear-old');

Route::post('/comments/{item}', [CommentController::class, 'store'])->name('comments.store');
Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
Route::get('/comments/{item}', [CommentController::class, 'itemComments'])->name('comments.item');


Route::post('/favorite/{id}', [FavoriteController::class, 'toggle'])->name('items.favorite');

Route::get('/favorites', [FavoriteController::class, 'myFavorites'])->name('favorites.index');

// Recently Viewed
Route::get('/recently-viewed', [RecentlyViewedController::class, 'index'])
    ->name('recently-viewed.index');

Route::post('/recently-viewed/clear', [RecentlyViewedController::class, 'clear'])
    ->name('recently-viewed.clear');

Route::delete('/recently-viewed/{id}', [RecentlyViewedController::class, 'remove'])
    ->name('recently-viewed.remove');