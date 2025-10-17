<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk atau Daftar - Job-In</title>
    @vite(['resources/css/app.css'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md" x-data="{ tab: 'login', role: '3' }">
        
        <div class="flex border-b mb-6">
            <button @click="tab = 'login'" :class="{ 'border-indigo-600 text-indigo-600': tab === 'login', 'border-transparent text-gray-500': tab !== 'login' }" class="w-1/2 py-3 font-semibold border-b-2 focus:outline-none">
                Masuk
            </button>
            <button @click="tab = 'register'" :class="{ 'border-indigo-600 text-indigo-600': tab === 'register', 'border-transparent text-gray-500': tab !== 'register' }" class="w-1/2 py-3 font-semibold border-b-2 focus:outline-none">
                Daftar
            </button>
        </div>

        @if (session('status'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div x-show="tab === 'login'">
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email / No. Telepon</label>
                    <input type="text" name="login" class="border rounded w-full p-2" value="{{ old('login') }}" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" class="border rounded w-full p-2" required>
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg">
                    Masuk
                </button>
            </form>
        </div>

        <div x-show="tab === 'register'">
            <form method="POST" action="{{ route('register.submit') }}">
                @csrf
                <input type="hidden" name="role_id" x-model="role">

                <h2 class="text-xl font-bold text-center mb-4" x-text="role === '3' ? 'Daftar Sebagai Pencari Kerja' : 'Daftarkan Perusahaan Anda'"></h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1" x-text="role === '3' ? 'Nama Lengkap' : 'Nama Perusahaan'"></label>
                    <input type="text" name="name" class="border rounded w-full p-2" value="{{ old('name') }}" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" class="border rounded w-full p-2" value="{{ old('email') }}" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" class="border rounded w-full p-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="border rounded w-full p-2" required>
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg">
                    Daftar Sekarang
                </button>

                <div class="mt-4 text-center text-sm">
                    <div x-show="role === '3'">
                        <p class="text-gray-600">
                            Ingin daftarkan perusahaanmu?
                            <a href="#" @click.prevent="role = '2'" class="font-medium text-indigo-600 hover:underline">
                                Klik disini
                            </a>
                        </p>
                    </div>
                    <div x-show="role === '2'" style="display: none;">
                        <p class="text-gray-600">
                            Bukan perusahaan?
                            <a href="#" @click.prevent="role = '3'" class="font-medium text-indigo-600 hover:underline">
                                Daftar sebagai Pencari Kerja
                            </a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>