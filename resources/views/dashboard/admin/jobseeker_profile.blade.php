@extends('layouts.admin')

@section('content')
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.jobseekers') }}" class="text-indigo-600 hover:text-indigo-800 mr-4">&larr; Back</a>
        <h1 class="text-3xl font-bold">Manage Profile: {{ $jobseeker->name }}</h1>
    </div>

    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Education</h2>
            <button class="add-btn bg-indigo-500 text-white py-1 px-3 rounded" data-type="education" data-user-id="{{ $jobseeker->id }}">+ Add</button>
        </div>
        <div id="education-list"></div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Work Experience</h2>
            <button class="add-btn bg-indigo-500 text-white py-1 px-3 rounded" data-type="work-experience" data-user-id="{{ $jobseeker->id }}">+ Add</button>
        </div>
        <div id="work-experience-list"></div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Organization Experience</h2>
            <button class="add-btn bg-indigo-500 text-white py-1 px-3 rounded" data-type="organization-experience" data-user-id="{{ $jobseeker->id }}">+ Add</button>
        </div>
        <div id="organization-experience-list"></div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Portfolio</h2>
            <button class="add-btn bg-indigo-500 text-white py-1 px-3 rounded" data-type="portfolio" data-user-id="{{ $jobseeker->id }}">+ Add</button>
        </div>
        <div id="portfolio-list"></div>
    </div>
    
    <div id="profileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 id="modalTitle" class="text-2xl font-bold"></h3>
                <button id="closeModalBtn" class="text-black cursor-pointer z-50">&times;</button>
            </div>
            <form id="profileForm" class="mt-4 max-h-[75vh] overflow-y-auto p-1"></form>
        </div>
    </div>

    @include('dashboard.admin.jobseeker_profile_js')
@endsection