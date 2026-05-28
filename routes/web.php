<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\SitemapGenerator;


Route::get('/', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat', [ChatController::class, 'chat'])->name('chat.send');
Route::post('/chat/image', [ChatController::class, 'generateImage'])->name('chat.image');
Route::get('/chat/greeting/new', [ChatController::class, 'newChatGreeting'])->name('chat.greeting');
Route::get('/chat/{id}', [ChatController::class, 'loadChat'])->name('chat.load');
Route::patch('/chat/{id}', [ChatController::class, 'renameChat'])->name('chat.rename');
Route::delete('/chat', [ChatController::class, 'clearChats'])->name('chat.clear');
Route::delete('/chat/{id}', [ChatController::class, 'deleteChat'])->name('chat.delete');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
Route::get('/generate-sitemap', function () {

    SitemapGenerator::create('https://abhix-ai-production.up.railway.app')
        ->writeToFile(public_path('sitemap.xml'));

    return "Sitemap generated successfully!";

});
