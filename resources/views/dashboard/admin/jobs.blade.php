@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Manage Jobs</h1>
        <button id="addJobBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Add Job
        </button>
    </div>

    <div class="mb-4">
        <input type="search" id="searchInput" placeholder="Ketik untuk mencari pekerjaan..." class="w-full md:w-1/3 border p-2 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Job Title</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Company</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="jobsTableBody"></tbody>
        </table>
    </div>
    <div id="paginationControls" class="mt-4 flex justify-between items-center"></div>

    <div id="jobModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 id="modalTitle" class="text-2xl font-bold"></h3>
                <button id="closeModalBtn" class="text-black cursor-pointer z-50">&times;</button>
            </div>
            <form id="jobForm" class="mt-4 max-h-[80vh] overflow-y-auto p-1">
                <input type="hidden" id="jobId" name="id">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="company_id">Perusahaan</label>
                    <select id="company_id" name="company_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required></select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="title">Job Title</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" id="title" name="title" type="text" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="department">Departemen</label>
                    <input type="text" id="department" name="department" class="w-full border p-2 rounded" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="province">Provinsi</label>
                        <input type="text" id="province" name="province" class="w-full border p-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="city">Kota/Kabupaten</label>
                        <input type="text" id="city" name="city" class="w-full border p-2 rounded">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="work_type">Tipe Kerja</label>
                    <select id="work_type" name="work_type" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <option value="on-site">On-Site</option>
                        <option value="remote">Remote</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
                    <textarea id="description" name="description" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required></textarea>
                </div>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="published_date">Tanggal Publikasi</label>
                        <input type="date" id="published_date" name="published_date" class="w-full border p-2 rounded" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="expired_date">Tanggal Kedaluwarsa</label>
                        <input type="date" id="expired_date" name="expired_date" class="w-full border p-2 rounded" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="status">Status</label>
                    <select id="status" name="status" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Job</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="deleteMessage"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="cancelDeleteBtn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 mr-2">Cancel</button>
                    <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @include('dashboard.admin.jobs_js')
@endsection