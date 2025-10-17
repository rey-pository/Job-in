<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Jobseeker\ProfileController;
use App\Http\Controllers\Api\Corporate\CompanyController;
use App\Http\Controllers\Api\Corporate\JobController as CorporateJobController;
use App\Http\Controllers\Api\Jobseeker\JobListedController;
use App\Http\Controllers\Api\Jobseeker\ApplicationController;
use App\Http\Controllers\Api\Corporate\ApplicationReviewController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Public\JobController as PublicJobController;

// Rute Publik (Tidak Perlu Login / API Key)
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

// Rute Publik Pihak Ketiga (Membutuhkan API Key)
Route::prefix('public')->middleware('auth.apikey')->group(function () {
    Route::get('/jobs', [PublicJobController::class, 'index']);
    Route::post('/jobs/{job}/apply', [PublicJobController::class, 'apply']);
});

// Grup Rute Untuk Pengguna yang Sudah Login
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [LoginController::class, 'logout']);

    // Grup Rute Admin
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard-stats', [AdminController::class, 'getDashboardStats']);
        Route::get('/chart-data', [AdminController::class, 'getChartData']);
        Route::get('/all-users', [AdminController::class, 'getAllUsers']);
        Route::post('/create-user', [AdminController::class, 'createUser']);
        Route::get('/user/{id}', [AdminController::class, 'getUserDetail']);
        Route::put('/update-user/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/delete-user/{id}', [AdminController::class, 'deleteUser']);
        Route::get('/jobs', [AdminController::class, 'getAllJobs']);
        Route::post('/jobs', [AdminController::class, 'createJob']);
        Route::get('/jobs/{id}', [AdminController::class, 'getJobDetail']);
        Route::put('/jobs/{id}', [AdminController::class, 'updateJob']);
        Route::delete('/jobs/{id}', [AdminController::class, 'deleteJob']);
        Route::get('/jobseekers', [AdminController::class, 'getJobseekers']);
        Route::get('/jobseekers/{userId}/profile', [AdminController::class, 'getJobseekerProfile']);
        Route::post('/jobseekers/{userId}/education', [AdminController::class, 'addEducation']);
        Route::put('/education/{educationId}', [AdminController::class, 'updateEducation']);
        Route::delete('/education/{educationId}', [AdminController::class, 'deleteEducation']);
        Route::post('/jobseekers/{userId}/work-experience', [AdminController::class, 'addWorkExperience']);
        Route::put('/work-experience/{workId}', [AdminController::class, 'updateWorkExperience']);
        Route::delete('/work-experience/{workId}', [AdminController::class, 'deleteWorkExperience']);
        Route::post('/jobseekers/{userId}/organization-experience', [AdminController::class, 'addOrganization']);
        Route::put('/organization-experience/{orgId}', [AdminController::class, 'updateOrganization']);
        Route::delete('/organization-experience/{orgId}', [AdminController::class, 'deleteOrganization']);
        Route::post('/jobseekers/{userId}/portfolio', [AdminController::class, 'addPortfolio']);
        Route::put('/portfolio/{portfolioId}', [AdminController::class, 'updatePortfolio']);
        Route::delete('/portfolio/{portfolioId}', [AdminController::class, 'deletePortfolio']);
        Route::get('/corporates', [AdminController::class, 'getCorporates']);
        Route::get('/corporates/{userId}/profile', [AdminController::class, 'getCorporateProfile']);
        Route::post('/corporates/{userId}/profile', [AdminController::class, 'updateOrCreateCorporateProfile']);
        Route::get('/companies-list', [AdminController::class, 'getCompaniesList']);
    });

    // Grup Rute Jobseeker
    Route::prefix('jobseeker')->group(function () {
        Route::get('/education', [ProfileController::class, 'getEducation']);
        Route::post('/education', [ProfileController::class, 'storeEducation']);
        Route::put('/education/{id}', [ProfileController::class, 'updateEducation']);
        Route::delete('/education/{id}', [ProfileController::class, 'deleteEducation']);
        Route::get('/work', [ProfileController::class, 'getWork']);
        Route::post('/work', [ProfileController::class, 'storeWork']);
        Route::put('/work/{id}', [ProfileController::class, 'updateWork']);
        Route::delete('/work/{id}', [ProfileController::class, 'deleteWork']);
        Route::get('/organization', [ProfileController::class, 'getOrganization']);
        Route::post('/organization', [ProfileController::class, 'storeOrganization']);
        Route::put('/organization/{id}', [ProfileController::class, 'updateOrganization']);
        Route::delete('/organization/{id}', [ProfileController::class, 'deleteOrganization']);
        Route::get('/portfolio', [ProfileController::class, 'getPortfolio']);
        Route::post('/portfolio', [ProfileController::class, 'storePortfolio']);
        Route::put('/portfolio/{id}', [ProfileController::class, 'updatePortfolio']);
        Route::delete('/portfolio/{id}', [ProfileController::class, 'deletePortfolio']);
        Route::get('/applications', [ApplicationController::class, 'index']);
    });

    // Rute Jobseeker untuk lowongan (dipindahkan ke sini)
    Route::get('/jobs', [JobListedController::class, 'index']);
    Route::get('/jobs/{job}', [JobListedController::class, 'show']);
    Route::post('/jobs/{job}/apply', [JobListedController::class, 'apply']);

    // Grup Rute Corporate
    Route::prefix('corporate')->group(function () {
        Route::put('/company', [CompanyController::class, 'update']);
        Route::get('/company', [CompanyController::class, 'show']);
        
        Route::prefix('jobs')->group(function () {
            Route::get('/', [CorporateJobController::class, 'index']);
            Route::post('/', [CorporateJobController::class, 'store']);
            Route::get('/{id}', [CorporateJobController::class, 'show']);
            Route::put('/{id}', [CorporateJobController::class, 'update']);
            Route::delete('/{id}', [CorporateJobController::class, 'destroy']);
        });

        Route::prefix('applications')->group(function () {
            Route::get('/', [ApplicationReviewController::class, 'index']);
            Route::get('/{id}', [ApplicationReviewController::class, 'show']);
            Route::post('/{id}/status', [ApplicationReviewController::class, 'updateStatus']);
        });
    });
});