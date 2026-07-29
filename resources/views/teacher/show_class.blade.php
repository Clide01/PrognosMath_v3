<x-app-layout>
    <!-- Global AI Loading Overlay -->
    <div id="aiLoadingOverlay" class="fixed inset-0 z-[100] bg-slate-900/80 backdrop-blur-sm flex flex-col justify-center items-center hidden transition-opacity">
        <div class="bg-white p-8 rounded-3xl shadow-2xl flex flex-col items-center max-w-sm w-full mx-4 text-center border border-slate-100">
            <div class="relative w-16 h-16 mb-6">
                <div class="absolute inset-0 bg-blue-500 rounded-full blur-xl opacity-20 animate-pulse"></div>
                <svg class="animate-spin relative z-10 w-16 h-16 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-extrabold text-slate-900 mb-2 tracking-tight">Gemini AI is Working</h3>
            <p class="text-sm text-slate-500 font-medium leading-relaxed">Synthesizing personalized learning module for the student. Please hold.</p>
        </div>
    </div>

    <!-- Alpine toggleScroll & Dynamic Data logic -->
    <div class="py-10 bg-slate-50 min-h-screen" x-data="{ 
        openEditAssessmentModal: false, 
        editAssAction: '', 
        editAssTitle: '', 
        editAssTopic: '', 
        editAssType: '',
        openSmsModal: false,
        smsStudentName: '',
        smsAction: '',
        toggleScroll() {
            if (this.openEditAssessmentModal || this.openSmsModal) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
    }" x-init="
        $watch('openEditAssessmentModal', () => toggleScroll());
        $watch('openSmsModal', () => toggleScroll());
    ">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm flex items-center justify-between">
                    <p class="text-sm text-emerald-800 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm flex items-center justify-between">
                    <p class="text-sm text-red-800 font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Class Header Section -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <!-- Left Side: Title and Details -->
                <div class="space-y-2 relative z-10">
                    <div class="inline-flex items-center gap-2 bg-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-blue-500/30">
                        Class Dashboard
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight">{{ $mathClass->class_name }}</h2>
                    
                    <!-- Class Code in Header -->
                    <p class="text-slate-400 text-sm flex items-center gap-3 mt-1">
                        <span>Section: <span class="text-slate-200 font-semibold">{{ $mathClass->section }}</span></span>
                        <span class="text-slate-600">|</span>
                        <span>SY: <span class="text-slate-200 font-semibold">{{ $mathClass->school_year }}</span></span>
                        <span class="text-slate-600">|</span>
                        <span class="flex items-center gap-2">Code: <span class="bg-blue-500/20 text-blue-300 font-mono font-bold px-2 py-0.5 rounded border border-blue-500/30 tracking-widest">{{ $mathClass->class_code }}</span></span>
                    </p>
                </div>

                <!-- Right Side: Back Button & Stats -->
                <div class="relative z-10 flex flex-col md:items-end gap-4 w-full md:w-auto">
                    <a href="{{ route('teacher.dashboard') }}" class="text-slate-400 hover:text-white transition text-xs font-bold inline-flex items-center gap-1 uppercase tracking-wider bg-slate-800/80 border border-slate-700 px-4 py-2 rounded-xl shadow-sm self-start md:self-end">
                        &larr; Back to Teacher Dashboard
                    </a>

                    <div class="bg-slate-800/80 px-6 py-4 rounded-2xl border border-slate-700 text-center shadow-inner">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400 block">Total Students</span>
                        <div class="text-3xl font-black text-white mt-0.5">{{ $approvedStudents->count() }}</div>
                    </div>
                </div>
            </div>

            <!-- Risk Alert Banner -->
            @if($highRiskCount > 0)
                <div class="bg-amber-50 border border-amber-200 p-5 rounded-2xl shadow-sm flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <p class="text-sm text-amber-900 font-medium">
                            <strong class="font-bold">Action Required:</strong> The system has identified <strong>{{ $highRiskCount }} student(s)</strong> in this class who are at a high risk of falling behind. Early intervention is recommended.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Student Performance Overview -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-slate-100">
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Student Support Overview</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Overview of student performance and engagement levels based on recent assessments.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                    
                    <!-- HIGH RISK -->
                    <div class="p-6 md:p-8 bg-red-50/20">
                        <div class="flex items-center gap-2 mb-6">
                            <span class="w-3 h-3 rounded-full bg-red-500"></span>
                            <h4 class="font-black text-red-700 uppercase tracking-wider text-xs">High Risk</h4>
                            <span class="ml-auto bg-red-100 text-red-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-red-200">{{ $approvedStudents->where('risk_level', 'High')->count() }}</span>
                        </div>
                        <ul class="space-y-2.5">
                            @forelse($approvedStudents->where('risk_level', 'High') as $s)
                                <li class="text-sm font-semibold text-slate-800 flex justify-between items-center bg-white p-3 rounded-xl border border-red-200/60 shadow-sm">
                                    {{ $s->first_name }} {{ $s->last_name }}
                                </li>
                            @empty
                                <li class="text-xs text-slate-400 italic py-2">No students currently in this category.</li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- AT RISK -->
                    <div class="p-6 md:p-8 bg-amber-50/20">
                        <div class="flex items-center gap-2 mb-6">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <h4 class="font-black text-amber-700 uppercase tracking-wider text-xs">At Risk</h4>
                            <span class="ml-auto bg-amber-100 text-amber-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-amber-200">{{ $approvedStudents->where('risk_level', 'Moderate')->count() }}</span>
                        </div>
                        <ul class="space-y-2.5">
                            @forelse($approvedStudents->where('risk_level', 'Moderate') as $s)
                                <li class="text-sm font-semibold text-slate-800 flex justify-between items-center bg-white p-3 rounded-xl border border-amber-200/60 shadow-sm">
                                    {{ $s->first_name }} {{ $s->last_name }}
                                </li>
                            @empty
                                <li class="text-xs text-slate-400 italic py-2">No students currently in this category.</li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- ON TRACK -->
                    <div class="p-6 md:p-8 bg-emerald-50/20">
                        <div class="flex items-center gap-2 mb-6">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <h4 class="font-black text-emerald-700 uppercase tracking-wider text-xs">On Track</h4>
                            <span class="ml-auto bg-emerald-100 text-emerald-700 text-xs font-bold px-2.5 py-0.5 rounded-full border border-emerald-200">{{ $approvedStudents->where('risk_level', 'Low')->count() }}</span>
                        </div>
                        <ul class="space-y-2.5">
                            @forelse($approvedStudents->where('risk_level', 'Low') as $s)
                                <li class="text-sm font-semibold text-slate-800 flex justify-between items-center bg-white p-3 rounded-xl border border-emerald-200/60 shadow-sm">
                                    {{ $s->first_name }} {{ $s->last_name }}
                                </li>
                            @empty
                                <li class="text-xs text-slate-400 italic py-2">No students currently in this category.</li>
                            @endforelse
                        </ul>
                    </div>

                </div>
            </div>

            <!-- Assessments & Materials -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-slate-100 flex justify-between items-center">
                    <div>
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Assessments & Learning Resources</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Manage quizzes, activities, and generated resources for this class.</p>
                    </div>
                </div>
                
                <div class="p-6 md:p-8 max-h-[520px] overflow-y-auto custom-scrollbar pr-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($assessments as $assessment)
                            <div class="border border-slate-200/80 rounded-2xl p-6 flex flex-col justify-between hover:border-slate-400 transition bg-white shadow-sm">
                                <div>
                                    <div class="flex justify-between items-start mb-4">
                                        <span class="bg-slate-100 text-slate-700 text-[11px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md border border-slate-200">{{ $assessment->type }}</span>
                                        <div class="flex space-x-1">
                                            <button @click="
                                                openEditAssessmentModal = true;
                                                editAssAction = '{{ route('teacher.assessment.update', $assessment->id) }}';
                                                editAssTitle = '{{ addslashes($assessment->title) }}';
                                                editAssTopic = '{{ addslashes($assessment->topic) }}';
                                                editAssType = '{{ $assessment->type }}';
                                            " class="text-slate-400 hover:text-slate-700 p-1.5 transition" title="Edit Assessment">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            
                                            <form method="POST" action="{{ route('teacher.assessment.destroy', $assessment->id) }}" onsubmit="return confirm('Are you sure you want to delete this assessment? All associated student scores will also be deleted.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-slate-400 hover:text-red-600 p-1.5 transition" title="Delete Assessment">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <h4 class="font-extrabold text-slate-900 text-lg line-clamp-1" title="{{ $assessment->title }}">{{ $assessment->title }}</h4>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-1">Topic: <span class="text-slate-700 font-medium">{{ $assessment->topic }}</span></p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center text-xs">
                                    <span class="text-slate-400 font-medium">{{ $assessment->created_at->format('M d, Y') }}</span>
                                    <span class="font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-md">{{ $assessment->questions()->count() }} Items</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center text-slate-400 text-sm italic">
                                No assessments have been created for this class yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Student List & Progress -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-slate-100">
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Student List & Progress</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Detailed performance tracking and learning paths for section {{ $mathClass->section }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-black text-slate-500 uppercase tracking-widest">
                                <th class="p-5">Student Name & Email</th>
                                <th class="p-5">Identified Objective Gap</th>
                                <th class="p-5">Module Progress</th>
                                <th class="p-5 text-center">Status</th>
                                <th class="p-5 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($approvedStudents as $student)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-5">
                                        <p class="font-bold text-slate-900">{{ $student->first_name }} {{ $student->last_name }}</p>
                                        <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $student->email }}</p>
                                    </td>

                                    <td class="p-5">
                                        @if($student->weak_competency === 'None' || $student->weak_competency === null || $student->weak_competency === 'None Identified')
                                            <span class="text-slate-400 text-xs italic">Pending Analysis</span>
                                        @else
                                            <span class="font-bold text-red-700 bg-red-50 px-3 py-1.5 rounded-lg border border-red-200 text-xs inline-block">{{ $student->weak_competency }}</span>
                                        @endif
                                    </td>

                                    <td class="p-5">
                                        @if($student->ai_materials_count == 0)
                                            <span class="text-xs text-slate-400 italic font-medium">No Lessons Assigned</span>
                                        @else
                                            <div class="w-48 bg-slate-100 rounded-full h-2 mb-1.5 overflow-hidden border border-slate-200">
                                                <div class="bg-slate-900 h-2 rounded-full" style="width: {{ $student->ai_progress }}%"></div>
                                            </div>
                                            <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">{{ $student->ai_progress }}% Completed</span>
                                        @endif
                                    </td>

                                    <td class="p-5 text-center">
                                        @if($student->risk_level === 'High')
                                            <span class="bg-red-100 text-red-700 text-xs font-black px-3.5 py-1.5 rounded-full border border-red-200 inline-block">High Risk</span>
                                        @elseif($student->risk_level === 'Moderate')
                                            <span class="bg-amber-100 text-amber-800 text-xs font-black px-3.5 py-1.5 rounded-full border border-amber-200 inline-block">At Risk</span>
                                        @elseif($student->risk_level === 'Low')
                                            <span class="bg-emerald-100 text-emerald-700 text-xs font-black px-3.5 py-1.5 rounded-full border border-emerald-200 inline-block">On Track</span>
                                        @else
                                            <span class="bg-slate-100 text-slate-600 text-xs font-bold px-3.5 py-1.5 rounded-full border border-slate-200 inline-block">Evaluating...</span>
                                        @endif
                                    </td>

                                    <td class="p-5">
                                        <div class="flex flex-col gap-2 max-w-[200px] mx-auto">
                                            @if($student->risk_level === 'High' || $student->risk_level === 'Moderate')
                                                <form method="POST" action="{{ route('teacher.generate.intervention', $student->id) }}" onsubmit="document.getElementById('aiLoadingOverlay').classList.remove('hidden');">
                                                    @csrf
                                                    <button type="submit" class="w-full bg-slate-900 hover:bg-blue-600 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm transition flex items-center justify-center gap-1.5">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg> 
                                                        Generate Learning Module
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <a href="{{ route('teacher.classes.student.analytics', ['class_id' => $mathClass->id, 'student_id' => $student->id]) }}" class="w-full bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 text-center text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm transition block">
                                                View Analytics
                                            </a>

                                            <!-- WIRED SMS BUTTON -->
                                            <button type="button" @click="
                                                openSmsModal = true;
                                                smsStudentName = '{{ addslashes($student->first_name) }}';
                                                smsAction = '{{ route('teacher.message.parent', $student->id) }}';
                                            " class="w-full bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm transition flex justify-center items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                                Send SMS Alert
                                            </button>

                                            <form method="POST" action="{{ route('teacher.classes.student.remove', ['class_id' => $mathClass->id, 'student_id' => $student->id]) }}" onsubmit="return confirm('Are you sure you want to remove this student from the class?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold px-3.5 py-2 rounded-xl transition shadow-sm">
                                                    Remove Student
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center">
                                        <p class="text-slate-600 font-bold text-base">No Students Enrolled</p>
                                        <p class="text-slate-400 text-xs mt-1">Pending student requests will appear here once approved.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MODAL: Edit Assessment -->
        <div x-show="openEditAssessmentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto" x-cloak style="display: none;">
            <div class="flex min-h-full items-start justify-center p-4 py-10 sm:py-16">
                <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-6" @click.away="openEditAssessmentModal = false">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                        <h3 class="text-xl font-extrabold text-slate-900">Edit Assessment Details</h3>
                        <button @click="openEditAssessmentModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
                    </div>

                    <form method="POST" :action="editAssAction" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Assessment Title</label>
                            <input type="text" name="title" x-model="editAssTitle" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800" required>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Type</label>
                            <select name="type" x-model="editAssType" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800" required>
                                <option value="quiz">Quiz</option>
                                <option value="activity">Activity</option>
                                <option value="assignment">Assignment</option>
                                <option value="examination">Examination</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Topic</label>
                            <input type="text" name="topic" x-model="editAssTopic" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800" required>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                            <button type="button" @click="openEditAssessmentModal = false" class="px-5 py-2.5 text-xs text-slate-600 hover:bg-slate-100 rounded-xl font-bold transition">Cancel</button>
                            <button type="submit" class="px-6 py-2.5 text-xs bg-slate-900 text-white hover:bg-blue-600 rounded-xl font-bold transition shadow-sm">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: Send SMS Alert -->
        <div x-show="openSmsModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto" x-cloak style="display: none;">
            <div class="flex min-h-full items-start justify-center p-4 py-10 sm:py-16">
                <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-6" @click.away="openSmsModal = false">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900">Direct Message</h3>
                            <p class="text-xs text-slate-500 mt-1">Send an SMS update to <span x-text="smsStudentName" class="font-bold text-slate-700"></span>'s parent.</p>
                        </div>
                        <button @click="openSmsModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
                    </div>

                    <form method="POST" :action="smsAction" x-data="{ text: '' }" class="space-y-4">
                        @csrf
                        <div class="relative">
                            <textarea 
                                name="custom_message" 
                                x-model="text"
                                maxlength="300"
                                rows="4" 
                                class="w-full rounded-2xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-slate-900 focus:ring-slate-900 p-4 bg-slate-50/50 resize-none shadow-inner"
                                placeholder="Type your message to the parent here..."
                                required></textarea>
                            
                            <!-- Live Counter UI -->
                            <div class="absolute bottom-3 right-3 text-xs font-bold px-2.5 py-1 rounded-md bg-white shadow-sm border border-slate-100" 
                                 :class="text.length >= 300 ? 'text-red-500 border-red-200' : 'text-slate-500'">
                                <span x-text="text.length"></span> / 300
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 mt-2 border-t border-slate-100">
                            <button type="button" @click="openSmsModal = false" class="px-5 py-2.5 text-xs text-slate-600 hover:bg-slate-100 rounded-xl font-bold transition">Cancel</button>
                            <button type="submit" class="px-6 py-2.5 text-xs bg-slate-900 text-white hover:bg-blue-600 rounded-xl font-bold transition shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Send SMS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>