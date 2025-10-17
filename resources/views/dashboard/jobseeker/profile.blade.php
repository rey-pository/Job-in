@extends('layouts.jobseeker')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Profil Saya</h1>

    @if (session('status'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-6 text-sm" role="alert">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-6 text-sm" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{ mode: 'view' }">
        <div x-show="mode === 'view'" class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-xl font-semibold mb-4 border-b pb-3">Informasi Personal</h2>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Nama Lengkap</label>
                    <p class="mt-1 text-gray-900">{{ Auth::user()->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Email</label>
                    <p class="mt-1 text-gray-900">{{ Auth::user()->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Nomor Telepon</label>
                    <p class="mt-1 text-gray-900">{{ Auth::user()->phone_number ?: 'Belum diatur' }}</p>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t flex justify-end">
                <button @click="mode = 'editProfile'" type="button" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">
                    Edit Profil
                </button>
            </div>
        </div>

        <div x-show="mode === 'editProfile'" style="display: none;" class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-xl font-semibold mb-4 border-b pb-3">Edit Informasi Personal</h2>
            <form action="{{ route('jobseeker.profile.update') }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone_number">Nomor Telepon</label>
                    <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', Auth::user()->phone_number) }}" class="w-full border p-2 rounded">
                </div>
                <div class="flex justify-end space-x-3">
                    <button @click.prevent="mode = 'view'" type="button" class="bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300">Batal</button>
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4 border-b pb-3">Ganti Password</h2>
            <form action="{{ route('jobseeker.profile.password') }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="current_password">Password Lama</label>
                    <input type="password" id="current_password" name="current_password" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="new_password">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" class="w-full border p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="new_password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="w-full border p-2 rounded" required>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">Ubah Password</button>
                </div>
            </form>
        </div>
    </div>
@endsection