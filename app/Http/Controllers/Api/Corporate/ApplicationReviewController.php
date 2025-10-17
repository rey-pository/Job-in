<?php

namespace App\Http\Controllers\Api\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;

class ApplicationReviewController extends Controller
{
    // Menampilkan semua pelamar dari lowongan milik perusahaan
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role_id !== 2) {
            return response()->json(['message' => 'Only corporate users can view applications.'], 403);
        }

        $company = $user->company;
        if (!$company) {
            return response()->json(['message' => 'Company profile not found.'], 404);
        }

        $applications = Application::with([
                'job',
                'user.educationHistories',
                'user.workExperiences',
                'user.organizationExperiences',
                'user.portfolioHistories'
            ])
            ->whereHas('job', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->orderByDesc('created_at')
            ->get();

        $data = $applications->map(function ($a) {
            $candidate = $a->user;

            return [
                'application_id' => $a->id,
                'status' => $a->status,
                'applied_at' => $a->created_at->format('Y-m-d H:i'),
                'job' => [
                    'id' => $a->job->id ?? null,
                    'title' => $a->job->title ?? null,
                    'department' => $a->job->department ?? null,
                ],
                'candidate' => [
                    'id' => $candidate->id ?? null,
                    'name' => $candidate->name ?? $a->name,
                    'email' => $candidate->email ?? $a->email,
                    'phone' => $candidate->phone_number ?? $a->phone,
                    'avatar' => $candidate->avatar ?? null,
                    'status_verifikasi' => $candidate->status_verifikasi ?? null,
                    'education_histories' => $candidate->educationHistories ?? [],
                    'work_experiences' => $candidate->workExperiences ?? [],
                    'organization_experiences' => $candidate->organizationExperiences ?? [],
                    'portfolio_histories' => $candidate->portfolioHistories ?? [],
                ],
            ];
        });

        return response()->json([
            'message' => 'Applications retrieved successfully.',
            'total' => $data->count(),
            'data' => $data
        ], 200);
    }

    // Menampilkan detail 1 pelamar dengan semua pengalaman
    public function show(Request $request, $application_id)
    {
        $user = $request->user();

        if ($user->role_id !== 2) {
            return response()->json(['message' => 'Only corporate users can view candidate details.'], 403);
        }

        $application = Application::with([
            'user.educationHistories',
            'user.workExperiences',
            'user.organizationExperiences',
            'user.portfolioHistories',
            'job.company'
        ])->find($application_id);

        if (!$application) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        if ($application->job->company->id !== $user->company->id) {
            return response()->json(['message' => 'Unauthorized access to this application.'], 403);
        }

        $candidate = $application->user;

        return response()->json([
            'message' => 'Candidate details retrieved successfully.',
            'application' => [
                'id' => $application->id,
                'status' => $application->status,
                'applied_at' => $application->created_at->format('Y-m-d H:i'),
                'job_title' => $application->job->title,
            ],
            'candidate' => [
                'id' => $candidate->id ?? null,
                'name' => $candidate->name ?? $application->name,
                'email' => $candidate->email ?? $application->email,
                'phone_number' => $candidate->phone_number ?? $application->phone,
                'avatar' => $candidate->avatar ?? null,
                'status_verifikasi' => $candidate->status_verifikasi ?? null,
            ],
            'education_histories' => $candidate->educationHistories ?? [],
            'work_experiences' => $candidate->workExperiences ?? [],
            'organization_experiences' => $candidate->organizationExperiences ?? [],
            'portfolio_histories' => $candidate->portfolioHistories ?? [],
        ]);
    }

    // Update status lamaran (misal dari submitted ke reviewing)
    public function updateStatus(Request $request, $application_id)
    {
        $user = $request->user();

        if ($user->role_id !== 2) {
            return response()->json(['message' => 'Only corporate users can update application status.'], 403);
        }

        $application = Application::with(['job.company'])->find($application_id);

        if (!$application) {
            return response()->json(['message' => 'Application not found.'], 404);
        }

        if ($application->job->company->id !== $user->company->id) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:submitted,reviewing,shortlisted,interview,hired,rejected'
        ]);

        $application->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Application status updated successfully.',
            'data' => [
                'application_id' => $application->id,
                'status' => $application->status,
            ]
        ], 200);
    }
}
