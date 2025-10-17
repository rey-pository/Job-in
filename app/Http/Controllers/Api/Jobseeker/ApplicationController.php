<?php

namespace App\Http\Controllers\Api\Jobseeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;

class ApplicationController extends Controller
{
    /**
     * Jobseeker apply to a specific job
     */
    public function store(Request $request, $job_id)
    {
        $user = $request->user();

        if ($user->role_id != 3) {
            return response()->json(['message' => 'Only jobseekers can apply for jobs.'], 403);
        }

        $job = \App\Models\Job::where('id', $job_id)
            ->where('status', 'active')
            ->first();

        if (!$job) {
            return response()->json(['message' => 'Job not found or not active.'], 404);
        }

        // already applied 
        $alreadyApplied = Application::where('job_id', $job->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyApplied) {
            return response()->json(['message' => 'You have already applied for this job.'], 409);
        }

        $validator = \Validator::make($request->all(), [
            'cv' => 'required|file|mimes:pdf|max:2048',
            'ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // upload file ke storage
        $cvPath = $request->file('cv')->store('applications/cv', 'public');
        $ktpPath = $request->file('ktp')->store('applications/ktp', 'public');

        
        $application = Application::create([
            'job_id' => $job->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $request->phone ?? $user->phone_number,
            'cv_path' => $cvPath,
            'ktp_path' => $ktpPath,
            'status' => 'submitted',
        ]);

        return response()->json([
            'message' => 'Job application submitted successfully.',
            'data' => $application
        ], 201);
    }

    //jobseeker list applied
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role_id != 3) {
            return response()->json(['message' => 'Only jobseekers can view their applications.'], 403);
        }

        // Ambil semua lamaran user + relasi job dan company
        $applications = Application::with(['job.company'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $data = $applications->map(function ($a) {
            return [
                'id' => $a->id,
                'status' => $a->status,
                'applied_at' => $a->created_at->format('Y-m-d H:i:s'),
                'job' => [
                    'id' => $a->job->id,
                    'title' => $a->job->title,
                    'department' => $a->job->department,
                    'company' => $a->job->company->name ?? null,
                ]
            ];
        });

        return response()->json([
            'message' => 'Applications retrieved successfully.',
            'total' => $data->count(),
            'data' => $data
        ], 200);
    }
}
