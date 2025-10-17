<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100">
    <div class="flex">
        <aside class="w-64 min-h-screen bg-gray-800 text-white flex flex-col justify-between">
            <div>
                <div class="p-4 font-bold text-lg border-b border-gray-700">Job Portal Admin</div>
                <nav class="mt-4">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 hover:bg-gray-700">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>
                        <span class="ml-3">Beranda</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 hover:bg-gray-700">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                        <span class="ml-3">Manage Users</span>
                    </a>
                    <a href="{{ route('admin.jobs') }}" class="flex items-center px-4 py-3 hover:bg-gray-700">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 000 2h4a1 1 0 100-2H7z" clip-rule="evenodd" /></svg>
                        <span class="ml-3">Manage Jobs</span>
                    </a>
                    <a href="{{ route('admin.jobseekers') }}" class="flex items-center px-4 py-3 hover:bg-gray-700">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0115 16h1a1 1 0 011 1v.1a1 1 0 01-1 1h-2.07zM4.07 17H2a1 1 0 01-1-1v-.1a1 1 0 011-1h1a5 5 0 013.43-4.33A6.97 6.97 0 007.5 16c0 .34.024.673.07 1H4.07zM10 12a4 4 0 110-8 4 4 0 010 8z" /></svg>
                        <span class="ml-3">Manage Jobseekers</span>
                    </a>
                    <a href="{{ route('admin.corporates') }}" class="flex items-center px-4 py-3 hover:bg-gray-700">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" /></svg>
                        <span class="ml-3">Manage Corporates</span>
                    </a>
                </nav>
            </div>
            <div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left flex items-center px-4 py-3 hover:bg-gray-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        <span class="ml-3">Logout</span>
                    </button>
                </form>
            </div>
        </aside>
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>
</body>
</html>