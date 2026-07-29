<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Enrollment Requests</h2>
                    <p class="text-slate-500 text-sm mt-1 font-medium">Review and approve pending student enrollment requests.</p>
                </div>
                <a href="{{ route('teacher.dashboard') }}" class="text-slate-400 hover:text-blue-600 text-xs font-bold uppercase tracking-widest transition">&larr; Dashboard</a>
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
                                <th class="p-5">Student Name</th>
                                <th class="p-5">Email</th>
                                <th class="p-5">Target Class</th>
                                <th class="p-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php $hasRequests = false; @endphp
                            
                            @foreach($classes as $mathClass)
                                @foreach($mathClass->students as $student)
                                    @php $hasRequests = true; @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="p-5 font-extrabold text-slate-800 text-sm">{{ $student->first_name }} {{ $student->last_name }}</td>
                                        <td class="p-5 text-sm font-semibold text-slate-600">{{ $student->email }}</td>
                                        <td class="p-5">
                                            <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 px-3 py-1.5 rounded-lg whitespace-nowrap">
                                                {{ $mathClass->class_name }} ({{ $mathClass->section }})
                                            </span>
                                        </td>
                                        <td class="p-5">
                                            <div class="flex justify-end items-center gap-2">
                                                <!-- Approve Button -->
                                                <form method="POST" action="{{ route('teacher.requests.update', ['class_id' => $mathClass->id, 'student_id' => $student->id]) }}" class="m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-sm transition whitespace-nowrap">Approve</button>
                                                </form>

                                                <!-- Reject Button -->
                                                <form method="POST" action="{{ route('teacher.requests.update', ['class_id' => $mathClass->id, 'student_id' => $student->id]) }}" class="m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="bg-white border border-slate-200 hover:bg-red-50 hover:border-red-200 hover:text-red-600 text-slate-600 text-xs font-bold px-4 py-2 rounded-xl shadow-sm transition whitespace-nowrap">Reject</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                            @if(!$hasRequests)
                                <tr>
                                    <td colspan="4" class="p-10 text-center text-slate-500 font-bold text-sm">No pending enrollment requests.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>