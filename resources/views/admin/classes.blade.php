<!-- resources/views/admin/classes.blade.php -->

<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen" x-data="{ 
        openCreateModal: false,
        openEditModal: false,
        editId: '',
        editTeacherId: '',
        editClassName: '',
        editSection: '',
        editYear: ''
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Class Management</h2>
                    <p class="text-slate-500 text-sm mt-1 font-medium">Create and oversee all active school sections.</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-blue-600 text-xs font-bold uppercase tracking-widest transition">&larr; Dashboard</a>
                    <button @click="openCreateModal = true" class="bg-slate-900 hover:bg-blue-600 text-white font-bold px-5 py-3 rounded-xl shadow-md text-xs uppercase tracking-wider transition">
                        Create Class
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
                                <th class="p-5">Grade & Section</th>
                                <th class="p-5">Assigned Teacher</th>
                                <th class="p-5">Class Code</th>
                                <th class="p-5">School Year</th>
                                <th class="p-5 text-center">Enrolled Students</th>
                                <th class="p-5 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($classes as $c)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-5">
                                        <p class="font-extrabold text-slate-800 text-sm">{{ $c->class_name }}</p>
                                        <p class="text-xs text-slate-500 font-semibold">{{ $c->section }}</p>
                                    </td>
                                    <td class="p-5 text-sm font-semibold text-slate-700">
                                        {{ $c->teacher->first_name ?? 'Unassigned' }} {{ $c->teacher->last_name ?? '' }}
                                    </td>
                                    <td class="p-5">
                                        <span class="font-mono font-bold text-xs bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200 text-slate-800">{{ $c->class_code }}</span>
                                    </td>
                                    <td class="p-5 text-xs font-bold text-slate-600">{{ $c->school_year }}</td>
                                    <td class="p-5 text-center">
                                        <span class="bg-blue-50 text-blue-700 text-xs font-black px-3 py-1 rounded-full border border-blue-100">{{ $c->approved_students_count }} Students</span>
                                    </td>
                                    <td class="p-5 text-center space-x-3">
                                        <button @click="
                                            openEditModal = true;
                                            editId = '{{ $c->id }}';
                                            editTeacherId = '{{ $c->teacher_id }}';
                                            editClassName = '{{ addslashes($c->class_name) }}';
                                            editSection = '{{ addslashes($c->section) }}';
                                            editYear = '{{ $c->school_year }}';
                                        " class="text-blue-600 hover:text-blue-800 text-xs font-bold transition">Edit</button>
                                        
                                        <form method="POST" action="{{ route('admin.classes.destroy', $c->id) }}" class="inline" onsubmit="return confirm('Delete this class permanently?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold transition">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-10 text-center text-slate-500 font-bold text-sm">No classes created yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create Class Modal -->
            <div x-show="openCreateModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto" x-cloak style="display: none;">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100" @click.away="openCreateModal = false">
                        <h3 class="text-xl font-black text-slate-900 mb-6">Create New Class</h3>
                        <form method="POST" action="{{ route('admin.classes.store') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Assign Teacher</label>
                                <select name="teacher_id" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                                    <option value="" disabled selected>Select Teacher</option>
                                    @foreach($teachers as $t)
                                        <option value="{{ $t->id }}">{{ $t->first_name }} {{ $t->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Grade Level</label>
                                <select name="class_name" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                                    @for($i=1; $i<=12; $i++)
                                        <option value="Grade {{ $i }}">Grade {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Section Name</label>
                                <input type="text" name="section" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" placeholder="e.g. Einstein" required>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">School Year</label>
                                <select name="school_year" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                                    <option value="" disabled selected>Select School Year</option>
                                    @foreach($schoolYears as $sy)
                                        <option value="{{ $sy->year }}">{{ $sy->year }} ({{ $sy->semester }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex justify-end gap-3 pt-4 mt-6 border-t border-slate-100">
                                <button type="button" @click="openCreateModal = false" class="px-5 py-2.5 text-xs text-slate-600 hover:bg-slate-100 rounded-xl font-bold transition">Cancel</button>
                                <button type="submit" class="px-6 py-2.5 text-xs bg-slate-900 text-white hover:bg-blue-600 rounded-xl font-bold transition shadow-sm">Create Class</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Class Modal -->
            <div x-show="openEditModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto" x-cloak style="display: none;">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100" @click.away="openEditModal = false">
                        <h3 class="text-xl font-black text-slate-900 mb-6">Edit Class</h3>
                        <form method="POST" :action="`/admin/classes/${editId}`" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Assign Teacher</label>
                                <select name="teacher_id" x-model="editTeacherId" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                                    @foreach($teachers as $t)
                                        <option value="{{ $t->id }}">{{ $t->first_name }} {{ $t->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Grade Level</label>
                                <select name="class_name" x-model="editClassName" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                                    @for($i=1; $i<=12; $i++)
                                        <option value="Grade {{ $i }}">Grade {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Section Name</label>
                                <input type="text" name="section" x-model="editSection" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">School Year</label>
                                <select name="school_year" x-model="editYear" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3" required>
                                    <option value="" disabled>Select School Year</option>
                                    @foreach($schoolYears as $sy)
                                        <option value="{{ $sy->year }}">{{ $sy->year }} ({{ $sy->semester }})</option>
                                    @endforeach
                                </select>
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