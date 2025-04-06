<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\{
    AuthController,
    ContactController,
    UserController,
    CustomerController
};



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/splash-screen', [AuthController::class, 'splashScreens']);
Route::get('/timezones', [AuthController::class, 'getTimeZones']);
Route::post('/contact', [ContactController::class, 'submitContact']);

Route::group(['prefix'=>'auth'], function(){
    Route::post('/send-phone-otp', [AuthController::class, 'sendPhoneOtp']);
    Route::post('/verify-phone-otp', [AuthController::class, 'verifyPhoneOtp']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-register', [AuthController::class, 'verifyRegister']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/set-forgot-password', [AuthController::class, 'setForgotPassword']);
});

Route::middleware('jwt.verify')->group(function() {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);     
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);

    Route::get('/customer/list', [CustomerController::class, 'customerList']);
    Route::get('/customer/search', [CustomerController::class, 'customerSearch']);
    Route::post('/customer/recept', [CustomerController::class, 'customerReceipt']);
    Route::post('/customer/invoice/detail', [CustomerController::class, 'customerInvoiceDetail']);
    Route::post('/customer/recept/store', [CustomerController::class, 'customerReceptStore']);
});

Route::get('/customer/leger/{legerid}', [CustomerController::class, 'customerLeger']);

Route::get('/customer/list/copy', [CustomerController::class, 'customerListcopy']);