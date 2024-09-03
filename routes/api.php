<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TimeEntryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
// users routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Activity routes
Route::middleware('auth:sanctum')->post('/activities', [ActivityController::class, 'store']);
Route::middleware('auth:sanctum')->get('/activities', [ActivityController::class, 'index']);

// TimeEntry routes

Route::middleware('auth:sanctum')->post('/time-entries', [TimeEntryController::class, 'store']);
Route::middleware('auth:sanctum')->post('/time-entries/{id}/end', [TimeEntryController::class, 'end']);
Route::middleware('auth:sanctum')->get('/time-entries', [TimeEntryController::class, 'index']);

// Notification routes
Route::middleware('auth:sanctum')->get('/notifications', [NotificationController::class, 'index']);
Route::middleware('auth:sanctum')->post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
