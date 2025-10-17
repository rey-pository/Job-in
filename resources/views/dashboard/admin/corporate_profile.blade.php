@extends('layouts.admin')

@section('content')
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.corporates') }}" class="text-indigo-600 hover:text-indigo-800 mr-4">&larr; Back</a>
        <h1 class="text-3xl font-bold">Manage Corporate Profile</h1>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <form id="corporateForm" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Company Name</label>
                <input type="text" value="{{ $corporate->name }}" class="w-full border p-2 rounded bg-gray-100" readonly>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="logo">Upload New Logo</label>
                <input type="file" id="logo" name="logo" class="w-full border p-2 rounded" accept="image/*">
                <img id="logo-preview" class="mt-4 h-24 w-24 object-cover rounded" src="https://placehold.co/96x96/E2E8F0/A0AEC0?text=Logo" alt="Logo Preview">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="website">Website URL</label>
                <input type="text" id="website" name="website" class="w-full border p-2 rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="address">Address</label>
                <textarea id="address" name="address" rows="3" class="w-full border p-2 rounded"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
                <textarea id="description" name="description" rows="5" class="w-full border p-2 rounded"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" id="saveButton" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 disabled:bg-gray-400" disabled>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const userId = "{{ $corporate->id }}";
    const form = document.getElementById('corporateForm');
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logo-preview');
    const saveButton = document.getElementById('saveButton');
    const API_BASE_URL = '/api/admin';

    async function apiRequest(endpoint, method = 'GET', body = null) {
        const headers = { 
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
        };
        const config = { method, headers, credentials: 'include' };
        if (method !== 'GET' && body) {
            if (body instanceof FormData) {
                config.body = body;
            } else {
                headers['Content-Type'] = 'application/json';
                config.body = JSON.stringify(body);
            }
        }
        const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'An error occurred');
        }
        return response.json();
    }

    async function loadProfile() {
        try {
            const response = await apiRequest(`/corporates/${userId}/profile`);
            const company = response.company;
            if (company) {
                form.website.value = company.website || '';
                form.address.value = company.address || '';
                form.description.value = company.description || '';
                updateLogoPreview(company.logo);
            }
        } catch (error) {
            console.error('Failed to load profile:', error);
            // Tetap lanjutkan meskipun gagal memuat, agar user bisa membuat profil baru
        } finally {
            // --- PERBAIKAN #1 ---
            // Tombol Save diaktifkan di sini, jadi akan selalu aktif setelah loading selesai
            saveButton.disabled = false;
        }
    }

    function updateLogoPreview(path) {
        const imageUrl = path ? `/storage/${path}` : 'https://placehold.co/96x96/E2E8F0/A0AEC0?text=Logo';
        logoPreview.src = imageUrl;
    }

    logoInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            logoPreview.src = URL.createObjectURL(file);
        }
    });
    
    logoPreview.addEventListener('error', () => {
        logoPreview.src = 'https://placehold.co/96x96/E2E8F0/A0AEC0?text=Invalid';
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        saveButton.disabled = true;
        saveButton.textContent = 'Saving...';
        const formData = new FormData(form);

        try {
            // --- PERBAIKAN #2 ---
            // Endpoint diubah ke rute "update or create" yang menggunakan userId
            await apiRequest(`/corporates/${userId}/profile`, 'POST', formData);
            alert('Profile saved successfully!');
            window.location.href = "{{ route('admin.corporates') }}";
        } catch (error) {
            alert(`Failed to save profile: ${error.message}`);
            saveButton.disabled = false;
            saveButton.textContent = 'Save Changes';
        }
    });

    loadProfile();
});
</script>
@endsection