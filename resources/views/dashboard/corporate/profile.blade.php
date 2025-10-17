@extends('layouts.corporate')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Company Profile</h1>

    @if (session('status'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm" role="alert">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Wrapper utama dengan Alpine.js --}}
    <div x-data="{ mode: 'view' }">

        <div x-show="mode === 'view'" class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-start space-x-6">
                {{-- Logo Perusahaan --}}
                @if($company && $company->logo)
                    <img src="/storage/{{ $company->logo }}" alt="Current Logo" class="h-24 w-24 object-cover rounded-md border">
                @else
                    <div class="h-24 w-24 bg-gray-200 flex items-center justify-center rounded-md text-gray-500 font-bold text-3xl">
                        {{ substr($company->name ?? 'C', 0, 1) }}
                    </div>
                @endif

                {{-- Detail Perusahaan --}}
                <div class="flex-grow">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $company->name ?? 'Nama Perusahaan Belum Diatur' }}</h2>
                    <p class="text-gray-500">{{ Auth::user()->email }}</p>
                    <p class="text-gray-500">{{ Auth::user()->phone_number }}</p>
                </div>
            </div>

            <div class="mt-6 border-t pt-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Website</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->website ?? 'Belum diatur' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->address ?? 'Belum diatur' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $company->description ?? 'Belum diatur' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Tombol Aksi --}}
            <div class="mt-6 pt-4 border-t flex justify-end space-x-3">
                <button @click="mode = 'changePassword'" type="button" class="bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300">
                    Ubah Password
                </button>
                <button @click="mode = 'editProfile'" type="button" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">
                    Edit Profil Perusahaan
                </button>
            </div>
        </div>

        <div x-show="mode === 'editProfile'" style="display: none;" class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4 border-b pb-3">Update Profile Information</h2>
            <form action="{{ route('corporate.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="logo">Upload New Logo</label>
                    <input type="file" id="logo" name="logo" class="w-full border p-2 rounded" accept="image/*">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="website">Website URL</label>
                    <input type="text" id="website" name="website" value="{{ old('website', $company->website ?? '') }}" class="w-full border p-2 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="address">Address</label>
                    <textarea id="address" name="address" rows="3" class="w-full border p-2 rounded">{{ old('address', $company->address ?? '') }}</textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
                    <textarea id="description" name="description" rows="5" class="w-full border p-2 rounded">{{ old('description', $company->description ?? '') }}</textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button @click.prevent="mode = 'view'" type="button" class="bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300">Batal</button>
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">Update Profile</button>
                </div>
            </form>
        </div>

        <div x-show="mode === 'changePassword'" style="display: none;" class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4 border-b pb-3">Reset Password</h2>
            <form action="{{ route('corporate.profile.reset_password') }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                    <input type="password" name="password" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="w-full border p-2 rounded" required>
                </div>
                <div class="flex justify-end space-x-3">
                    <button @click.prevent="mode = 'view'" type="button" class="bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300">Batal</button>
                    <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">Reset Password</button>
                </div>
            </form>
        </div>

    </div>
@endsection