@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Manage Corporates</h1>
    
    <div class="mb-4">
        <input type="search" id="searchInput" placeholder="Ketik untuk mencari nama atau email..." class="w-full md:w-1/3 border p-2 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Company Name</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email / Phone</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="corporatesTableBody"></tbody>
        </table>
    </div>
    <div id="paginationControls" class="mt-4 flex justify-between items-center"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('corporatesTableBody');
    const paginationControls = document.getElementById('paginationControls');
    const searchInput = document.getElementById('searchInput');
    const API_BASE_URL = '/api/admin';
    let debounceTimer;

    async function apiRequest(endpoint) {
        const headers = { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') };
        const response = await fetch(`${API_BASE_URL}${endpoint}`, { headers, credentials: 'include' });
        if (!response.ok) throw new Error('Failed to fetch data');
        return response.json();
    }

    async function fetchCorporates(page = 1, searchTerm = '') {
        try {
            let endpoint = `/corporates?page=${page}`;
            if (searchTerm) endpoint += `&search=${encodeURIComponent(searchTerm)}`;
            const response = await apiRequest(endpoint);
            tableBody.innerHTML = '';
            if (!response.data || response.data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-4">No corporates found.</td></tr>`;
                paginationControls.innerHTML = '';
                return;
            }
            response.data.forEach(user => {
                const profileUrl = `{{ route('admin.corporate.profile', ['id' => ':id']) }}`.replace(':id', user.id);
                const logoUrl = user.company?.logo ? `/storage/${user.company.logo}` : 'https://placehold.co/40x40/E2E8F0/A0AEC0?text=Logo';
                const row = `<tr><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><div class="flex items-center"><div class="flex-shrink-0 w-10 h-10"><img class="w-full h-full rounded-full object-cover" src="${logoUrl}" alt="Logo" /></div><div class="ml-3"><p class="text-gray-900 whitespace-no-wrap">${user.name}</p></div></div></td><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm"><p>${user.email}</p><p class="text-gray-500 text-xs">${user.phone_number || ''}</p></td><td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center"><a href="${profileUrl}" class="text-indigo-600 hover:text-indigo-900">Manage Profile</a></td></tr>`;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
            renderPagination(response);
        } catch (error) {
            console.error(error);
            tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-red-500">Failed to load data.</td></tr>`;
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
            fetchCorporates(1, searchInput.value);
        }, 500);
    });

    paginationControls.addEventListener('click', function(e) {
        if (e.target.classList.contains('pagination-btn') && e.target.dataset.page) {
            fetchCorporates(e.target.dataset.page, searchInput.value);
        }
    });

    fetchCorporates();
});
</script>
@endsection