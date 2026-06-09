<?php

use App\Http\Controllers\Api\V1\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ユーザー認証が必要なルート
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 公開API（認証不要）
Route::apiResource('contacts', ContactController::class);
Route::get('contacts/export', [ContactController::class, 'export']);
