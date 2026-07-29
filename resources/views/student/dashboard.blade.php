<x-app-layout>
    <!-- ADDED: x-data="{ openParentModal: false }" to this wrapper div -->
    <div class="py-10 bg-slate-50 min-h-screen" x-data="{ openParentModal: false }">
        <div class="max-w-[90rem] mx-auto sm:px-6 lg:px-8 space-y-8">

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

            <!-- MISSING PARENT BANNER (Only shows if parent_id is null) -->
            @if(!Auth::user()->parent_id)
                <div class="bg-amber-50 border border-amber-200 p-5 rounded-3xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-amber-900 font-bold text-sm">Missing Parent/Guardian Link</h4>
                            <p class="text-amber-700 text-xs mt-0.5">Your account is not linked to a parent yet. Link them now so they receive SMS updates on your progress.</p>
                        </div>
                    </div>
                    <!-- This button now correctly controls the Alpine.js state -->
                    <button type="button" @click="openParentModal = true" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold uppercase tracking-wider px-5 py-2.5 rounded-xl transition shadow-sm whitespace-nowrap w-full sm:w-auto text-center">
                        Link Parent Now
                    </button>
                </div>
            @endif

            <!-- Student Welcome Banner -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="space-y-2 relative z-10">
                    <div class="inline-flex items-center gap-2 bg-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-blue-500/30">
                        Student Dashboard
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight">Welcome, {{ Auth::user()->first_name }}!</h2>
                    <p class="text-slate-400 text-sm">Status: Enrolled in <span class="text-white font-bold">{{ $enrolledClasses->count() }} Class(es)</span></p>
                </div>

                <div class="relative z-10 flex flex-wrap gap-3 w-full md:w-auto">
                    <a href="{{ route('student.grades.index') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold px-5 py-3 rounded-xl text-xs uppercase tracking-wider transition flex items-center text-center justify-center">
                        View Grades
                    </a>
                    
                    <a href="{{ route('student.classes.index') }}" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-5 py-3 rounded-xl shadow-lg text-xs uppercase tracking-wider transition flex items-center text-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Join A Class
                    </a>
                </div>
            </div>

            <!-- Three-Column Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- COLUMN 1: My Progress (AI ML Feedback) -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden flex flex-col h-full">
                    <div class="p-6 md:p-8 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">My Progress</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Here is how you are doing based on recent tests.</p>
                        </div>
                        <div class="w-10 h-10 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                    
                    <div class="p-6 md:p-8 space-y-6 flex-grow overflow-y-auto max-h-[500px] custom-scrollbar">
                        @forelse($enrolledClasses as $class)
                            <div class="border border-slate-200 rounded-2xl p-5 hover:border-slate-300 transition shadow-sm bg-slate-50/50 group">
                                <div class="flex justify-between items-start mb-4">
                                    <h4 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider group-hover:text-blue-600 transition">{{ $class->class_name }} - {{ $class->section }}</h4>
                                    
                                    <!-- ML Risk Level Indicator -->
                                    @php
                                        $risk = $class->pivot->current_risk_level ?? 'Evaluating';
                                    @endphp
                                    @if($risk === 'Low')
                                        <span class="bg-emerald-100 text-emerald-700 border border-emerald-200 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md">On Track</span>
                                    @elseif($risk === 'Moderate')
                                        <span class="bg-amber-100 text-amber-700 border border-amber-200 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md">Needs Review</span>
                                    @elseif($risk === 'High')
                                        <span class="bg-red-100 text-red-700 border border-red-200 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md">Needs Help</span>
                                    @else
                                        <span class="bg-slate-200 text-slate-600 border border-slate-300 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md">Evaluating</span>
                                    @endif
                                </div>

                                <!-- AI Diagnosed Weakness -->
                                <div class="bg-white p-4 rounded-xl border border-slate-100 flex flex-col gap-2">
                                    <span class="text-xs font-bold text-slate-600">Identified Objective Gap:</span>
                                    @if($class->pivot->diagnosed_weak_competency && $class->pivot->diagnosed_weak_competency !== 'None Identified' && $class->pivot->diagnosed_weak_competency !== 'Pending Analysis')
                                        <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-100 px-3 py-1.5 rounded-lg">
                                            {{ $class->pivot->diagnosed_weak_competency }}
                                        </span>
                                    @else
                                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-3 py-1.5 rounded-lg">
                                            Doing Great!
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto text-slate-400 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                <p class="text-slate-800 font-bold text-base">No Classes Yet</p>
                                <p class="text-slate-400 text-xs mt-1">Join a class to see your progress.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- COLUMN 2: Learning Modules -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden flex flex-col h-full">
                    <div class="p-6 md:p-8 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Learning Modules</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Personalized learning materials.</p>
                        </div>
                        <div class="w-10 h-10 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                    </div>
                    
                    <div class="p-6 md:p-8 space-y-4 flex-grow overflow-y-auto max-h-[500px] custom-scrollbar">
                        @forelse($aiInterventions as $module)
                            <div class="border border-slate-200 rounded-2xl p-5 hover:border-slate-300 transition shadow-sm bg-white group flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <span class="bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-indigo-200 mb-2 inline-block">
                                        Learning Module
                                    </span>
                                    <h4 class="font-extrabold text-slate-900 text-base line-clamp-1 group-hover:text-blue-600 transition">{{ $module->title }}</h4>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-1">Status: <span class="font-medium {{ $module->status === 'completed' ? 'text-emerald-600' : 'text-amber-600' }}">{{ ucfirst($module->status) }}</span></p>
                                </div>
                                
                                <a href="{{ route('student.intervention.show', $module->id) }}" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider px-6 py-2.5 rounded-xl shadow-sm transition text-center whitespace-nowrap sm:w-auto w-full">
                                    {{ $module->status === 'completed' ? 'Review' : 'Start' }}
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto text-slate-400 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                                <p class="text-slate-800 font-bold text-base">No Learning Modules</p>
                                <p class="text-slate-400 text-xs mt-1">Keep up the good work! You currently have no remedial modules assigned.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- COLUMN 3: Pending Assignments -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden flex flex-col h-full">
                    <div class="p-6 md:p-8 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Pending Assignments</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Complete the tasks assigned by your teacher.</p>
                        </div>
                        <div class="w-10 h-10 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                    </div>
                    
                    <div class="p-6 md:p-8 space-y-4 flex-grow overflow-y-auto max-h-[500px] custom-scrollbar">
                        @forelse($activeAssessments as $assessment)
                            <div class="border border-slate-200 rounded-2xl p-5 hover:border-slate-300 transition shadow-sm bg-white group flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-slate-200 mb-2 inline-block">
                                        {{ $assessment->type }}
                                    </span>
                                    <h4 class="font-extrabold text-slate-900 text-base line-clamp-1 group-hover:text-blue-600 transition">{{ $assessment->title }}</h4>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-1">Topic: <span class="font-medium text-slate-700">{{ $assessment->topic }}</span></p>
                                </div>
                                
                                <a href="{{ route('student.quiz.take', $assessment->id) }}" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold uppercase tracking-wider px-6 py-2.5 rounded-xl shadow-sm transition text-center whitespace-nowrap sm:w-auto w-full">
                                    Start
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto text-slate-400 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-slate-800 font-bold text-base">You're all caught up!</p>
                                <p class="text-slate-400 text-xs mt-1">There are no pending assignments at the moment.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
        
        <!-- LINK PARENT MODAL -->
        <div x-show="openParentModal" class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex justify-center items-center p-4" x-cloak style="display: none;">
            <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl border border-slate-100 space-y-6" @click.away="openParentModal = false">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">Add Parent Details</h3>
                    </div>
                    <button @click="openParentModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-2xl">&times;</button>
                </div>

                <form method="POST" action="{{ route('student.link.parent') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">First Name</label>
                            <input type="text" name="parent_first_name" class="w-full border-slate-200 rounded-xl text-sm focus:ring-[#303070] focus:border-[#303070] p-3 font-semibold text-slate-800" required>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Last Name</label>
                            <input type="text" name="parent_last_name" class="w-full border-slate-200 rounded-xl text-sm focus:ring-[#303070] focus:border-[#303070] p-3 font-semibold text-slate-800" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
                        <input type="email" name="parent_email" class="w-full border-slate-200 rounded-xl text-sm focus:ring-[#303070] focus:border-[#303070] p-3 font-semibold text-slate-800" required>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Mobile Number</label>
                        <input type="text" name="parent_phone" class="w-full border-slate-200 rounded-xl text-sm focus:ring-[#303070] focus:border-[#303070] p-3 font-semibold text-slate-800" placeholder="09171234567" required>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 mt-2 border-t border-slate-100">
                        <button type="button" @click="openParentModal = false" class="px-5 py-2.5 text-xs text-slate-600 hover:bg-slate-100 rounded-xl font-bold transition">Cancel</button>
                        <button type="submit" class="px-6 py-2.5 text-xs bg-[#303070] text-white hover:bg-[#404085] rounded-xl font-bold transition shadow-sm">Link Parent Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>