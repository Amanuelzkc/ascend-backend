<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\GeneralApplicationController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\UploadController;

Route::post('/login', [AuthController::class, 'login']);

// Public routes
Route::post('/upload/image', [UploadController::class, 'uploadImage']);
Route::post('/resume', [UploadController::class, 'uploadResume']);

Route::get('/jobs', [JobPostingController::class, 'index']);
Route::get('/jobs/{slug}', [JobPostingController::class, 'show']);
Route::post('/jobs/{job}/apply', [ApplicationController::class, 'store']);
Route::post('/applications/submit', [ApplicationController::class, 'store']);

Route::get('/blog', [BlogPostController::class, 'index']);
Route::get('/blog/{slug}', [BlogPostController::class, 'show']);

Route::get('/insights', [InsightController::class, 'index']);
Route::get('/insights/{slug}', [InsightController::class, 'show']);

Route::post('/general-applications', [GeneralApplicationController::class, 'store']);

// Protected admin routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/admin/download-resume', [UploadController::class, 'downloadResume']);

    Route::apiResource('jobs', JobPostingController::class)->except(['index', 'show']);
    Route::apiResource('applications', ApplicationController::class)->except(['store']);
    Route::apiResource('blog', BlogPostController::class)->except(['index', 'show']);
    Route::apiResource('insights', InsightController::class)->except(['index', 'show']);
    Route::apiResource('general-applications', GeneralApplicationController::class)->except(['store']);
    Route::apiResource('notifications', NotificationController::class);
    Route::get('/admin/notifications', [NotificationController::class, 'index']);
    Route::patch('/admin/notifications', [NotificationController::class, 'bulkUpdate']);
    Route::delete('/admin/notifications', [NotificationController::class, 'bulkDelete']);

    // Admin User Management
    Route::get('/admin/users/list', [AdminUserController::class, 'list']);
    Route::post('/admin/users/create', [AdminUserController::class, 'create']);
    Route::post('/admin/users/update', [AdminUserController::class, 'update']);
    Route::post('/admin/users/reset-password', [AdminUserController::class, 'resetPassword']);
    Route::post('/admin/users/delete', [AdminUserController::class, 'delete']);

    // Scheduling
    Route::post('/admin/publish-sync', [\App\Http\Controllers\SchedulingController::class, 'sync']);
});
