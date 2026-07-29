<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen" x-data="{ 
        openCreateModal: false,
        openEditModal: false, 
        editId: '', editFirstName: '', editLastName: '', editEmail: '', editPhone: '' 
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Teacher Management</h2>
                    <p class="text-slate-500 text-sm mt-1 font-medium">Manage faculty access and contact details.</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-blue-600 text-xs font-bold uppercase tracking-widest transition">&larr; Dashboard</a>
                    <button @click="openCreateModal = true" class="bg-slate-900 hover:bg-blue-600 text-white font-bold px-5 py-3 rounded-xl shadow-md text-xs uppercase tracking-wider transition">
                        Register Teacher
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-2xl shadow-sm">
                    <p class="text-sm text-emerald-800 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] text-slate-500 uppercase tracking-widest font-black">
                                <th class="p-5">Teacher Name</th>
                                <th class="p-5">Email Address</th>
                                <th class="p-5">Mobile Number</th>
                                <th class="p-5 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($teachers as $teacher)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-5 font-extrabold text-slate-800 text-sm">{{ $teacher->first_name }} {{ $teacher->last_name }}</td>
                                    <td class="p-5 text-sm font-semibold text-slate-600">{{ $teacher->email }}</td>
                                    <td class="p-5 text-sm font-mono font-bold text-blue-600">{{ $teacher->contact_number ?? 'Not Provided' }}</td>
                                    <td class="p-5 text-center space-x-3">
                                        <button @click="
                                            openEditModal = true;
                                            editId = '{{ $teacher->id }}';
                                            editFirstName = '{{ addslashes($teacher->first_name) }}';
                                            editLastName = '{{ addslashes($teacher->last_name) }}';
                                            editEmail = '{{ $teacher->email }}';
                                            editPhone = '{{ $teacher->contact_number }}';
                                        " class="text-blue-600 hover:text-blue-800 text-xs font-bold transition">Edit</button>
                                        
                                        <form method="POST" action="{{ route('admin.users.destroy', $teacher->id) }}" class="inline" onsubmit="return confirm('Remove this teacher from the platform?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold transition">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-10 text-center text-slate-500 font-bold text-sm">No teachers registered yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create Teacher Modal -->
            <div x-show="openCreateModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto" x-cloak style="display: none;">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100" @click.away="openCreateModal = false">
                        <h3 class="text-xl font-black text-slate-900 mb-6">Register New Teacher</h3>
                        <form method="POST" action="{{ route('admin.teachers.store') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">First Name</label>
                                <input type="text" name="first_name" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Last Name</label>
                                <input type="text" name="last_name" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
                                <input type="email" name="email" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Mobile Number</label>
                                <input type="text" name="contact_number" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" placeholder="09171234567" required>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Account Password</label>
                                <input type="password" name="password" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" placeholder="Min. 8 characters" required>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 mt-6 border-t border-slate-100">
                                <button type="button" @click="openCreateModal = false" class="px-5 py-2.5 text-xs text-slate-600 hover:bg-slate-100 rounded-xl font-bold transition">Cancel</button>
                                <button type="submit" class="px-6 py-2.5 text-xs bg-slate-900 text-white hover:bg-blue-600 rounded-xl font-bold transition shadow-sm">Register Teacher</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Teacher Modal -->
            <div x-show="openEditModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto" x-cloak style="display: none;">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100" @click.away="openEditModal = false">
                        <h3 class="text-xl font-black text-slate-900 mb-6">Edit Teacher Details</h3>
                        <form method="POST" :action="`/admin/users/${editId}`" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">First Name</label>
                                <input type="text" name="first_name" x-model="editFirstName" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Last Name</label>
                                <input type="text" name="last_name" x-model="editLastName" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Email</label>
                                <input type="email" name="email" x-model="editEmail" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Mobile Number</label>
                                <input type="text" name="contact_number" x-model="editPhone" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3">
                            </div>
                            <div class="pt-4 border-t border-slate-100 mt-2">
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 text-red-500">Reset Password (Optional)</label>
                                <input type="password" name="password" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3 placeholder-slate-300" placeholder="Leave blank to keep current password">
                            </div>
                            <div class="flex justify-end gap-3 pt-4 mt-6 border-t border-slate-100">
                                <button type="button" @click="openEditModal = false" class="px-5 py-2.5 text-xs text-slate-600 hover:bg-slate-100 rounded-xl font-bold transition">Cancel</button>
                                <button type="submit" class="px-6 py-2.5 text-xs bg-slate-900 text-white hover:bg-blue-600 rounded-xl font-bold transition shadow-sm">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>