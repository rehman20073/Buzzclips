<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('feed.index');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
Route::post('/videos/{video}/like', [FeedController::class, 'toggleLike'])->name('videos.like');
Route::post('/videos/{video}/comment', [FeedController::class, 'addComment'])->name('videos.comment');
Route::get('/videos/{video}/comments', [FeedController::class, 'getComments'])->name('videos.comments');


require __DIR__.'/auth.php';
