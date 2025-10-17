<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\FrontendLoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Corporate\DashboardController as CorporateDashboardController;
use App\Http\Controllers\Jobseeker\DashboardController as JobseekerDashboardController;
use App\Models\User;
use App\Models\Job;
use App\Models\Company;

Route::get('/', function () {
    $jobs = Job::where('status', 'active')->with('company')->latest()->take(6)->get();
    $companies = Company::whereNotNull('logo')->latest()->take(5)->get();
    return view('welcome', [
        'jobs' => $jobs,
        'companies' => $companies,
    ]);
});

Route::get('/login', [FrontendLoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [FrontendLoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [FrontendLoginController::class, 'logout'])->name('logout');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::middleware(['auth'])->group(function () {
    
    // Rute Pengalihan Dashboard Utama
    Route::get('/dashboard', function () {
        $user = Auth::user();
        switch ($user->role_id) {
            case 1: return redirect()->route('admin.dashboard');
            case 2: return redirect()->route('corporate.dashboard');
            case 3: return redirect()->route('jobseeker.dashboard');
            default: return redirect('/login');
        }
    });

    // Grup Rute Admin
    Route::prefix('dashboard/admin')->name('admin.')->group(function () {
        Route::get('/', function () {
            if (Auth::user()->role_id !== 1) abort(403, 'Akses Ditolak');
            return view('dashboard.admin', ['user' => Auth::user()]);
        })->name('dashboard');
        
        Route::get('/users', function () {
            if (Auth::user()->role_id !== 1) abort(403, 'Akses Ditolak');
            return view('dashboard.admin.users');
        })->name('users');

        Route::get('/jobs', function () {
            if (Auth::user()->role_id !== 1) abort(403, 'Akses Ditolak');
            return view('dashboard.admin.jobs');
        })->name('jobs');

        Route::get('/jobseekers', function () {
            if (Auth::user()->role_id !== 1) abort(403, 'Akses Ditolak');
            return view('dashboard.admin.jobseekers');
        })->name('jobseekers');
        
        Route::get('/jobseekers/{id}/profile', function ($id) {
            if (Auth::user()->role_id !== 1) abort(403, 'Akses Ditolak');
            $jobseeker = User::where('id', $id)->where('role_id', 3)->firstOrFail();
            return view('dashboard.admin.jobseeker_profile', ['jobseeker' => $jobseeker]);
        })->name('jobseeker.profile');

        Route::get('/corporates', function () {
            if (Auth::user()->role_id !== 1) abort(403, 'Akses Ditolak');
            return view('dashboard.admin.corporates');
        })->name('corporates');

        Route::get('/corporates/{id}/profile', function ($id) {
            if (Auth::user()->role_id !== 1) abort(403, 'Akses Ditolak');
            $corporate = User::where('id', $id)->where('role_id', 2)->firstOrFail();
            return view('dashboard.admin.corporate_profile', ['corporate' => $corporate]);
        })->name('corporate.profile');
    });

    // Grup Rute Corporate
    Route::prefix('dashboard/corporate')->name('corporate.')->group(function () {
        Route::get('/', [CorporateDashboardController::class, 'index'])->name('dashboard');
        Route::get('/jobs', [CorporateDashboardController::class, 'jobs'])->name('jobs');
        Route::get('/applications', [CorporateDashboardController::class, 'applications'])->name('applications');
        Route::get('/applications/{application}', [CorporateDashboardController::class, 'showApplication'])->name('applications.show');
        Route::get('/profile', [CorporateDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [CorporateDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/reset-password', [CorporateDashboardController::class, 'resetPassword'])->name('profile.reset_password');
    });
    
    // Grup Rute Jobseeker
    Route::prefix('dashboard/jobseeker')->name('jobseeker.')->group(function () {
        Route::get('/', [JobseekerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/jobs/{job}', [JobseekerDashboardController::class, 'showJob'])->name('jobs.show');
        Route::get('/companies', [JobseekerDashboardController::class, 'companies'])->name('companies');
        Route::get('/companies/{company}', [JobseekerDashboardController::class, 'showCompany'])->name('companies.show');
        Route::get('/applications', [JobseekerDashboardController::class, 'applications'])->name('applications');
        Route::get('/profile-history', [JobseekerDashboardController::class, 'profileHistory'])->name('profile.history');
        Route::get('/profile', [JobseekerDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile/update', [JobseekerDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [JobseekerDashboardController::class, 'updatePassword'])->name('profile.password');
    });
});