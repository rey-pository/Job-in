<?php

namespace App\Http\Controllers\Api\Jobseeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;

class JobListedController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::where('status', 'active')->with('company');
        if ($request->filled('search')) {
            $searchTerm = $request->query('search');
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
        }
        if ($request->has('work_types') && is_array($request->query('work_types'))) {
            $workTypes = array_filter($request->query('work_types'));
            if (!empty($workTypes)) {
                 $query->whereIn('work_type', $workTypes);
            }
        }
        $jobs = $query->latest('published_date')->paginate(9);
        return response()->json($jobs);
    }

    public function show(Job $job)
    {
        $job->load('company');
        return response()->json($job);
    }

    public function apply(Request $request, Job $job)
    {
        $user = $request->user();
        if ($user->role_id !== 3) {
            return response()->json(['message' => 'Hanya pencari kerja yang bisa melamar.'], 403);
        }

        $alreadyApplied = Application::where('user_id', $user->id)
                                     ->where('job_id', $job->id)
                                     ->exists();
        if ($alreadyApplied) {
            return response()->json(['message' => 'Anda sudah pernah melamar lowongan ini.'], 409);
        }

        $validated = $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $dataToSave = [
            'job_id' => $job->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone_number,
            'status' => 'submitted',
        ];

        if ($request->hasFile('cv')) {
            $path = $request->file('cv')->store('applications/cvs', 'public');
            $dataToSave['cv_path'] = $path;
        }

        if ($request->hasFile('ktp')) {
            $path = $request->file('ktp')->store('applications/ktps', 'public');
            $dataToSave['ktp_path'] = $path;
        }

        Application::create($dataToSave);
        return response()->json(['message' => 'Lamaran berhasil dikirim.'], 201);
    }
}