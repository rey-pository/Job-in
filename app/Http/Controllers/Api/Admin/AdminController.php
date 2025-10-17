<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Job;
use App\Models\EducationHistory;
use App\Models\WorkExperience;
use App\Models\OrganizationExperience;
use App\Models\PortfolioHistory;
use App\Models\Company;

class AdminController extends Controller
{
    private function authorizeAdmin($user)
    {
        if ($user->role_id !== 1) {
            abort(response()->json(['message' => 'Only admin users can access this feature.'], 403));
        }
    }

    public function getDashboardStats(Request $request)
    {
        $this->authorizeAdmin($request->user());
        $stats = [
            'total_users' => User::count(),
            'total_corporates' => User::where('role_id', 2)->count(),
            'total_jobseekers' => User::where('role_id', 3)->count(),
            'active_jobs' => Job::where('status', 'active')->count(),
            'pending_corporates' => User::where('role_id', 2)->where('status_verifikasi', 'pending')->count(),
        ];
        return response()->json($stats);
    }
    
    public function getChartData(Request $request)
    {
        $this->authorizeAdmin($request->user());
        $labels = [];
        $jobseekersData = [];
        $corporatesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $jobseekersData[] = User::where('role_id', 3)->whereDate('created_at', $date)->count();
            $corporatesData[] = User::where('role_id', 2)->whereDate('created_at', $date)->count();
        }
        return response()->json(['labels' => $labels, 'jobseekers' => $jobseekersData, 'corporates' => $corporatesData]);
    }

    public function getAllUsers(Request $request)
    {
        $this->authorizeAdmin($request->user());
        $query = User::with('role')->orderByDesc('created_at');
        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                  ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
            });
        }
        $users = $query->paginate(5);
        return response()->json($users);
    }

    public function getJobseekers(Request $request)
    {
        $this->authorizeAdmin($request->user());
        $query = User::where('role_id', 3)->orderByDesc('created_at');
        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                  ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
            });
        }
        $jobseekers = $query->paginate(10);
        return response()->json($jobseekers);
    }

    public function getCorporates(Request $request)
    {
        $this->authorizeAdmin($request->user());
        $query = User::where('role_id', 2)->with('company')->orderByDesc('created_at');
        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                  ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
            });
        }
        $corporates = $query->paginate(10);
        return response()->json($corporates);
    }

    public function createUser(Request $request)
    {
        $this->authorizeAdmin($request->user());
        $validated = $request->validate([
            'name' => 'required|string|max:255', 'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number',
            'password' => 'required|string|min:6', 'role_id' => 'required|in:1,2,3',
            'status_verifikasi' => 'nullable|in:approved,pending,rejected'
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $createdUser = User::create($validated);
        return response()->json(['message' => 'User created successfully.', 'data' => $createdUser], 201);
    }

    public function getUserDetail(Request $request, $id)
    {
        $this->authorizeAdmin($request->user());
        $target = User::with('role')->find($id);
        if (!$target) return response()->json(['message' => 'User not found.'], 404);
        return response()->json(['message' => 'User details retrieved successfully.', 'data' => $target], 200);
    }

    public function updateUser(Request $request, $id)
    {
        $this->authorizeAdmin($request->user());
        $target = User::find($id);
        if (!$target) return response()->json(['message' => 'User not found.'], 404);
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255', 'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone_number' => 'sometimes|string|max:20|unique:users,phone_number,' . $id,
            'password' => 'sometimes|string|min:6', 'role_id' => 'sometimes|in:1,2,3',
            'status_verifikasi' => 'nullable|in:approved,pending,rejected'
        ]);
        if (isset($validated['password'])) $validated['password'] = Hash::make($validated['password']);
        $target->update($validated);
        return response()->json(['message' => 'User updated successfully.', 'data' => $target], 200);
    }

    public function deleteUser(Request $request, $id)
    {
        $this->authorizeAdmin($request->user());
        $target = User::find($id);
        if (!$target) return response()->json(['message' => 'User not found.'], 404);
        $target->delete();
        return response()->json(['message' => 'User deleted successfully.'], 200);
    }

    public function getAllJobs(Request $request)
    {
        $this->authorizeAdmin($request->user());
        $query = Job::with('company')->orderByDesc('created_at');
        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
        }
        $jobs = $query->paginate(5);
        return response()->json($jobs);
    }

    public function getJobDetail(Request $request, $id)
    {
        $this->authorizeAdmin($request->user());
        $job = Job::with('company')->find($id);
        if (!$job) return response()->json(['message' => 'Job not found.'], 404);
        return response()->json(['data' => $job]);
    }

    public function createJob(Request $request)
    {
        $this->authorizeAdmin($request->user());
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id', 'title' => 'required|string|max:255',
            'department' => 'required|string|max:255', 'description' => 'required|string',
            'province' => 'nullable|string|max:255', 'city' => 'nullable|string|max:255',
            'work_type' => 'required|in:on-site,remote,hybrid', 'published_date' => 'required|date',
            'expired_date' => 'required|date|after_or_equal:published_date', 'status' => 'required|in:active,expired',
        ]);
        $job = Job::create($validated);
        return response()->json($job, 201);
    }
    
    public function updateJob(Request $request, $id)
    {
        $this->authorizeAdmin($request->user());
        $job = Job::find($id);
        if (!$job) return response()->json(['message' => 'Job not found.'], 404);
        $validated = $request->validate([
            'company_id' => 'sometimes|exists:companies,id', 'title' => 'sometimes|string|max:255',
            'department' => 'sometimes|string|max:255', 'description' => 'sometimes|string',
            'province' => 'sometimes|nullable|string|max:255', 'city' => 'sometimes|nullable|string|max:255',
            'work_type' => 'sometimes|in:on-site,remote,hybrid', 'published_date' => 'sometimes|date',
            'expired_date' => 'sometimes|date|after_or_equal:published_date', 'status' => 'sometimes|in:active,expired',
        ]);
        $job->update($validated);
        return response()->json(['message' => 'Job updated successfully.', 'data' => $job], 200);
    }

    public function deleteJob(Request $request, $id)
    {
        $this->authorizeAdmin($request->user());
        $job = Job::find($id);
        if (!$job) return response()->json(['message' => 'Job not found.'], 404);
        $job->delete();
        return response()->json(['message' => 'Job deleted successfully.'], 200);
    }

    public function getJobseekerProfile(Request $request, $userId)
    {
        $this->authorizeAdmin($request->user());
        $jobseeker = User::where('id', $userId)->where('role_id', 3)->with([
            'educationHistories' => fn($q) => $q->orderByDesc('end_year'), 'workExperiences' => fn($q) => $q->orderByDesc('end_date'),
            'organizationExperiences' => fn($q) => $q->orderByDesc('end_year'), 'portfolioHistories' => fn($q) => $q->orderByDesc('date'),
        ])->firstOrFail();
        return response()->json($jobseeker);
    }

    public function addEducation(Request $request, $userId)
    {
        $this->authorizeAdmin($request->user());
        $validated = $request->validate(['institution' => 'required|string|max:255', 'degree' => 'required|string|max:255', 'start_year' => 'required|digits:4', 'end_year' => 'nullable|digits:4']);
        $education = EducationHistory::create($validated + ['user_id' => $userId]);
        return response()->json($education, 201);
    }

    public function updateEducation(Request $request, $educationId)
    {
        $this->authorizeAdmin($request->user());
        $education = EducationHistory::findOrFail($educationId);
        $validated = $request->validate(['institution' => 'sometimes|string|max:255', 'degree' => 'sometimes|string|max:255', 'start_year' => 'sometimes|digits:4', 'end_year' => 'nullable|digits:4']);
        $education->update($validated);
        return response()->json($education);
    }

    public function deleteEducation(Request $request, $educationId)
    {
        $this->authorizeAdmin($request->user());
        EducationHistory::findOrFail($educationId)->delete();
        return response()->json(['message' => 'Education record deleted.']);
    }

    public function addWorkExperience(Request $request, $userId)
    {
        $this->authorizeAdmin($request->user());
        $validated = $request->validate([
            'company_name' => 'required|string|max:255', 'position' => 'required|string|max:255',
            'start_date' => 'required|date', 'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);
        $work = WorkExperience::create($validated + ['user_id' => $userId]);
        return response()->json($work, 201);
    }

    public function updateWorkExperience(Request $request, $workId)
    {
        $this->authorizeAdmin($request->user());
        $work = WorkExperience::findOrFail($workId);
        $validated = $request->validate([
            'company_name' => 'sometimes|string|max:255', 'position' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date', 'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);
        $work->update($validated);
        return response()->json($work);
    }

    public function deleteWorkExperience(Request $request, $workId)
    {
        $this->authorizeAdmin($request->user());
        WorkExperience::findOrFail($workId)->delete();
        return response()->json(['message' => 'Work experience record deleted.']);
    }

    public function addOrganization(Request $request, $userId)
    {
        $this->authorizeAdmin($request->user());
        $validated = $request->validate(['organization_name' => 'required|string|max:255', 'role' => 'required|string|max:255', 'start_year' => 'required|digits:4', 'end_year' => 'nullable|digits:4', 'description' => 'nullable|string']);
        $org = OrganizationExperience::create($validated + ['user_id' => $userId]);
        return response()->json($org, 201);
    }

    public function updateOrganization(Request $request, $orgId)
    {
        $this->authorizeAdmin($request->user());
        $org = OrganizationExperience::findOrFail($orgId);
        $validated = $request->validate(['organization_name' => 'sometimes|string|max:255', 'role' => 'sometimes|string|max:255', 'start_year' => 'sometimes|digits:4', 'end_year' => 'nullable|digits:4', 'description' => 'nullable|string']);
        $org->update($validated);
        return response()->json($org);
    }

    public function deleteOrganization(Request $request, $orgId)
    {
        $this->authorizeAdmin($request->user());
        OrganizationExperience::findOrFail($orgId)->delete();
        return response()->json(['message' => 'Organization record deleted.']);
    }

public function addPortfolio(Request $request, $userId)
    {
        $this->authorizeAdmin($request->user());
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:competition,certification,training,publication,achievement',
            'issuer' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'attachment' => 'nullable|string',
            'description' => 'nullable|string'
        ]);
        $portfolio = PortfolioHistory::create($validated + ['user_id' => $userId]);
        return response()->json($portfolio, 201);
    }

    public function updatePortfolio(Request $request, $portfolioId)
    {
        $this->authorizeAdmin($request->user());
        $portfolio = PortfolioHistory::findOrFail($portfolioId);
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:competition,certification,training,publication,achievement',
            'issuer' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'attachment' => 'nullable|string',
            'description' => 'nullable|string'
        ]);
        $portfolio->update($validated);
        return response()->json($portfolio);
    }

    public function deletePortfolio(Request $request, $portfolioId)
    {
        $this->authorizeAdmin($request->user());
        PortfolioHistory::findOrFail($portfolioId)->delete();
        return response()->json(['message' => 'Portfolio record deleted.']);
    }

    public function getCorporateProfile(Request $request, $userId)
    {
        $this->authorizeAdmin($request->user());
        $corporate = User::where('id', $userId)->where('role_id', 2)->with('company')->firstOrFail();
        return response()->json($corporate);
    }

    public function updateOrCreateCorporateProfile(Request $request, $userId)
    {
        $this->authorizeAdmin($request->user());
        $user = User::where('id', $userId)->where('role_id', 2)->firstOrFail();
        $validated = $request->validate([
            'website' => 'sometimes|nullable|url',
            'address' => 'sometimes|nullable|string',
            'description' => 'sometimes|nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $companyData = $validated;
        if ($request->hasFile('logo')) {
            if ($user->company && $user->company->logo) {
                Storage::delete('public/' . $user->company->logo);
            }
            $path = $request->file('logo')->store('uploads/logos', 'public');
            $companyData['logo'] = $path;
        }
        $company = Company::updateOrCreate(
            ['user_id' => $user->id],
            $companyData
        );
        return response()->json(['message' => 'Corporate profile saved successfully.', 'data' => $company]);
    }
    
    public function getCompaniesList(Request $request)
    {
        $this->authorizeAdmin($request->user());
        $companies = Company::orderBy('name')->get(['id', 'name', 'logo']);
        return response()->json($companies);
    }
}