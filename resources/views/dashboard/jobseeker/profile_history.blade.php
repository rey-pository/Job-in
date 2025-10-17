@extends('layouts.jobseeker')

@section('content')
<div x-data="profileHistoryManager()">
    <h1 class="text-3xl font-bold mb-6">Profile History</h1>

    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Riwayat Pendidikan</h2>
            <button @click="openModal('education', 'add')" class="bg-indigo-500 text-white py-1 px-3 rounded">+ Tambah</button>
        </div>
        <div id="education-list"></div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Pengalaman Kerja</h2>
            <button @click="openModal('work', 'add')" class="bg-indigo-500 text-white py-1 px-3 rounded">+ Tambah</button>
        </div>
        <div id="work-list"></div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Pengalaman Organisasi</h2>
            <button @click="openModal('organization', 'add')" class="bg-indigo-500 text-white py-1 px-3 rounded">+ Tambah</button>
        </div>
        <div id="organization-list"></div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Portofolio</h2>
            <button @click="openModal('portfolio', 'add')" class="bg-indigo-500 text-white py-1 px-3 rounded">+ Tambah</button>
        </div>
        <div id="portfolio-list"></div>
    </div>

    <div x-show="isModalOpen" @click.away="isModalOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.stop>
            <div class="p-6 border-b"><h3 class="text-2xl font-bold" x-text="modalTitle"></h3></div>
            <form @submit.prevent="saveItem($event)">
                <div class="p-6 max-h-[70vh] overflow-y-auto" x-html="modalContent"></div>
                <div class="p-4 bg-gray-50 border-t flex justify-end space-x-3">
                    <button @click="isModalOpen = false" type="button" class="bg-gray-200 py-2 px-4 rounded">Batal</button>
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function profileHistoryManager() {
        return {
            isModalOpen: false, modalTitle: '', modalContent: '',
            modalType: '', modalMode: '', currentItemId: null,
            profileData: {
                education: @json($user->educationHistories),
                work: @json($user->workExperiences),
                organization: @json($user->organizationExperiences),
                portfolio: @json($user->portfolioHistories),
            },
            templates: {
                education: {
                    list: item => `<div class="border-t py-3"><div class="flex justify-between"><div><p class="font-bold">${item.degree}</p><p>${item.institution}</p><p class="text-sm text-gray-500">${item.start_year} - ${item.end_year || 'Now'}</p></div><div><button @click="openModal('education', 'edit', ${item.id})" class="text-yellow-600">Edit</button><button @click="deleteItem('education', ${item.id})" class="text-red-600 ml-2">Delete</button></div></div></div>`,
                    form: (item = {}) => `<div class="space-y-4"><input type="hidden" name="id" value="${item.id || ''}"><div><label>Institusi</label><input type="text" name="institution" value="${item.institution || ''}" class="w-full border p-2 rounded" required></div><div><label>Gelar</label><input type="text" name="degree" value="${item.degree || ''}" class="w-full border p-2 rounded" required></div><div><label>Bidang Studi</label><input type="text" name="field_of_study" value="${item.field_of_study || ''}" class="w-full border p-2 rounded"></div><div class="grid grid-cols-2 gap-4"><div><label>Tahun Mulai</label><input type="number" name="start_year" value="${item.start_year || ''}" class="w-full border p-2 rounded" required></div><div><label>Tahun Selesai</label><input type="number" name="end_year" value="${item.end_year || ''}" class="w-full border p-2 rounded"></div></div></div>`
                },
                work: {
                    list: item => `<div class="border-t py-3"><div class="flex justify-between"><div><p class="font-bold">${item.position}</p><p>${item.company_name}</p><p class="text-sm text-gray-500">${item.start_date} - ${item.end_date || 'Now'}</p></div><div><button @click="openModal('work', 'edit', ${item.id})" class="text-yellow-600">Edit</button><button @click="deleteItem('work', ${item.id})" class="text-red-600 ml-2">Delete</button></div></div></div>`,
                    form: (item = {}) => `<div class="space-y-4"><input type="hidden" name="id" value="${item.id || ''}"><div><label>Nama Perusahaan</label><input type="text" name="company_name" value="${item.company_name || ''}" class="w-full border p-2 rounded" required></div><div><label>Posisi</label><input type="text" name="position" value="${item.position || ''}" class="w-full border p-2 rounded" required></div><div class="grid grid-cols-2 gap-4"><div><label>Tanggal Mulai</label><input type="date" name="start_date" value="${item.start_date || ''}" class="w-full border p-2 rounded" required></div><div><label>Tanggal Selesai</label><input type="date" name="end_date" value="${item.end_date || ''}" class="w-full border p-2 rounded"></div></div></div>`
                },
                organization: {
                    list: item => `<div class="border-t py-3"><div class="flex justify-between"><div><p class="font-bold">${item.role}</p><p>${item.organization_name}</p><p class="text-sm text-gray-500">${item.start_year} - ${item.end_year || 'Now'}</p></div><div><button @click="openModal('organization', 'edit', ${item.id})" class="text-yellow-600">Edit</button><button @click="deleteItem('organization', ${item.id})" class="text-red-600 ml-2">Delete</button></div></div></div>`,
                    form: (item = {}) => `<div class="space-y-4"><input type="hidden" name="id" value="${item.id || ''}"><div><label>Nama Organisasi</label><input type="text" name="organization_name" value="${item.organization_name || ''}" class="w-full border p-2 rounded" required></div><div><label>Peran</label><input type="text" name="role" value="${item.role || ''}" class="w-full border p-2 rounded" required></div><div class="grid grid-cols-2 gap-4"><div><label>Tahun Mulai</label><input type="number" name="start_year" value="${item.start_year || ''}" class="w-full border p-2 rounded" required></div><div><label>Tahun Selesai</label><input type="number" name="end_year" value="${item.end_year || ''}" class="w-full border p-2 rounded"></div></div><div><label>Deskripsi</label><textarea name="description" class="w-full border p-2 rounded">${item.description || ''}</textarea></div></div>`
                },
                portfolio: {
                    list: item => `<div class="border-t py-3"><div class="flex justify-between"><div><p class="font-bold">${item.title} (${item.type})</p><p>${item.issuer || ''} - ${item.date || ''}</p></div><div><button @click="openModal('portfolio', 'edit', ${item.id})" class="text-yellow-600">Edit</button><button @click="deleteItem('portfolio', ${item.id})" class="text-red-600 ml-2">Delete</button></div></div></div>`,
                    form: (item = {}) => `<div class="space-y-4"><input type="hidden" name="id" value="${item.id || ''}"><div><label>Judul</label><input type="text" name="title" value="${item.title || ''}" class="w-full border p-2 rounded" required></div><div class="grid grid-cols-2 gap-4"><div><label>Tipe</label><select name="type" class="w-full border p-2 rounded"><option value="competition" ${item.type === 'competition' ? 'selected' : ''}>Competition</option><option value="certification" ${item.type === 'certification' ? 'selected' : ''}>Certification</option><option value="training" ${item.type === 'training' ? 'selected' : ''}>Training</option><option value="publication" ${item.type === 'publication' ? 'selected' : ''}>Publication</option><option value="achievement" ${item.type === 'achievement' ? 'selected' : ''}>Achievement</option></select></div><div><label>Tanggal</label><input type="date" name="date" value="${item.date || ''}" class="w-full border p-2 rounded"></div></div><div><label>Penerbit</label><input type="text" name="issuer" value="${item.issuer || ''}" class="w-full border p-2 rounded"></div><div><label>Link Lampiran</label><input type="text" name="attachment" value="${item.attachment || ''}" class="w-full border p-2 rounded"></div><div><label>Deskripsi</label><textarea name="description" class="w-full border p-2 rounded">${item.description || ''}</textarea></div></div>`
                }
            },

            init() { this.renderAll(); },
            renderAll() { Object.keys(this.templates).forEach(type => this.renderSection(type)); },
            renderSection(type) {
                const container = document.getElementById(`${type}-list`);
                const data = this.profileData[type];
                container.innerHTML = data.length > 0 ? data.map(item => this.templates[type].list(item)).join('') : '<p class="text-gray-500">Belum ada data.</p>';
            },
            openModal(type, mode, itemId = null) {
                this.modalType = type;
                this.modalMode = mode;
                this.currentItemId = itemId;
                const item = mode === 'edit' ? this.profileData[type].find(i => i.id == itemId) : {};
                this.modalTitle = `${mode === 'edit' ? 'Edit' : 'Tambah'} ${type.replace('-', ' ')}`;
                this.modalContent = this.templates[type].form(item);
                this.isModalOpen = true;
            },
            async saveItem(event) {
                const formData = new FormData(event.target);
                const data = Object.fromEntries(formData.entries());
                const id = this.currentItemId;
                const method = this.modalMode === 'add' ? 'POST' : 'PUT';
                const endpoint = this.modalMode === 'add' ? `/api/jobseeker/${this.modalType}` : `/api/jobseeker/${this.modalType}/${id}`;
                try {
                    const response = await fetch(endpoint, {
                        method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                        credentials: 'include', body: JSON.stringify(data)
                    });
                    if (!response.ok) throw new Error((await response.json()).message || 'Gagal menyimpan.');
                    await this.reloadData(this.modalType);
                    this.isModalOpen = false;
                } catch (error) { alert(error.message); }
            },
            async deleteItem(type, itemId) {
                if (!confirm('Anda yakin ingin menghapus data ini?')) return;
                try {
                    await fetch(`/api/jobseeker/${type}/${itemId}`, {
                        method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, credentials: 'include'
                    });
                    await this.reloadData(type);
                } catch (error) { alert('Gagal menghapus data.'); }
            },
            async reloadData(type) {
                try {
                    const response = await fetch(`/api/jobseeker/${type}`, { headers: {'Accept': 'application/json'}, credentials: 'include' }).then(res => res.json());
                    this.profileData[type] = response.data;
                    this.renderSection(type);
                } catch (error) { console.error(`Gagal memuat ulang data ${type}:`, error); }
            }
        }
    }

    // Listener global untuk menangani klik edit/delete yang dinamis
    document.addEventListener('click', function(e) {
        const alpineComponent = e.target.closest('[x-data]').__x;
        if (e.target.classList.contains('edit-btn')) {
            alpineComponent.openModal(e.target.dataset.type, 'edit', e.target.dataset.id);
        }
        if (e.target.classList.contains('delete-btn')) {
            alpineComponent.deleteItem(e.target.dataset.type, e.target.dataset.id);
        }
    });
</script>
@endsection