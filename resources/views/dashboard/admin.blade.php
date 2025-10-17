@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Beranda Dashboard</h1>
    <p class="text-gray-700 mb-8">Selamat datang kembali, {{ $user['name'] }}!</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        
        <div class="bg-white p-6 rounded-lg shadow flex items-center justify-between">
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Total Pengguna</h3>
                <p id="total-users" class="text-3xl font-bold">...</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0115 16h1a1 1 0 011 1v.1a1 1 0 01-1 1h-2.07zM4.07 17H2a1 1 0 01-1-1v-.1a1 1 0 011-1h1a5 5 0 013.43-4.33A6.97 6.97 0 007.5 16c0 .34.024.673.07 1H4.07zM10 12a4 4 0 110-8 4 4 0 010 8z" />
                </svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow flex items-center justify-between">
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Total Perusahaan</h3>
                <p id="total-corporates" class="text-3xl font-bold">...</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow flex items-center justify-between">
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Total Pencari Kerja</h3>
                <p id="total-jobseekers" class="text-3xl font-bold">...</p>
            </div>
            <div class="bg-indigo-100 p-3 rounded-full">
                 <svg class="w-8 h-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow flex items-center justify-between">
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Lowongan Aktif</h3>
                <p id="active-jobs" class="text-3xl font-bold">...</p>
            </div>
            <div class="bg-pink-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-pink-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 000 2h4a1 1 0 100-2H7z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <div class="bg-yellow-100 p-6 rounded-lg shadow flex items-center justify-between">
            <div>
                <h3 class="text-yellow-700 text-sm font-medium">Corporate Pending</h3>
                <p id="pending-corporates" class="text-3xl font-bold text-yellow-800">...</p>
            </div>
            <div class="bg-yellow-200 p-3 rounded-full">
                <svg class="w-8 h-8 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

    </div>

    <div class="bg-white p-6 rounded-lg shadow mt-8">
        <h3 class="text-xl font-bold mb-4">Pendaftaran Pengguna Baru (7 Hari Terakhir)</h3>
        <div>
            <canvas id="userRegistrationChart"></canvas>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const API_BASE_URL = '/api/admin';

    async function apiRequest(endpoint) {
        const headers = { 
            'Accept': 'application/json', 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
        };
        const response = await fetch(`${API_BASE_URL}${endpoint}`, { headers, credentials: 'include' });
        if (!response.ok) {
            throw new Error(`Failed to fetch ${endpoint}`);
        }
        return response.json();
    }

    async function loadDashboardStats() {
        try {
            const stats = await apiRequest('/dashboard-stats');
            document.getElementById('total-users').textContent = stats.total_users ?? '0';
            document.getElementById('total-corporates').textContent = stats.total_corporates ?? '0';
            document.getElementById('total-jobseekers').textContent = stats.total_jobseekers ?? '0';
            document.getElementById('active-jobs').textContent = stats.active_jobs ?? '0';
            document.getElementById('pending-corporates').textContent = stats.pending_corporates ?? '0';
        } catch (error) {
            console.error("Error loading stats:", error);
            document.getElementById('total-users').textContent = 'N/A';
            document.getElementById('total-corporates').textContent = 'N/A';
            document.getElementById('total-jobseekers').textContent = 'N/A';
            document.getElementById('active-jobs').textContent = 'N/A';
            document.getElementById('pending-corporates').textContent = 'N/A';
        }
    }

    async function loadChartData() {
        try {
            const chartData = await apiRequest('/chart-data');
            const ctx = document.getElementById('userRegistrationChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Jobseekers',
                            data: chartData.jobseekers,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            tension: 0.1
                        },
                        {
                            label: 'Corporates',
                            data: chartData.corporates,
                            borderColor: 'rgb(249, 115, 22)',
                            backgroundColor: 'rgba(249, 115, 22, 0.5)',
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error("Error loading chart data:", error);
            const chartContainer = document.getElementById('userRegistrationChart').parentElement;
            chartContainer.innerHTML = '<p class="text-center text-red-500">Gagal memuat data grafik.</p>';
        }
    }

    loadDashboardStats();
    loadChartData();
});
</script>
@endsection