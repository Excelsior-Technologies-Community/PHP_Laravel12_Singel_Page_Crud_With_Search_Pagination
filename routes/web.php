<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::post('/store', [ItemController::class, 'store'])->name('items.store');
Route::post('/update/{item}', [ItemController::class, 'update'])->name('items.update');
Route::get('/delete/{item}', [ItemController::class, 'destroy'])->name('items.delete');



