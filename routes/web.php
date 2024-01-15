<?php

use App\Http\Controllers\BinanceController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'subscribe.free'])->group(function () {
    Route::get('index', [BinanceController::class, 'index']);
    Route::get('trade', [BinanceController::class, 'trade']);
    Route::get('price', [BinanceController::class, 'price']);
    Route::get('account', [BinanceController::class, 'account']);


    // Subscription
    Route::get('user/subscribe/{planId}/{paymentFrequency}', [SubscriptionController::class, 'checkout'])->name('subscription.stripe.checkout');
    Route::post('subscriptions/stripe/activate', [SubscriptionController::class, 'activateStripeSubscription'])->name('subscription.stripe.activate');
    Route::post('subscriptions/cancel', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::get('/subscription', [SubscriptionController::class, 'show'])->name('subscription');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
