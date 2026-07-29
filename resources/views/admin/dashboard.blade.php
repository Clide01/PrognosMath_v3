<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Dashboard Header -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="space-y-2 relative z-10">
                    <div class="inline-flex items-center gap-2 bg-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-blue-500/30">
                        Admin Workspace
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight">System Management</h2>
                    <p class="text-slate-400 text-sm">Select a module below to manage users, classes, and platform configurations.</p>
                </div>
            </div>

            <!-- Core Management Modules Grid (Stats + Navigation Combined) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Students Module -->
                <a href="{{ route('admin.students.index') }}" class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 hover:border-blue-400 hover:shadow-md transition flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Students</h3>
                        <p class="text-xs text-slate-500 mt-1 font-medium">Manage student accounts, view profiles, and update records.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Registered</span>
                        <span class="text-2xl font-black text-slate-900">{{ $totalStudents ?? 0 }}</span>
                    </div>
                </a>

                <!-- Teachers Module -->
                <a href="{{ route('admin.teachers.index') }}" class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 hover:border-emerald-400 hover:shadow-md transition flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Teachers</h3>
                        <p class="text-xs text-slate-500 mt-1 font-medium">Register instructors, manage credentials, and assign access.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Active</span>
                        <span class="text-2xl font-black text-slate-900">{{ $totalTeachers ?? 0 }}</span>
                    </div>
                </a>

                <!-- Parents Module -->
                <a href="{{ route('admin.parents.index') }}" class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 hover:border-amber-400 hover:shadow-md transition flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Parents</h3>
                        <p class="text-xs text-slate-500 mt-1 font-medium">Monitor linked parent accounts and verify contact details for SMS.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Linked</span>
                        <span class="text-2xl font-black text-slate-900">{{ $totalParents ?? 0 }}</span>
                    </div>
                </a>

                <!-- Classes Overview Card (Now Clickable) -->
                <a href="{{ route('admin.classes.index') }}" class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 hover:border-indigo-400 hover:shadow-md transition flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900">Classes</h3>
                        <p class="text-xs text-slate-500 mt-1 font-medium">Create, update, and delete active school sections.</p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Sections</span>
                        <span class="text-2xl font-black text-slate-900">{{ $totalClasses ?? 0 }}</span>
                    </div>
                </a>

            </div>

            <!-- Secondary Setup Links (Terms & Broadcasts Only - Removed Objectives to clear confusion) -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80">
                <h3 class="text-lg font-black text-slate-900 mb-6">System Configurations</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <a href="{{ route('admin.terms.index') }}" class="p-5 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-white hover:border-slate-300 transition flex items-center justify-between group">
                        <div>
                            <p class="font-extrabold text-slate-900 text-sm">Academic Terms</p>
                            <p class="text-xs text-slate-500 mt-0.5">Manage school years & semesters</p>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-1 transition">&rarr;</span>
                    </a>

                    <a href="{{ route('admin.announcements.index') }}" class="p-5 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-white hover:border-slate-300 transition flex items-center justify-between group">
                        <div>
                            <p class="font-extrabold text-slate-900 text-sm">Broadcasts</p>
                            <p class="text-xs text-slate-500 mt-0.5">Post system-wide announcements</p>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-1 transition">&rarr;</span>
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>