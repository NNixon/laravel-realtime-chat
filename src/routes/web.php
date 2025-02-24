<?php 

use Illuminate\Support\Facades\Route;
use NNixon\LaravelRealtimeChat\Http\Controllers\ChatController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages/{user}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/messages', [ChatController::class, 'sendMessage'])->name('chat.send');
});