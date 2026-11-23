<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SubscriptionController::class, 'webIndex']);
Route::post('/subscriptions', [SubscriptionController::class, 'webStore']);
Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'webDestroy']);