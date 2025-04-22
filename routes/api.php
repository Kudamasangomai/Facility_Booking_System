<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\API\v1\UserController;
use App\Http\Controllers\Api\v1\PayNowController;
use App\Http\Controllers\API\v1\PaypalController;
use App\Http\Controllers\Api\v1\BookingController;
use App\Http\Controllers\Api\v1\FacilityController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return 'working';
});

// Public routes
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('forgotpassword', [AuthController::class, 'forgotpassword'])->name('forgotpassword');
Route::get('passwordreset/{token}', [AuthController::class, 'passwordreset'])->name('password.reset');
Route::post('passwordstore', [AuthController::class, 'passwordstore'])->name('password.store');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
Route::get('v1/facilities', [FacilityController::class, 'index']);


Route::group(['prefix' => 'v1', 'middleware' => 'auth.basic'], function () {


    // Facilities
    Route::apiResource('facilities', FacilityController::class)->except('index');
    Route::post('/facilities/addfacilityimage/{facility}', [FacilityController::class, 'addfacilityimage']);


    // Bookings
    Route::apiResource('bookings', BookingController::class);
    Route::get('/bookings/search', [BookingController::class, 'searchfacilityavailability']);

    // Users
    Route::apiResource('users', UserController::class);
    Route::post('/users/updateuserstatus/{id}', [UserController::class, 'updateuserstatus']);
    

    // Paymentd
    Route::get('paynowpayment/{id}', [PayNowController::class, 'payment'])->name('paynow.payment');
    Route::get('payment/{id}', [PaypalController::class, 'payment'])->name('paypal.payment');
    Route::get('payment/cancel',  [PaypalController::class, 'cancel'])->name('payment.cancel');
    Route::get('payment/success', [PaypalController::class, 'success'])->name('payment.success');
});

Route::get('v1/facilities', [FacilityController::class, 'index']);

Route::fallback(function () {
    return response()->json(
        [
            'message' => 'Route Not Found. If error persists, contact the Administrator on Kudam775@gmail.com'
        ],
        Response::HTTP_NOT_FOUND
    );
});
