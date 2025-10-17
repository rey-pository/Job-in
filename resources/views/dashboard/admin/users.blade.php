@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Manage Users</h1>
        <button id="addUserBtn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">+ Add User</button>
    </div>

    <div class="mb-4">
        <input type="search" id="searchInput" placeholder="Ketik untuk mencari nama atau email..." class="w-full md:w-1/3 border p-2 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email / Phone</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody"></tbody>
        </table>
    </div>

    <div id="paginationControls" class="mt-4 flex justify-between items-center"></div>

    <div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 id="modalTitle" class="text-2xl font-bold">Add User</h3>
                <button id="closeModalBtn" class="text-black cursor-pointer z-50">&times;</button>
            </div>
            <form id="userForm" class="mt-4">
                <input type="hidden" id="userId" name="id">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" id="name" name="name" type="text" placeholder="John Doe" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" id="email" name="email" type="email" placeholder="user@example.com" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone_number">Phone Number</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" id="phone_number" name="phone_number" type="text" placeholder="08123456789">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" id="password" name="password" type="password" placeholder="Leave blank to keep unchanged">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="role_id">Role</label>
                        <select id="role_id" name="role_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                            <option value="1">Admin</option>
                            <option value="2">Corporate</option>
                            <option value="3">Jobseeker</option>
                        </select>
                    </div>
                    <div id="status-wrapper">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="status_verifikasi">Verification Status</label>
                        <select id="status_verifikasi" name="status_verifikasi" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="viewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
             <div class="flex justify-between items-center border-b pb-3"><h3 class="text-2xl font-bold">User Details</h3><button id="closeViewModalBtn" class="text-black cursor-pointer z-50">&times;</button></div>
            <div id="userDetails" class="mt-4 space-y-2"></div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete User</h3>
                <div class="mt-2 px-7 py-3"><p class="text-sm text-gray-500" id="deleteMessage">Are you sure you want to delete this user?</p></div>
                <div class="items-center px-4 py-3"><button id="cancelDeleteBtn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 mr-2">Cancel</button><button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button></div>
            </div>
        </div>
    </div>

    @include('dashboard.admin.users_js')
@endsection