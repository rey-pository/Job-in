<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Application;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::where('status', 'active')
                   ->with('company:id,name,logo,address')
                   ->latest('published_date')
                   ->paginate(10);
                   
        return response()->json($jobs);
    }

    public function apply(Request $request, Job $job)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $dataToSave = [
            'job_id' => $job->id,
            'user_id' => null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
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

        return response()->json(['message' => 'Application submitted successfully.'], 201);
    }
}