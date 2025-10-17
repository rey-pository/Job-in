<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Job;
use App\Models\Company;
use App\Models\Application;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role_id !== 2) {
                abort(403, 'Akses Ditolak');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Auth::user();
        $company = $user->company;
        $activeJobsCount = 0;
        if ($company) {
            $activeJobsCount = Job::where('company_id', $company->id)->where('status', 'active')->count();
        }
        $profileVisits = 1250;

        // --- INI PERBAIKANNYA ---
        // Mengubah 'dashboard.corporate.index' menjadi 'dashboard.corporate'
        return view('dashboard.corporate', [
            'activeJobsCount' => $activeJobsCount,
            'profileVisits' => $profileVisits,
        ]);
    }

    public function jobs()
    {
        return view('dashboard.corporate.jobs');
    }

    public function applications()
    {
        $company = Auth::user()->company;
        $applications = Application::whereHas('job', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->with(['user', 'job'])->latest()->paginate(10);

        return view('dashboard.corporate.applications', ['applications' => $applications]);
    }
    
    public function showApplication(Application $application)
    {
        if ($application->job->company_id !== Auth::user()->company->id) {
            abort(403, 'Akses Ditolak');
        }
        $application->load('user.educationHistories', 'user.workExperiences', 'user.organizationExperiences', 'user.portfolioHistories');
        return view('dashboard.corporate.application_show', ['application' => $application]);
    }

    public function profile()
    {
        $company = Auth::user()->company;
        return view('dashboard.corporate.profile', ['company' => $company]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $company = $user->company ?? new Company(['user_id' => $user->id]);
        $validated = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::delete('public/' . $company->logo);
            }
            $path = $request->file('logo')->store('uploads/logos', 'public');
            $validated['logo'] = $path;
        }
        $company->fill($validated)->save();
        return redirect()->route('corporate.profile')->with('status', 'Profil perusahaan berhasil diperbarui!');
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['password' => 'required|string|min:8|confirmed']);
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->route('corporate.profile')->with('status', 'Password berhasil direset!');
    }
}