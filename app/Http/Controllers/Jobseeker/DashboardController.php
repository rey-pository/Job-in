<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Job;
use App\Models\Company;
use App\Models\Application;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role_id !== 3) {
                abort(403, 'Akses Ditolak');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = Job::where('status', 'active')->with('company');
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
        }
        if ($request->has('work_type') && is_array($request->input('work_type'))) {
            $workTypes = array_filter($request->input('work_type'));
            if (!empty($workTypes)) {
                $query->whereIn('work_type', $workTypes);
            }
        }
        $jobs = $query->latest('published_date')->paginate(9)->appends($request->all());
        return view('dashboard.jobseeker.index', ['jobs' => $jobs]);
    }
    
    public function showJob(Job $job)
    {
        $job->load('company');
        $hasApplied = Application::where('user_id', Auth::id())
                                 ->where('job_id', $job->id)
                                 ->exists();
        return view('dashboard.jobseeker.show', [
            'job' => $job,
            'hasApplied' => $hasApplied,
        ]);
    }

    public function companies(Request $request)
    {
        $query = Company::query();
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
        }
        $companies = $query->latest()->paginate(12)->appends($request->all());
        return view('dashboard.jobseeker.companies', ['companies' => $companies]);
    }
    
    public function showCompany(Company $company)
    {
        $jobs = $company->jobs()->where('status', 'active')->latest()->paginate(5);
        return view('dashboard.jobseeker.company_profile', [
            'company' => $company,
            'jobs' => $jobs,
        ]);
    }

    public function applications()
    {
        $applications = Application::where('user_id', Auth::id())
                                   ->with('job.company')
                                   ->latest()
                                   ->paginate(10);
        return view('dashboard.jobseeker.applications', ['applications' => $applications]);
    }

    public function profileHistory()
    {
        $user = Auth::user()->load([
            'educationHistories' => fn($q) => $q->orderByDesc('end_year'),
            'workExperiences' => fn($q) => $q->orderByDesc('end_date'),
            'organizationExperiences' => fn($q) => $q->orderByDesc('end_year'),
            'portfolioHistories' => fn($q) => $q->orderByDesc('date'),
        ]);
        return view('dashboard.jobseeker.profile_history', ['user' => $user]);
    }

    public function profile()
    {
        return view('dashboard.jobseeker.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number,' . $user->id,
        ]);
        $user->update($validated);
        return redirect()->route('jobseeker.profile')->with('status', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Password lama tidak cocok.');
                }
            }],
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
        return redirect()->route('jobseeker.profile')->with('status', 'Password berhasil diubah!');
    }
}