@extends('layouts.app')

@section('title', 'Manage Users')

@section('toolbar')
    <button onclick="openUserModal()"
            class="ml-auto px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
        + Add user
    </button>
@endsection

@section('content')
<div class="mt-4 bg-white border border-gray-200 rounded-xl overflow-hidden">
    <table class="w-full text-base">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Username</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Role</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $user->username }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-sm font-medium
                            {{ $user->access_type === 'admin' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($user->access_type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 flex gap-2">
                        <button onclick='openUserModal(@json($user))'
                                class="px-3 py-1 text-base font-medium border border-gray-300 rounded-md hover:bg-gray-100 transition">
                            Edit
                        </button>
                        <button onclick="requestDeleteUser({{ $user->id }}, '{{ $user->username }}')"
                                class="px-3 py-1 text-base font-medium border border-red-300 text-red-600 rounded-md hover:bg-red-50 transition">
                            Delete
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Add/Edit user modal --}}
<div id="user-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden" onclick="closeUserModalOnBackdrop(event)">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative bg-white rounded-xl shadow-lg w-full max-w-md mx-4 z-10 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 id="user-modal-title" class="text-base font-semibold text-gray-800">Add user</h3>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 text-xl font-light leading-none">✕</button>
        </div>
        <div class="px-6 py-4 flex flex-col gap-4">
            <div id="user-modal-error" class="hidden text-base text-red-600 bg-red-50 border border-red-200 rounded-md px-3 py-2"></div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Username</label>
                <input type="text" id="user-username"
                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Password</label>
                <input type="password" id="user-password"
                    class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                <p class="text-sm text-gray-400">Min. 8 characters, with uppercase, lowercase, and a special character.</p>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Role</label>
                <select id="user-access-type"
                        class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2">
            <button onclick="closeUserModal()"
                    class="px-4 py-2 text-base font-medium border border-gray-300 rounded-md hover:bg-gray-100 transition">
                Cancel
            </button>
            <button onclick="saveUser()"
                    class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                Save
            </button>
        </div>
    </div>
</div>

{{-- Delete confirmation modal --}}
<div id="delete-user-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative bg-white rounded-xl shadow-lg w-full max-w-md mx-4 z-10 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Delete user?</h3>
        </div>
        <div class="px-6 py-4">
            <p id="delete-user-message" class="text-base text-gray-600"></p>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2">
            <button onclick="closeDeleteModal()"
                    class="px-4 py-2 text-base font-medium border border-gray-300 rounded-md hover:bg-gray-100 transition">
                Cancel
            </button>
            <button onclick="confirmDeleteUser()"
                    class="px-4 py-2 text-base font-medium bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                Delete user
            </button>
        </div>
    </div>
</div>

<script>
    const csrfToken   = "{{ csrf_token() }}";
    const usersStore  = "{{ route('users.store') }}";
    const usersBase   = "{{ url('/users') }}";
    const currentUserId = {{ auth()->id() }};

    let editingUserId = null;
    let pendingDeleteId = null;

    function openUserModal(user = null) {
        document.getElementById('user-modal-error').classList.add('hidden');
        document.getElementById('user-password').value = '';

        const passwordInput = document.getElementById('user-password');

        if (user) {
            editingUserId = user.id;
            document.getElementById('user-modal-title').textContent = 'Edit user';
            document.getElementById('user-username').value = user.username;
            document.getElementById('user-access-type').value = user.access_type;
            passwordInput.placeholder = 'Leave blank to keep current password';
        } else {
            editingUserId = null;
            document.getElementById('user-modal-title').textContent = 'Add user';
            document.getElementById('user-username').value = '';
            document.getElementById('user-access-type').value = 'staff';
            passwordInput.placeholder = 'Enter password';
        }

        document.getElementById('user-modal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('user-modal').classList.add('hidden');
    }

    function closeUserModalOnBackdrop(event) {
        if (event.target === document.getElementById('user-modal')) closeUserModal();
    }

    async function saveUser() {
        const username    = document.getElementById('user-username').value.trim();
        const password    = document.getElementById('user-password').value;
        const access_type = document.getElementById('user-access-type').value;
        const errorBox     = document.getElementById('user-modal-error');

        errorBox.classList.add('hidden');

        if (!username) {
            showUserModalError('Username is required.');
            return;
        }

        const isEdit = editingUserId !== null;

        // Password required only when adding a new user
        if (!isEdit && !password) {
            showUserModalError('Password is required for new users.');
            return;
        }

        // Client-side strength check (mirrors server-side rule)
        if (password) {
            const strongEnough = password.length >= 8
                && /[a-z]/.test(password)
                && /[A-Z]/.test(password)
                && /[^a-zA-Z0-9]/.test(password);

            if (!strongEnough) {
                showUserModalError('Password must be at least 8 characters and include an uppercase letter, a lowercase letter, and a special character.');
                return;
            }
        }

        const url  = isEdit ? `${usersBase}/${editingUserId}` : usersStore;
        const body = { username, access_type };
        if (password) body.password = password;
        if (isEdit) body._method = 'PUT';

        const res  = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        });

        const data = await res.json();

        if (!res.ok) {
            // Laravel validation errors come back as { errors: { field: [messages] } }
            if (data.errors) {
                const firstError = Object.values(data.errors)[0][0];
                showUserModalError(firstError);
            } else {
                showUserModalError(data.error || 'Something went wrong. Please check your input.');
            }
            return;
        }

        location.reload();
    }

    function showUserModalError(message) {
        const errorBox = document.getElementById('user-modal-error');
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
    }

    function requestDeleteUser(id, username) {
        pendingDeleteId = id;
        document.getElementById('delete-user-message').textContent = `Are you sure you want to delete "${username}"?`;
        checkAndShowDeleteModal(id, username, false);
    }

    async function checkAndShowDeleteModal(id, username, confirmed) {
        const res = await fetch(`${usersBase}/${id}?confirmed=${confirmed}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });

        const data = await res.json();

        if (data.error) {
            alert(data.error);
            return;
        }

        if (data.requiresConfirmation) {
            document.getElementById('delete-user-message').textContent =
                `"${username}" has ${data.count} maintenance log(s) on record. These logs will remain, but will reference a deleted account. Delete anyway?`;
            document.getElementById('delete-user-modal').classList.remove('hidden');
            return;
        }

        if (data.success) {
            location.reload();
        }
    }

    function closeDeleteModal() {
        document.getElementById('delete-user-modal').classList.add('hidden');
        pendingDeleteId = null;
    }

    async function confirmDeleteUser() {
        if (!pendingDeleteId) return;

        const res = await fetch(`${usersBase}/${pendingDeleteId}?confirmed=true`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });

        const data = await res.json();

        if (data.success) {
            location.reload();
        } else if (data.error) {
            alert(data.error);
        }
    }
</script>
@endsection
