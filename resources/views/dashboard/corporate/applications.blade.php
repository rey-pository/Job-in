@extends('layouts.corporate')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Candidate Review</h1>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Candidate</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Applied For</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody x-data>
                @forelse($applications as $application)
                    <tr>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <p class="font-semibold text-gray-900 whitespace-no-wrap">{{ $application->user->name ?? $application->name }}</p>
                            <p class="text-gray-600 whitespace-no-wrap">{{ $application->user->email ?? $application->email }}</p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $application->job->title ?? 'Lowongan Dihapus' }}</p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <select @change="updateStatus({{ $application->id }}, $event.target.value)" class="border p-1 rounded-md text-sm bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="submitted" @selected($application->status == 'submitted')>Submitted</option>
                                <option value="reviewing" @selected($application->status == 'reviewing')>Reviewing</option>
                                <option value="shortlisted" @selected($application->status == 'shortlisted')>Shortlisted</option>
                                <option value="interview" @selected($application->status == 'interview')>Interview</option>
                                <option value="hired" @selected($application->status == 'hired')>Hired</option>
                                <option value="rejected" @selected($application->status == 'rejected')>Rejected</option>
                            </select>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center">
                            <a href="{{ route('corporate.applications.show', $application->id) }}" class="text-indigo-600 hover:text-indigo-900">Lihat Selengkapnya</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-10 text-gray-500">
                            Belum ada pelamar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        {{ $applications->links() }}
    </div>

<script>
    async function updateStatus(applicationId, newStatus) {
        try {
            const response = await fetch(`/api/corporate/applications/${applicationId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include',
                body: JSON.stringify({ status: newStatus })
            });
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Gagal memperbarui status.');
            }
            // Tidak perlu alert agar pengalaman pengguna lebih lancar
            // Cukup biarkan dropdown menampilkan status baru
        } catch (error) {
            alert(error.message);
            console.error(error);
            // Jika gagal, muat ulang halaman untuk mengembalikan dropdown ke status semula
            window.location.reload();
        }
    }
</script>
@endsection