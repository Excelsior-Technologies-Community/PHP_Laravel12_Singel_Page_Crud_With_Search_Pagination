<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ItemController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\RecentlyViewedController;


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [
    DashboardController::class,
    'index'
])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Items
|--------------------------------------------------------------------------
*/

// Item listing
Route::get('/items', [
    ItemController::class,
    'index'
])->name('items.index');

// Trash
Route::get('/items/trash', [
    ItemController::class,
    'trash'
])->name('items.trash');

// Restore
Route::post('/items/{id}/restore', [
    ItemController::class,
    'restore'
])->name('items.restore');

// Force delete
Route::delete('/items/{id}/force-delete', [
    ItemController::class,
    'forceDelete'
])->name('items.force-delete');

// Create
Route::post('/items', [
    ItemController::class,
    'store'
])->name('items.store');

// Update
Route::put('/items/{id}', [
    ItemController::class,
    'update'
])->name('items.update');

// Soft delete
Route::delete('/items/{id}', [
    ItemController::class,
    'destroy'
])->name('items.destroy');

// Show
Route::get('/items/{id}', [
    ItemController::class,
    'show'
])->name('items.show');


/*
|--------------------------------------------------------------------------
| Item Import / Export
|--------------------------------------------------------------------------
*/

Route::post('/items/import-csv', [
    ItemController::class,
    'importCsv'
])->name('items.import-csv');

Route::get('/items/export-csv', [
    ItemController::class,
    'exportCsv'
])->name('items.export-csv');


/*
|--------------------------------------------------------------------------
| Favorites
|--------------------------------------------------------------------------
*/

Route::post('/favorite/{itemId}', [
    FavoriteController::class,
    'toggle'
])->name('favorite.toggle');

Route::get('/favorites', [
    FavoriteController::class,
    'myFavorites'
])->name('favorites.index');


/*
|--------------------------------------------------------------------------
| Recently Viewed
|--------------------------------------------------------------------------
*/

Route::get('/recently-viewed', [
    RecentlyViewedController::class,
    'index'
])->name('recently-viewed.index');

Route::post('/recently-viewed/clear', [
    RecentlyViewedController::class,
    'clear'
])->name('recently-viewed.clear');

Route::delete('/recently-viewed/{id}', [
    RecentlyViewedController::class,
    'remove'
])->name('recently-viewed.remove');


/*
|--------------------------------------------------------------------------
| Comments
|--------------------------------------------------------------------------
*/

Route::post('/items/{item}/comments', [
    CommentController::class,
    'store'
])->name('comments.store');

Route::put('/comments/{comment}', [
    CommentController::class,
    'update'
])->name('comments.update');

Route::delete('/comments/{comment}', [
    CommentController::class,
    'destroy'
])->name('comments.destroy');

Route::get('/items/{itemId}/comments', [
    CommentController::class,
    'itemComments'
])->name('comments.item');

Route::post('/comments/{comment}/approve', [
    CommentController::class,
    'approve'
])->name('comments.approve');


/*
|--------------------------------------------------------------------------
| Activity Logs
|--------------------------------------------------------------------------
*/

// Activity log listing
Route::get('/activity-logs', [
    ActivityLogController::class,
    'index'
])->name('activity-logs.index');

// Export CSV
Route::get('/activity-logs/export', [
    ActivityLogController::class,
    'exportCsv'
])->name('activity-logs.export');

// View single log
Route::get('/activity-logs/{id}', [
    ActivityLogController::class,
    'show'
])->name('activity-logs.show');

// Delete log
Route::delete('/activity-logs/{id}', [
    ActivityLogController::class,
    'destroy'
])->name('activity-logs.destroy');

// Clear old logs
Route::post('/activity-logs/clear-old', [
    ActivityLogController::class,
    'clearOld'
])->name('activity-logs.clear-old');