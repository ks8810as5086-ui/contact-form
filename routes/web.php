<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- 誰でもアクセスできる画面 ---
Route::get('/', [ContactController::class, 'index'])->name('contact.index');
Route::resource('contacts', ContactController::class)->only(['index', 'store']);
Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contacts.confirm');
Route::get('/contacts/thanks', function () {
    return view('contact.thanks');
})->name('contact.thanks');

// --- 管理画面（認証が必要な画面） ---
// middleware('auth') を適用
Route::prefix('admin')->middleware('auth')->group(function () {
    // お問い合わせ管理画面
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/contacts/{contact}', [AdminController::class, 'show'])->name('admin.show');
    Route::delete('/contacts/{contact}', [AdminController::class, 'destroy'])->name('admin.destroy');
    // タグ管理画面
    Route::resource('tags', TagController::class)->except(['show', 'create']);
});
