<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\UrlVisitController;
use App\Http\Controllers\UserController;
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
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);
//Users
Route::middleware('auth:sanctum')->get('/users', [UserController::class, 'index']);
Route::middleware('auth:sanctum')->get('/users/{user}/edit', [UserController::class, 'edit']);
Route::get('/users/{id}', [UserController::class, 'show'])->middleware('auth:sanctum'); // Get user by ID
Route::put('/users/{id}', [UserController::class, 'update'])->middleware('auth:sanctum'); // Get user by ID
Route::get('/user', [UserController::class, 'showProfile'])->middleware('auth:sanctum'); // Get user by ID
Route::middleware('auth:sanctum')->put('/user', [UserController::class, 'updateprofile']);

Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('auth:sanctum'); // Get user by ID

Route::get('/roles', [RoleController::class, 'index']);

// Activity routes
Route::middleware('auth:sanctum')->post('/activities/start', [ActivityController::class, 'start']);
Route::middleware('auth:sanctum')->put('/activities/stop/{id}', [ActivityController::class, 'stop']);
Route::middleware('auth:sanctum')->post('/activities/save-url/{id}', [ActivityController::class, 'saveUrl']);
Route::middleware('auth:sanctum')->post('/activities/analyzeUrl', [ActivityController::class, 'analyzeUrl']);
Route::middleware('auth:sanctum')->get('/activities', [ActivityController::class, 'index']);
Route::delete('/activities/{id}', [ActivityController::class, 'delete'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/activities/{id}', [ActivityController::class, 'show']);
Route::get('/activities', [ActivityController::class, 'showUserActivities']); // Fetch user's activities

Route::post('/logout', [ApiController::class, 'logout'])->middleware('auth:sanctum');


// TimeEntry routesRoute::middleware('auth:sanctum')->get('/activities/stop', [ActivityController::class, 'index']);


Route::middleware('auth:sanctum')->post('/time-entries', [TimeEntryController::class, 'store']);
Route::middleware('auth:sanctum')->post('/time-entries/{id}/end', [TimeEntryController::class, 'end']);
Route::middleware('auth:sanctum')->get('/time-entries', [TimeEntryController::class, 'index']);

// Notification routes
Route::middleware('auth:sanctum')->get('/notifications', [NotificationController::class, 'index']);
Route::middleware('auth:sanctum')->post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
// Apply Sanctum authentication middleware to the routes
Route::middleware('auth:sanctum')->prefix('url-visits')->group(function () {
    Route::get('/', [UrlVisitController::class, 'index']);
    Route::post('/', [UrlVisitController::class, 'store']);
    Route::get('/{id}', [UrlVisitController::class, 'show']);
    Route::put('/{id}', [UrlVisitController::class, 'update']);
    Route::delete('/{id}', [UrlVisitController::class, 'destroy']);
});
Route::apiResource('url-visits', UrlVisitController::class)->middleware('auth:sanctum');
Route::get('/members', [MemberController::class, 'index'])->middleware('auth:sanctum');
Route::post('/invite', [InvitationController::class, 'invite'])->middleware('auth:sanctum');
Route::get('/invitations', [InvitationController::class, 'index'])->middleware('auth:sanctum');
 // Route to accept an invitation by invitation ID
// In routes/api.php
Route::middleware('auth:sanctum')->post('/invitations/{id}/accept', [InvitationController::class, 'acceptInvitation']);
    
 // Route to delete an invitation by invitation ID
 Route::delete('/invitations/{id}', [InvitationController::class, 'deleteInvitation'])->middleware('auth:sanctum');

 Route::middleware('auth:sanctum')->get('/members', [MemberController::class, 'index']);
 
 Route::middleware('auth:sanctum')->group(function () {
    Route::get('members/{id}', [MemberController::class, 'edit']);
    Route::put('members/{id}', [MemberController::class, 'update']);
    Route::delete('/members/{id}', [MemberController::class, 'deleteMember']);
});