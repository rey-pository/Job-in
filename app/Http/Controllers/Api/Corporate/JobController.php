<?php

namespace App\Http\Controllers\Api\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller
{
    /**
     * Display all jobs belonging to the logged-in corporate user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Pastikan hanya role corporate yang bisa akses
        if ($user->role_id !== 2) {
            return response()->json(['message' => 'Only corporate users can access job data.'], 403);
        }

        $company = $user->company;
        if (!$company) {
            return response()->json(['message' => 'Company profile not found.'], 404);
        }

        // Ambil semua job + relasi company (nama & logo)
        $jobs = Job::with('company:id,name,logo')
            ->where('company_id', $company->id)
            ->orderByDesc('published_date')
            ->get();

        return response()->json([
            'message' => 'Job listings retrieved successfully.',
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'logo' => $company->logo,
            ],
            'data' => $jobs,
        ], 200);
    }

    /**
     * Store a new job posting for the authenticated corporate user.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role_id !== 2) {
            return response()->json(['message' => 'Only corporate users can create jobs.'], 403);
        }

        $company = $user->company;
        if (!$company) {
            return response()->json(['message' => 'Company profile not found.'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'description' => 'required|string',
            'published_date' => 'required|date',
            'expired_date' => 'required|date|after:published_date',
            'status' => 'in:active,expired',
        ]);

        // Sanitasi input WYSIWYG agar tidak menyimpan tag berbahaya
        $validated['description'] = strip_tags($validated['description'], '<p><b><i><u><ul><ol><li><br><a>');

        $validated['company_id'] = $company->id;

        $job = Job::create($validated);

        // Ambil ulang job dengan relasi company agar response langsung lengkap
        $job = Job::with('company:id,name,logo')->find($job->id);

        return response()->json([
            'message' => 'Job posting created successfully.',
            'data' => $job,
        ], 201);
    }

    /**
     * Update an existing job posting.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role_id !== 2) {
            return response()->json(['message' => 'Only corporate users can update jobs.'], 403);
        }

        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found.'], 404);
        }

        if ($job->company_id !== $user->company->id) {
            return response()->json(['message' => 'Unauthorized access to this job.'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'department' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'published_date' => 'sometimes|date',
            'expired_date' => 'sometimes|date|after_or_equal:published_date',
            'status' => 'in:active,expired',
        ]);

        // desc adjustment
        if (isset($validated['description'])) {
            $validated['description'] = strip_tags($validated['description'], '<p><b><i><u><ul><ol><li><br><a>');
        }

        $job->update($validated);

        $job = Job::with('company:id,name,logo')->find($job->id);

        return response()->json([
            'message' => 'Job updated successfully.',
            'data' => $job,
        ], 200);
    }

    /**
     * Delete a job posting owned by the corporate user.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role_id !== 2) {
            return response()->json(['message' => 'Only corporate users can delete jobs.'], 403);
        }

        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found.'], 404);
        }

        if ($job->company_id !== $user->company->id) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully.'], 200);
    }
    public function show(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role_id !== 2) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found.'], 404);
        }

        if ($job->company_id !== $user->company->id) {
            return response()->json(['message' => 'Unauthorized access to this job.'], 403);
        }   
        return response()->json(['data' => $job]);
    }
    
}
