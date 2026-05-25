<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat', [ChatController::class, 'chat'])->name('chat.send');
Route::post('/chat/image', [ChatController::class, 'generateImage'])->name('chat.image');
Route::get('/chat/greeting/new', [ChatController::class, 'newChatGreeting'])->name('chat.greeting');
Route::get('/chat/{id}', [ChatController::class, 'loadChat'])->name('chat.load');
Route::patch('/chat/{id}', [ChatController::class, 'renameChat'])->name('chat.rename');
Route::delete('/chat', [ChatController::class, 'clearChats'])->name('chat.clear');
Route::delete('/chat/{id}', [ChatController::class, 'deleteChat'])->name('chat.delete');
