<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/subscriptions', [SubscriptionController::class, 'index']);
Route::post('/subscriptions', [SubscriptionController::class, 'store']);
Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'destroy']);
