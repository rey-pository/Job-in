<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('jobsTableBody');
    const paginationControls = document.getElementById('paginationControls');
    const jobModal = document.getElementById('jobModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const jobForm = document.getElementById('jobForm');
    const deleteModal = document.getElementById('deleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const addJobBtn = document.getElementById('addJobBtn');
    const modalTitle = document.getElementById('modalTitle');
    const companySelect = document.getElementById('company_id');
    const searchInput = document.getElementById('searchInput');
    const API_BASE_URL = '/api/admin';
    
    let debounceTimer;

    async function apiRequest(endpoint, method = 'GET', body = null) {
        const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') };
        const config = { method, headers, credentials: 'include' };
        if (method !== 'GET' && body) config.body = JSON.stringify(body);
        const response = await fetch(API_BASE_URL + endpoint, config);
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Something went wrong');
        }
        return response.json();
    }

    async function populateCompanyDropdown() {
        try {
            const companies = await apiRequest('/companies-list');
            companySelect.innerHTML = '<option value="">Pilih Perusahaan</option>';
            companies.forEach(company => {
                const option = document.createElement('option');
                option.value = company.id;
                option.textContent = company.name;
                companySelect.appendChild(option);
            });
        } catch (error) {
            console.error('Failed to load companies:', error);
        }
    }

    async function fetchJobs(page = 1, searchTerm = '') {
        try {
            let endpoint = `/jobs?page=${page}`;
            if (searchTerm) {
                endpoint += `&search=${encodeURIComponent(searchTerm)}`;
            }
            const response = await apiRequest(endpoint);
            tableBody.innerHTML = '';
            if (!response.data || response.data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4">No jobs found.</td></tr>`;
                paginationControls.innerHTML = '';
                return;
            }
            response.data.forEach(job => {
                const statusColors = { active: 'green', expired: 'gray' };
                const color = statusColors[job.status] || 'gray';
                const row = `<tr><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p class="font-semibold">${job.title}</p><p class="text-gray-500 text-xs">${job.department || ''}</p></td><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">${job.company ? job.company.name : 'N/A'}</td><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><span class="bg-${color}-200 text-${color}-800 py-1 px-3 rounded-full text-xs">${job.status}</span></td><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center"><button class="edit-btn text-yellow-600 hover:text-yellow-900" data-id="${job.id}">Edit</button><button class="delete-btn text-red-600 hover:text-red-900 ml-2" data-id="${job.id}" data-title="${job.title}">Delete</button></td></tr>`;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
            renderPagination(response);
        } catch (error) {
            console.error('Failed to fetch jobs:', error);
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-red-500">Failed to load data.</td></tr>`;
        }
    }
    
    function renderPagination(response) {
        const { from, to, total, links } = response;
        paginationControls.innerHTML = '';
        let info = `<span class="text-sm text-gray-500">Showing ${from} to ${to} of ${total} results</span>`;
        let buttons = '<div class="flex space-x-1">';
        links.forEach(link => {
            const label = link.label.replace('&laquo; Previous', 'Prev').replace('Next &raquo;', 'Next');
            if (link.url === null) {
                buttons += `<span class="px-3 py-1 text-gray-400 cursor-not-allowed">${label}</span>`;
            } else {
                const pageNumber = new URL(link.url).searchParams.get('page');
                const activeClass = link.active ? 'bg-indigo-600 text-white' : 'bg-white hover:bg-gray-100 text-gray-700';
                buttons += `<button class="pagination-btn px-3 py-1 border rounded ${activeClass}" data-page="${pageNumber}">${label}</button>`;
            }
        });
        buttons += '</div>';
        paginationControls.innerHTML = `${info}${buttons}`;
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const searchTerm = searchInput.value;
            fetchJobs(1, searchTerm);
        }, 500); 
    });

    paginationControls.addEventListener('click', function(e) {
        if (e.target.classList.contains('pagination-btn') && e.target.dataset.page) {
            const searchTerm = searchInput.value;
            fetchJobs(e.target.dataset.page, searchTerm);
        }
    });

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
            const res = await apiRequest(`/jobs/${id}`);
            jobForm.dataset.mode = 'edit';
            modalTitle.textContent = 'Edit Job';
            jobForm.querySelector('#jobId').value = res.data.id;
            jobForm.querySelector('#company_id').value = res.data.company_id;
            jobForm.querySelector('#title').value = res.data.title;
            jobForm.querySelector('#department').value = res.data.department;
            jobForm.querySelector('#description').value = res.data.description;
            jobForm.querySelector('#status').value = res.data.status;
            jobForm.querySelector('#province').value = res.data.province || '';
            jobForm.querySelector('#city').value = res.data.city || '';
            jobForm.querySelector('#work_type').value = res.data.work_type || 'on-site';
            jobForm.querySelector('#published_date').value = res.data.published_date;
            jobForm.querySelector('#expired_date').value = res.data.expired_date;
            jobModal.classList.remove('hidden');
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
        let url = '/jobs';
        let method = 'POST';
        if (mode === 'edit') {
            url = `/jobs/${id}`;
            method = 'PUT';
        }
        try {
            await apiRequest(url, method, data);
            jobModal.classList.add('hidden');
            fetchJobs(1, searchInput.value);
            alert(`Job ${mode === 'create' ? 'created' : 'updated'} successfully!`);
        } catch(error) { 
            alert(`Failed to save job: ${error.message}`);
        }
    });

    confirmDeleteBtn.addEventListener('click', async function() {
        try {
            await apiRequest(`/jobs/${this.dataset.id}`, 'DELETE');
            deleteModal.classList.add('hidden');
            fetchJobs(1, searchInput.value);
            alert('Job deleted successfully!');
        } catch(error) { alert('Failed to delete job: ' + error.message); }
    });
    
    closeModalBtn.addEventListener('click', () => jobModal.classList.add('hidden'));
    cancelDeleteBtn.addEventListener('click', () => deleteModal.classList.add('hidden'));

    populateCompanyDropdown();
    fetchJobs();
});
</script>