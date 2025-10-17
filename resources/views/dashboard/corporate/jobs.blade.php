@extends('layouts.corporate')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Manage Jobs</h1>
        <button id="addJobBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Add Job
        </button>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Job Title</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Published</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="jobsTableBody">
            </tbody>
        </table>
    </div>
    
    <div id="jobModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 id="modalTitle" class="text-2xl font-bold"></h3>
                <button id="closeModalBtn" class="text-black cursor-pointer z-50">&times;</button>
            </div>
            <form id="jobForm" class="mt-4 max-h-[80vh] overflow-y-auto p-1">
                <input type="hidden" id="jobId" name="id">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('jobsTableBody');
    const jobModal = document.getElementById('jobModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const jobForm = document.getElementById('jobForm');
    const deleteModal = document.getElementById('deleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const addJobBtn = document.getElementById('addJobBtn');
    const modalTitle = document.getElementById('modalTitle');
    const API_BASE_URL = '/api/corporate/jobs';

    async function apiRequest(endpoint, method = 'GET', body = null) {
        const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') };
        const config = { method, headers, credentials: 'include' };
        if (method !== 'GET' && body) config.body = JSON.stringify(body);
        const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Something went wrong');
        }
        return response.json();
    }

    async function fetchJobs() {
        try {
            const response = await apiRequest('');
            tableBody.innerHTML = '';
            if (!response.data || response.data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4">No jobs posted yet.</td></tr>`;
                return;
            }
            response.data.forEach(job => {
                const statusColors = { active: 'green', expired: 'gray' };
                const color = statusColors[job.status] || 'gray';
                const row = `<tr><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p class="font-semibold">${job.title}</p><p class="text-gray-500 text-xs">${job.department || ''}</p></td><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><span class="bg-${color}-200 text-${color}-800 py-1 px-3 rounded-full text-xs">${job.status}</span></td><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">${job.published_date}</td><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center"><button class="edit-btn text-yellow-600 hover:text-yellow-900" data-id="${job.id}">Edit</button><button class="delete-btn text-red-600 hover:text-red-900 ml-2" data-id="${job.id}" data-title="${job.title}">Delete</button></td></tr>`;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        } catch (error) {
            console.error('Failed to fetch jobs:', error);
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-red-500">Failed to load data.</td></tr>`;
        }
    }
    
    addJobBtn.addEventListener('click', () => {
        jobForm.reset();
        modalTitle.textContent = 'Add New Job';
        jobForm.querySelector('#jobId').value = '';
        jobForm.dataset.mode = 'create';
        jobModal.classList.remove('hidden');
    });

    tableBody.addEventListener('click', async e => {
        const id = e.target.dataset.id;
        if (e.target.classList.contains('edit-btn')) {
            try {
                const res = await apiRequest(`/${id}`);
                jobForm.dataset.mode = 'edit';
                modalTitle.textContent = 'Edit Job';
                jobForm.querySelector('#jobId').value = res.data.id;
                jobForm.querySelector('#title').value = res.data.title;
                jobForm.querySelector('#department').value = res.data.department;
                jobForm.querySelector('#description').value = res.data.description;
                jobForm.querySelector('#province').value = res.data.province || '';
                jobForm.querySelector('#city').value = res.data.city || '';
                jobForm.querySelector('#work_type').value = res.data.work_type || 'on-site';
                jobForm.querySelector('#published_date').value = res.data.published_date;
                jobForm.querySelector('#expired_date').value = res.data.expired_date;
                jobForm.querySelector('#status').value = res.data.status;
                jobModal.classList.remove('hidden');
            } catch(error) {
                alert('Failed to fetch job details: ' + error.message);
            }
        }
        if (e.target.classList.contains('delete-btn')) {
            document.getElementById('deleteMessage').textContent = `Are you sure you want to delete "${e.target.dataset.title}"?`;
            confirmDeleteBtn.dataset.id = id;
            deleteModal.classList.remove('hidden');
        }
    });

    jobForm.addEventListener('submit', async e => {
        e.preventDefault();
        const id = jobForm.querySelector('#jobId').value;
        const mode = jobForm.dataset.mode;
        const formData = new FormData(jobForm);
        const data = Object.fromEntries(formData.entries());
        
        let url = '';
        let method = 'POST';

        if (mode === 'edit') {
            url = `/${id}`;
            method = 'PUT';
        }

        try {
            await apiRequest(url, method, data);
            jobModal.classList.add('hidden');
            fetchJobs();
            alert(`Job ${mode === 'create' ? 'created' : 'updated'} successfully!`);
        } catch(error) { 
            alert(`Failed to save job: ${error.message}`);
        }
    });

    confirmDeleteBtn.addEventListener('click', async function() {
        try {
            await apiRequest(`/${this.dataset.id}`, 'DELETE');
            deleteModal.classList.add('hidden');
            fetchJobs();
            alert('Job deleted successfully!');
        } catch(error) { alert('Failed to delete job: ' + error.message); }
    });
    
    closeModalBtn.addEventListener('click', () => jobModal.classList.add('hidden'));
    cancelDeleteBtn.addEventListener('click', () => deleteModal.classList.add('hidden'));

    fetchJobs();
});
</script>
@endsection