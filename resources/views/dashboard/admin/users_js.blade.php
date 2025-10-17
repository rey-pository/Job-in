<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('usersTableBody');
    const paginationControls = document.getElementById('paginationControls');
    const userModal = document.getElementById('userModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const addUserBtn = document.getElementById('addUserBtn');
    const userForm = document.getElementById('userForm');
    const modalTitle = document.getElementById('modalTitle');
    const statusWrapper = document.getElementById('status-wrapper');
    const viewModal = document.getElementById('viewModal');
    const closeViewModalBtn = document.getElementById('closeViewModalBtn');
    const userDetails = document.getElementById('userDetails');
    const deleteModal = document.getElementById('deleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const deleteMessage = document.getElementById('deleteMessage');
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

    async function fetchUsers(page = 1, searchTerm = '') {
        try {
            let endpoint = `/all-users?page=${page}`;
            if (searchTerm) endpoint += `&search=${encodeURIComponent(searchTerm)}`;
            const response = await apiRequest(endpoint);
            tableBody.innerHTML = ''; 
            if (!response.data || response.data.length === 0) {
                 tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4">No users found.</td></tr>`;
                 paginationControls.innerHTML = '';
                 return;
            }
            response.data.forEach(user => {
                const roleMap = { 1: 'Admin', 2: 'Corporate', 3: 'Jobseeker' };
                const statusBadge = user.status_verifikasi === 'approved' 
                    ? `<span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">${user.status_verifikasi}</span>`
                    : `<span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs">${user.status_verifikasi || 'N/A'}</span>`;
                const row = `<tr><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">${user.name}</td><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p>${user.email}</p><p class="text-gray-500 text-xs">${user.phone_number || ''}</p></td><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">${roleMap[user.role_id] || 'Unknown'}</td><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">${statusBadge}</td><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center"><button class="info-btn text-blue-600 hover:text-blue-900" data-id="${user.id}">Info</button><button class="edit-btn text-yellow-600 hover:text-yellow-900 ml-2" data-id="${user.id}">Edit</button><button class="delete-btn text-red-600 hover:text-red-900 ml-2" data-id="${user.id}" data-name="${user.name}">Delete</button></td></tr>`;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
            renderPagination(response);
        } catch (error) {
            console.error('Failed to fetch users:', error);
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-500">Failed to load data.</td></tr>`;
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
            fetchUsers(1, searchInput.value);
        }, 500);
    });

    paginationControls.addEventListener('click', function(e) {
        if (e.target.classList.contains('pagination-btn') && e.target.dataset.page) {
            fetchUsers(e.target.dataset.page, searchInput.value);
        }
    });

    tableBody.addEventListener('click', function(e) {
        const target = e.target;
        const id = target.dataset.id;
        if (target.classList.contains('info-btn')) handleInfo(id);
        else if (target.classList.contains('edit-btn')) handleEdit(id);
        else if (target.classList.contains('delete-btn')) handleDelete(id, target.dataset.name);
    });

    addUserBtn.addEventListener('click', () => {
        userForm.reset();
        modalTitle.textContent = 'Add New User';
        userForm.dataset.mode = 'create';
        userForm.querySelector('#userId').value = '';
        userForm.querySelector('#password').placeholder = 'Required';
        userForm.querySelector('#role_id').disabled = false;
        statusWrapper.classList.remove('hidden');
        userModal.classList.remove('hidden');
    });

    userForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = userForm.querySelector('#userId').value;
        const mode = userForm.dataset.mode;
        const formData = new FormData(userForm);
        const data = Object.fromEntries(formData.entries());
        
        let url = '/create-user';
        let method = 'POST';

        if (mode === 'update') {
            url = `/update-user/${id}`;
            method = 'PUT';
            if (!data.password) {
                delete data.password;
            }
        }

        try {
            await apiRequest(url, method, data);
            userModal.classList.add('hidden');
            fetchUsers(1, searchInput.value);
            alert(`User ${mode === 'create' ? 'created' : 'updated'} successfully!`);
        } catch (error) {
            alert('Failed to save user: ' + error.message);
            console.error('Failed to save user:', error);
        }
    });

    async function handleInfo(id) {
        try {
            const response = await apiRequest(`/user/${id}`);
            const user = response.data;
            userDetails.innerHTML = `<p><strong>ID:</strong> ${user.id}</p><p><strong>Name:</strong> ${user.name}</p><p><strong>Email:</strong> ${user.email}</p><p><strong>Phone:</strong> ${user.phone_number || 'N/A'}</p><p><strong>Role:</strong> ${user.role.name}</p><p><strong>Status:</strong> ${user.status_verifikasi || 'N/A'}</p><p><strong>Joined:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>`;
            viewModal.classList.remove('hidden');
        } catch (error) {
            console.error('Failed to fetch user details:', error);
        }
    }

    async function handleEdit(id) {
        try {
            const response = await apiRequest(`/user/${id}`);
            const user = response.data;
            modalTitle.textContent = 'Edit User';
            userForm.dataset.mode = 'update';
            userForm.querySelector('#userId').value = user.id;
            userForm.querySelector('#name').value = user.name;
            userForm.querySelector('#email').value = user.email;
            userForm.querySelector('#phone_number').value = user.phone_number || '';
            userForm.querySelector('#password').value = '';
            userForm.querySelector('#password').placeholder = 'Leave blank to keep unchanged';
            userForm.querySelector('#role_id').value = user.role_id;
            userForm.querySelector('#role_id').disabled = false;
            statusWrapper.classList.remove('hidden');
            userForm.querySelector('#status_verifikasi').value = user.status_verifikasi || 'pending';
            userModal.classList.remove('hidden');
        } catch (error) {
            console.error('Failed to fetch user for editing:', error);
        }
    }
    
    function handleDelete(id, name) {
        deleteMessage.textContent = `Are you sure you want to delete user "${name}"? This action cannot be undone.`;
        confirmDeleteBtn.dataset.id = id;
        deleteModal.classList.remove('hidden');
    }

    confirmDeleteBtn.addEventListener('click', async function() {
        const id = this.dataset.id;
        try {
            await apiRequest(`/delete-user/${id}`, 'DELETE');
            deleteModal.classList.add('hidden');
            fetchUsers(1, searchInput.value);
            alert('User deleted successfully!');
        } catch (error) {
            console.error('Failed to delete user:', error);
        }
    });

    function closeAllModals() {
        userModal.classList.add('hidden');
        viewModal.classList.add('hidden');
        deleteModal.classList.add('hidden');
    }
    
    closeModalBtn.addEventListener('click', closeAllModals);
    closeViewModalBtn.addEventListener('click', closeAllModals);
    cancelDeleteBtn.addEventListener('click', closeAllModals);
    
    fetchUsers();
});
</script>