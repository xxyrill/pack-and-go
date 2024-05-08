<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MailingController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\SignController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserVehiclesController;
use App\Http\Controllers\VehicleListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/login', [SignController::class, 'login']);
Route::post('/user-registration', [UserController::class, 'store']);
Route::post('/verify-feilds', [UserController::class, 'verifyFields']);
Route::post('/vehicle-dropdown', [VehicleListController::class, 'index']);
Route::post('/send-otp', [SMSController::class, 'sendOtp']);
Route::post('/verify', [SMSController::class, 'verifyOtp']);
Route::post('/user/forgot-password', [UserController::class, 'forgotPasswordMail']);
Route::post('/user/password/save', [UserController::class, 'changePassword']);
Route::post('user/registration/save-license-file', [UserController::class, 'saveDriverLicensePhoto']);
Route::post('user/registration/ids', [UserController::class, 'saveIds']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/send-mail', [MailingController::class, 'sendRescheduleBooking']);
    Route::post('/send-otp-mail', [MailingController::class, 'changeEmail']);
    Route::post('/send-otp-contact-number', [SMSController::class, 'sendOtpUpdateContactNumber']);
    //User
    Route::prefix('user')->group(function(){
        Route::post('/get', [UserController::class, 'getUserData']);
        Route::get('/show/{id}', [UserController::class, 'show']);
        Route::get('/subscription', [UserController::class, 'getUserSubscription']);
        Route::patch('/{id}', [UserController::class, 'update']);
        Route::post('/save-license-file', [UserController::class, 'saveDriverLicensePhoto']);
        Route::post('/ids', [UserController::class, 'saveDriverLicensePhoto']);
        Route::post('/update/email', [UserController::class, 'updateEmail']);
        Route::post('/update/contact-number', [UserController::class, 'updateContactNumber']);
        Route::post('/check/password', [UserController::class, 'checkAuthentication']);
        Route::post('/update/password', [UserController::class, 'updatePassword']);
        Route::post('/update-profile-picture', [UserController::class, 'updateProfilePicture']);
        Route::post('/rate', [UserController::class, 'rateService']);
        Route::post('/ratings', [UserController::class, 'userRating']);
        Route::get('/ratings/stars', [UserController::class, 'numberOfRatings']);
        Route::post('/blocked', [UserController::class, 'userBlocked']);
        Route::post('/list', [UserController::class, 'blockList']);
        Route::get('/current-plan', [UserController::class, 'getCurrentPlant']);
        Route::post('/subscribe-plan', [UserController::class, 'subscribePlan']);
        Route::post('/suspension', [UserController::class, 'getSuspension']);
    });
    //User Vehicle
    Route::prefix('user-rating-comment')->group(function(){
        Route::post('/store', [UserController::class, 'commentRatingStore']);
        Route::post('/update', [UserController::class, 'commentRatingUpdate']);
        Route::delete('/{id}', [UserController::class, 'commentRatingDelete']);
    });

    //User Vehicle
    Route::prefix('user-vehicle')->group(function(){
        Route::post('/', [UserVehiclesController::class, 'index']);
        Route::post('/store', [UserVehiclesController::class, 'store']);
        Route::patch('/{id}', [UserVehiclesController::class, 'update']);
        Route::delete('/{id}', [UserVehiclesController::class, 'delete']);
        Route::post('/listing', [UserVehiclesController::class, 'vehicleUserLists']);
        Route::patch('/status/{id}', [UserVehiclesController::class, 'vehicleStatus']);
        Route::post('/upload-documents', [UserVehiclesController::class, 'uploadDocuments']);
    });

    //Vehicle List
    Route::prefix('vehicle-list')->group(function(){
        Route::post('/', [VehicleListController::class, 'index']);
        Route::post('/store', [VehicleListController::class, 'store']);
        Route::get('/{id}', [VehicleListController::class, 'show']);
        Route::patch('/{id}', [VehicleListController::class, 'update']);
        Route::delete('/{id}', [VehicleListController::class, 'destroy']);
    });

    //Booking
    Route::prefix('booking')->group(function(){
        Route::post('/', [BookingController::class, 'index']);
        Route::post('/store', [BookingController::class, 'store']);
        Route::patch('/update/{id}', [BookingController::class, 'update']);
        Route::post('/reschedule', [BookingController::class, 'bookingReschedule']);
        Route::post('/history', [BookingController::class, 'storeHistory']);
        Route::post('/canceled', [BookingController::class, 'storeCanceledBooking']);
        Route::post('/price', [BookingController::class, 'storeBookingPrice']);
        Route::post('/notification', [BookingController::class, 'getNotification']);
    });

    //Chats
    Route::prefix('chat')->group(function(){
        Route::post('/chatroom/{id}', [MessagingController::class, 'createChatRoom']);
        Route::post('/new', [MessagingController::class, 'newMessage']);
        Route::post('/get', [MessagingController::class, 'getMessage']);
        Route::post('/chatrooms', [MessagingController::class, 'getChatRooms']);
        Route::post('/notif/delete', [MessagingController::class, 'deleteNotification']);
    });

    //Chats
    Route::prefix('subscription')->group(function(){
        Route::post('/', [SubscriptionController::class, 'index']);
        Route::post('/store', [SubscriptionController::class, 'store']);
    });

    //Dashboards 
    Route::prefix('dashboard')->group(function(){
        Route::post('/dash-cards', [AnalyticsController::class, 'dashboard']);
        Route::post('/revenue', [AnalyticsController::class, 'getTotalRevenue']);
    });
    
    //Logout
    Route::post('/logout', [SignController::class, 'logout']);
});
