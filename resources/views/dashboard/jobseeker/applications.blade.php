@extends('layouts.jobseeker')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Status Lamaran Anda</h1>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Posisi / Perusahaan</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Melamar</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $application)
                    @php
                        $statusClasses = [
                            'submitted' => 'bg-gray-200 text-gray-800',
                            'reviewing' => 'bg-blue-200 text-blue-800',
                            'shortlisted' => 'bg-indigo-200 text-indigo-800',
                            'interview' => 'bg-purple-200 text-purple-800',
                            'hired' => 'bg-green-200 text-green-800',
                            'rejected' => 'bg-red-200 text-red-800',
                        ];
                        $statusClass = $statusClasses[$application->status] ?? 'bg-gray-200 text-gray-800';
                    @endphp
                    <tr>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <p class="font-semibold text-gray-900 whitespace-no-wrap">{{ $application->job->title ?? 'Lowongan Dihapus' }}</p>
                            <p class="text-gray-600 whitespace-no-wrap">{{ $application->job->company->name ?? 'Perusahaan Tidak Ditemukan' }}</p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $application->created_at->format('d M Y') }}</p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ ucfirst($application->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-10 text-gray-500">
                            Anda belum pernah melamar pekerjaan apapun.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        {{ $applications->links() }}
    </div>
@endsection