<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SubscriptionController::class, 'webDashboard'])->name('dashboard');
Route::get('/subscriptions', [SubscriptionController::class, 'webIndex'])->name('subscriptions.index');
Route::get('/subscriptions/create', [SubscriptionController::class, 'webCreate'])->name('subscriptions.create');
Route::post('/subscriptions', [SubscriptionController::class, 'webStore'])->name('subscriptions.store');
Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'webShow'])
    ->whereNumber('subscription')
    ->name('subscriptions.show');
Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'webDestroy'])
    ->whereNumber('subscription')
    ->name('subscriptions.destroy');
Route::get('/analytics', [SubscriptionController::class, 'webAnalytics'])->name('subscriptions.analytics');
