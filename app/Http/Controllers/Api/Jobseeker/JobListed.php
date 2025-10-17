<?php

namespace App\Http\Controllers\Api\Jobseeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;

class JobListed extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $jobs = Job::with('company:id,name,logo,website')
            ->where('status', 'active')
            ->orderByDesc('published_date')
            ->get();

        $response = $jobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'department' => $job->department,
                'description' => $job->description,
                'published_date' => $job->published_date,
                'expired_date' => $job->expired_date,
                'status' => $job->status,
                'company' => [
                    'name' => $job->company->name ?? null,
                    'logo' => $job->company->logo ?? null,
                    'website' => $job->company->website ?? null,
                ],
            ];
        });

        return response()->json([
            'message' => 'Available job listings retrieved successfully.',
            'total' => $response->count(),
            'data' => $response,
        ], 200);
    }
}
