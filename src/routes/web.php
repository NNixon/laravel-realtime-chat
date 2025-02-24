<?php 

use Illuminate\Support\Facades\Route;
use NNixon\LaravelRealtimeChat\Http\Controllers\ChatController;

Route::middleware(['web', 'auth'])->prefix(config('chat.route_prefix'))->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/messages/{user}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/messages', [ChatController::class, 'sendMessage'])->name('chat.send');
});