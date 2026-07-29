<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen" x-data="{ openJoinModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- System Notifications -->
            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-2xl shadow-sm flex items-center justify-between">
                    <p class="text-sm text-emerald-800 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-2xl shadow-sm flex items-center justify-between">
                    <p class="text-sm text-red-800 font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Header Section -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute -left-10 -top-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="space-y-2 relative z-10">
                    <a href="{{ route('student.dashboard') }}" class="text-slate-400 hover:text-white transition text-xs font-bold inline-flex items-center gap-1 uppercase tracking-wider mb-2">
                        &larr; Back to Dashboard
                    </a>
                    <h2 class="text-3xl font-extrabold tracking-tight">Browse Classes</h2>
                    <p class="text-slate-400 text-sm">View available classes and request enrollment in your preferred section.</p>
                </div>

                <div class="relative z-10">
                    <button @click="openJoinModal = true" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-6 py-3.5 rounded-xl shadow-lg text-xs uppercase tracking-wider transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Enroll in a Class
                    </button>
                </div>
            </div>

            <!-- Class List -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Available Classes</h3>
                        <p class="text-xs text-slate-500 mt-1">Classes currently open for enrollment.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($classes as $mathClass)
                        <div class="border border-slate-200/80 rounded-2xl p-6 flex flex-col justify-between hover:border-slate-400 transition bg-slate-50/50 shadow-sm group">
                            <div class="space-y-4">
                                <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center border border-slate-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-lg group-hover:text-blue-600 transition">{{ $mathClass->class_name }}</h4>
                                    <p class="text-xs text-slate-500 mt-1">Section: <span class="font-bold text-slate-800">{{ $mathClass->section }}</span> | School year: {{ $mathClass->school_year }}</p>
                                </div>
                                <div class="text-xs border-t border-slate-200 pt-4 flex flex-col gap-2">
                                    <div class="flex items-center justify-between">
                                        <span class="uppercase tracking-wider font-bold text-slate-400">Teacher:</span>
                                        <span class="font-semibold text-slate-700">{{ $mathClass->teacher->first_name ?? 'Teacher' }} {{ $mathClass->teacher->last_name ?? '' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="uppercase tracking-wider font-bold text-slate-400">Students:</span>
                                        <span class="font-semibold text-slate-700">{{ $mathClass->approved_students_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-slate-200">
                                <button @click="openJoinModal = true" class="w-full text-center bg-slate-900 hover:bg-blue-600 text-white font-bold py-3 rounded-xl transition text-xs uppercase tracking-wider shadow-sm">
                                    Request Enrollment
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center border-2 border-dashed border-slate-200 rounded-3xl bg-slate-50/50">
                            <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <p class="text-slate-700 font-bold text-base">No Classes Available</p>
                            <p class="text-slate-400 text-xs mt-1 max-w-sm mx-auto">There are currently no active classes available for enrollment. Please check back later.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- MODAL: Enroll in Class -->
        <div x-show="openJoinModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex justify-center items-center p-4" x-cloak style="display: none;">
            <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 space-y-6" @click.away="openJoinModal = false">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <h3 class="text-xl font-extrabold text-slate-900">Enroll in a Class</h3>
                    <button @click="openJoinModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-2xl">&times;</button>
                </div>

                <form method="POST" action="{{ route('student.class.join') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Class Enrollment Code</label>
                        <input type="text" name="class_code" placeholder="Enter code here..." class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-4 font-semibold text-slate-800 placeholder-slate-400 transition" required>
                        <p class="text-[11px] text-slate-400 mt-2 font-medium">Ask your teacher for the enrollment code to join their class section.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="openJoinModal = false" class="px-5 py-2.5 text-xs text-slate-600 hover:bg-slate-100 rounded-xl font-bold transition">Cancel</button>
                        <button type="submit" class="px-6 py-2.5 text-xs bg-slate-900 text-white hover:bg-blue-600 rounded-xl font-bold transition shadow-sm">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>