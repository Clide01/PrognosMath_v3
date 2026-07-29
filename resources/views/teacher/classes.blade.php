<!-- resources/views/teacher/classes.blade.php -->

<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Page Header -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="space-y-2 relative z-10">
                    <a href="{{ route('teacher.dashboard') }}" class="text-slate-400 hover:text-white transition text-xs font-bold inline-flex items-center gap-1 uppercase tracking-wider mb-2">
                        &larr; Back to Teacher Dashboard
                    </a>
                    <br>
                    <h2 class="text-3xl font-extrabold tracking-tight">Class Management</h2>
                    <p class="text-slate-400 text-sm">Create and organize your mathematics sections.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-2xl shadow-sm">
                    <p class="text-sm text-emerald-800 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Create Class Form -->
                <div class="lg:col-span-1 bg-white rounded-3xl shadow-sm p-8 border border-slate-200/80 h-fit">
                    <h3 class="text-lg font-extrabold text-slate-900 mb-6 border-b border-slate-100 pb-4">Create New Class</h3>
                    <form method="POST" action="{{ route('teacher.class.store') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Class Name / Subject</label>
                            <input type="text" name="class_name" placeholder="e.g. Grade 10 Mathematics" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800" required>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Section</label>
                            <input type="text" name="section" placeholder="e.g. Section A - Einstein" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800" required>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">School Year</label>
                            <select name="school_year" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800" required>
                                <option value="" disabled selected>Select School Year</option>
                                @foreach($schoolYears as $sy)
                                    <option value="{{ $sy->year }}">{{ $sy->year }} ({{ $sy->semester }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-slate-900 hover:bg-blue-600 text-white font-bold py-3.5 mt-2 rounded-xl transition text-xs uppercase tracking-wider shadow-sm">
                            Save Class
                        </button>
                    </form>
                </div>

                <!-- List of Classes -->
                <div class="lg:col-span-2">
                    <div class="max-h-[600px] overflow-y-auto custom-scrollbar pr-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @forelse($classes as $mathClass)
                                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 hover:border-slate-400 transition shadow-sm group">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center font-black text-sm border border-slate-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                        
                                        <!-- Class Code Display -->
                                        <div class="bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1.5 rounded-lg text-center shadow-sm">
                                            <span class="block text-[9px] font-black uppercase tracking-widest text-blue-500">Class Code</span>
                                            <span class="font-mono font-bold text-sm tracking-wider">{{ $mathClass->class_code }}</span>
                                        </div>
                                    </div>
                                    
                                    <h4 class="font-extrabold text-slate-900 text-lg group-hover:text-blue-600 transition">{{ $mathClass->class_name }}</h4>
                                    <p class="text-sm text-slate-600 font-medium mb-4">{{ $mathClass->section }}</p>
                                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-md">{{ $mathClass->school_year }}</span>
                                </div>
                            @empty
                                <div class="col-span-full bg-white p-12 text-center rounded-3xl shadow-sm border border-slate-200/80">
                                    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <p class="text-slate-800 font-bold text-base">You haven't created any classes yet.</p>
                                    <p class="text-slate-400 text-xs mt-1">Use the form to get started.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>