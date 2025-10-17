<script>
document.addEventListener('DOMContentLoaded', function () {
    const userId = "{{ $jobseeker->id }}";
    const modal = document.getElementById('profileModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('profileForm');
    const API_BASE_URL = '/api/admin';

    const templates = {
        education: {
            list: item => `<div class="border-t py-3" id="education-${item.id}"><div class="flex justify-between items-start"><div><p class="font-bold text-lg">${item.degree}</p><p class="text-gray-600">${item.institution}</p><p class="text-gray-500 text-sm">${item.start_year} - ${item.end_year || 'Present'}</p></div><div><button class="edit-btn text-yellow-600" data-type="education" data-id="${item.id}">Edit</button><button class="delete-btn text-red-600 ml-2" data-type="education" data-id="${item.id}">Delete</button></div></div></div>`,
            form: (item = {}) => `<input type="hidden" name="id" value="${item.id || ''}"><div class="mb-4"><label class="block">Institution</label><input type="text" name="institution" value="${item.institution || ''}" class="w-full border p-2 rounded" required></div><div class="mb-4"><label class="block">Degree</label><input type="text" name="degree" value="${item.degree || ''}" class="w-full border p-2 rounded" required></div><div class="grid grid-cols-2 gap-4"><div class="mb-4"><label class="block">Start Year</label><input type="number" name="start_year" value="${item.start_year || ''}" class="w-full border p-2 rounded" required></div><div class="mb-4"><label class="block">End Year</label><input type="number" name="end_year" value="${item.end_year || ''}" class="w-full border p-2 rounded"></div></div><button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded">Save</button>`
        },
        'work-experience': {
            list: item => `<div class="border-t py-3" id="work-experience-${item.id}"><div class="flex justify-between items-start"><div><p class="font-bold text-lg">${item.position}</p><p class="text-gray-600">${item.company_name}</p><p class="text-gray-500 text-sm">${item.start_date} - ${item.end_date || 'Present'}</p></div><div><button class="edit-btn text-yellow-600" data-type="work-experience" data-id="${item.id}">Edit</button><button class="delete-btn text-red-600 ml-2" data-type="work-experience" data-id="${item.id}">Delete</button></div></div></div>`,
            form: (item = {}) => `<input type="hidden" name="id" value="${item.id || ''}"><div class="mb-4"><label class="block">Company Name</label><input type="text" name="company_name" value="${item.company_name || ''}" class="w-full border p-2 rounded" required></div><div class="mb-4"><label class="block">Position</label><input type="text" name="position" value="${item.position || ''}" class="w-full border p-2 rounded" required></div><div class="grid grid-cols-2 gap-4"><div class="mb-4"><label class="block">Start Date</label><input type="date" name="start_date" value="${item.start_date || ''}" class="w-full border p-2 rounded" required></div><div class="mb-4"><label class="block">End Date</label><input type="date" name="end_date" value="${item.end_date || ''}" class="w-full border p-2 rounded"></div></div><button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded">Save</button>`
        },
        'organization-experience': {
            list: item => `<div class="border-t py-3" id="organization-experience-${item.id}"><div class="flex justify-between items-start"><div><p class="font-bold text-lg">${item.role}</p><p class="text-gray-600">${item.organization_name}</p><p class="text-gray-500 text-sm">${item.start_year} - ${item.end_year || 'Present'}</p></div><div><button class="edit-btn text-yellow-600" data-type="organization-experience" data-id="${item.id}">Edit</button><button class="delete-btn text-red-600 ml-2" data-type="organization-experience" data-id="${item.id}">Delete</button></div></div></div>`,
            form: (item = {}) => `<input type="hidden" name="id" value="${item.id || ''}"><div class="mb-4"><label class="block">Organization Name</label><input type="text" name="organization_name" value="${item.organization_name || ''}" class="w-full border p-2 rounded" required></div><div class="mb-4"><label class="block">Role / Position</label><input type="text" name="role" value="${item.role || ''}" class="w-full border p-2 rounded" required></div><div class="grid grid-cols-2 gap-4"><div class="mb-4"><label class="block">Start Year</label><input type="number" name="start_year" value="${item.start_year || ''}" class="w-full border p-2 rounded" required></div><div class="mb-4"><label class="block">End Year</label><input type="number" name="end_year" value="${item.end_year || ''}" class="w-full border p-2 rounded"></div></div><div class="mb-4"><label class="block">Description</label><textarea name="description" class="w-full border p-2 rounded">${item.description || ''}</textarea></div><button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded">Save</button>`
        },
        portfolio: {
            list: item => `<div class="border-t py-3" id="portfolio-${item.id}"><div class="flex justify-between items-start"><div><p class="font-bold text-lg">${item.title} <span class="text-sm font-normal text-gray-500">(${item.type})</span></p><p class="text-gray-600">${item.issuer || ''}</p><p class="text-gray-500 text-sm">${item.date}</p>${item.attachment ? `<a href="${item.attachment}" target="_blank" class="text-indigo-500 text-sm">View Attachment</a>` : ''}</div><div><button class="edit-btn text-yellow-600" data-type="portfolio" data-id="${item.id}">Edit</button><button class="delete-btn text-red-600 ml-2" data-type="portfolio" data-id="${item.id}">Delete</button></div></div></div>`,
            form: (item = {}) => `<input type="hidden" name="id" value="${item.id || ''}"><div class="mb-4"><label class="block">Title</label><input type="text" name="title" value="${item.title || ''}" class="w-full border p-2 rounded" required></div><div class="grid grid-cols-2 gap-4"><div class="mb-4"><label class="block">Type</label><input type="text" name="type" placeholder="e.g., Certificate, Project" value="${item.type || ''}" class="w-full border p-2 rounded" required></div><div class="mb-4"><label class="block">Date</label><input type="date" name="date" value="${item.date || ''}" class="w-full border p-2 rounded" required></div></div><div class="mb-4"><label class="block">Issuer</label><input type="text" name="issuer" placeholder="e.g., Google, Personal Project" value="${item.issuer || ''}" class="w-full border p-2 rounded"></div><div class="mb-4"><label class="block">Attachment URL</label><input type="text" name="attachment" value="${item.attachment || ''}" class="w-full border p-2 rounded"></div><div class="mb-4"><label class="block">Description</label><textarea name="description" class="w-full border p-2 rounded">${item.description || ''}</textarea></div><button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded">Save</button>`
        }
    };

    let profileData = {};

    async function apiRequest(endpoint, method = 'GET', body = null) {
        const config = { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, credentials: 'include' };
        if (method !== 'GET' && body) {
            config.body = JSON.stringify(body);
        }
        const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'An error occurred');
        }
        return response.json();
    }

    function renderSection(type, data) {
        const container = document.getElementById(`${type}-list`);
        container.innerHTML = data.length > 0 ? data.map(templates[type].list).join('') : '<p class="text-gray-500">No data available.</p>';
    }

    async function loadProfile() {
        try {
            const response = await apiRequest(`/jobseekers/${userId}/profile`);
            profileData = { 
                education: response.education_histories, 
                'work-experience': response.work_experiences,
                'organization-experience': response.organization_experiences,
                portfolio: response.portfolio_histories
            };
            renderSection('education', profileData.education);
            renderSection('work-experience', profileData['work-experience']);
            renderSection('organization-experience', profileData['organization-experience']);
            renderSection('portfolio', profileData.portfolio);
        } catch (error) {
            console.error('Failed to load profile:', error);
        }
    }

    document.body.addEventListener('click', async e => {
        const type = e.target.dataset.type;
        const id = e.target.dataset.id;
        
        if (e.target.classList.contains('add-btn')) {
            modalTitle.textContent = `Add ${type.replace(/-/g, ' ')}`;
            form.innerHTML = templates[type].form();
            form.dataset.type = type;
            form.dataset.mode = 'add';
            modal.classList.remove('hidden');
        }

        if (e.target.classList.contains('edit-btn')) {
            const item = profileData[type].find(i => i.id == id);
            modalTitle.textContent = `Edit ${type.replace(/-/g, ' ')}`;
            form.innerHTML = templates[type].form(item);
            form.dataset.type = type;
            form.dataset.mode = 'edit';
            modal.classList.remove('hidden');
        }

        if (e.target.classList.contains('delete-btn')) {
            if (confirm(`Are you sure you want to delete this ${type.replace('-', ' ')} record?`)) {
                try {
                    await apiRequest(`/${type}/${id}`, 'DELETE');
                    loadProfile();
                } catch (error) {
                    alert('Failed to delete record.');
                }
            }
        }
    });

    form.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        const { type, mode } = e.target.dataset;
        const id = data.id;
        
        try {
            if (mode === 'add') {
                await apiRequest(`/jobseekers/${userId}/${type}`, 'POST', data);
            } else {
                await apiRequest(`/${type}/${id}`, 'PUT', data);
            }
            modal.classList.add('hidden');
            loadProfile();
        } catch (error) {
            alert('Failed to save record.');
        }
    });

    document.getElementById('closeModalBtn').addEventListener('click', () => modal.classList.add('hidden'));
    
    loadProfile();
});
</script>