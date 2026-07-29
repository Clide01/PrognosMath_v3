<!-- resources/views/teacher/dashboard.blade.php -->

<x-app-layout>
    <!-- Global AI Loading Overlay (Hidden by default) -->
    <div id="aiLoadingOverlay" class="fixed inset-0 z-[100] bg-slate-900/80 backdrop-blur-sm flex flex-col justify-center items-center hidden transition-opacity">
        <div class="bg-white p-8 rounded-3xl shadow-2xl flex flex-col items-center max-w-sm w-full mx-4 text-center border border-slate-100">
            <div class="relative w-16 h-16 mb-6">
                <div class="absolute inset-0 bg-blue-500 rounded-full blur-xl opacity-20 animate-pulse"></div>
                <svg class="animate-spin relative z-10 w-16 h-16 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 id="aiLoaderTitle" class="text-xl font-extrabold text-slate-900 mb-2 tracking-tight">Gemini AI is Working</h3>
            <p id="aiLoaderDesc" class="text-sm text-slate-500 font-medium leading-relaxed">Analyzing document and synthesizing structured evaluation items. This usually takes 15-30 seconds.</p>
        </div>
    </div>

    <!-- Alpine toggleScroll logic to fix double scrollbars -->
    <div class="py-10 bg-slate-50 min-h-screen" x-data="{ 
        openClassModal: false, 
        openAssessmentModal: false,
        openEditModal: false,
        editFormAction: '',
        editGrade: '',
        editSection: '',
        editYear: '',
        toggleScroll() {
            if (this.openClassModal || this.openAssessmentModal || this.openEditModal) {
                document.body.classList.add('overflow-hidden');
            } else {
                document.body.classList.remove('overflow-hidden');
            }
        }
    }" x-init="
        $watch('openClassModal', () => toggleScroll());
        $watch('openAssessmentModal', () => toggleScroll());
        $watch('openEditModal', () => toggleScroll());
    ">
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

            <!-- Dashboard Header -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="space-y-2 relative z-10">
                    <div class="inline-flex items-center gap-2 bg-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-blue-500/30">
                        Teacher Workspace
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight">Teacher Dashboard</h2>
                    <p class="text-slate-400 text-sm">Manage your classes, students, and AI-generated assessments.</p>
                </div>

                <div class="relative z-10 flex flex-wrap gap-3 w-full md:w-auto">
                    <button @click="openClassModal = true" class="bg-white text-slate-900 hover:bg-slate-100 font-bold px-5 py-3 rounded-xl shadow-sm text-xs uppercase tracking-wider transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Create New Class
                    </button>
                    
                    <button @click="openAssessmentModal = true" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-5 py-3 rounded-xl shadow-lg text-xs uppercase tracking-wider transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> Generate AI Assessment
                    </button>
                    
                    <a href="{{ route('teacher.requests.index') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold px-5 py-3 rounded-xl text-xs uppercase tracking-wider transition flex items-center">
                        Join Requests
                        @if(isset($pendingRequestsCount) && $pendingRequestsCount > 0)
                            <span class="ml-2 bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-black">{{ $pendingRequestsCount }}</span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Search & Filter Controls -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200/80">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="text" id="classSearch" placeholder="Search classes by grade, section name, or school year..." class="w-full pl-11 pr-4 py-3.5 border border-slate-200 rounded-xl focus:ring-slate-900 focus:border-slate-900 text-sm font-medium text-slate-800 placeholder-slate-400">
                </div>
            </div>

            <!-- Class Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="classGrid">
                @if(isset($classes))
                    @forelse($classes as $class)
                        <div class="class-card bg-white rounded-3xl shadow-sm border border-slate-200/80 hover:border-slate-400 hover:shadow-md transition flex flex-col relative overflow-hidden group" data-name="{{ $class->class_name }} {{ $class->section }}">
                            
                            <!-- Action Menu (Top Right) -->
                            <div class="absolute top-5 right-5 flex space-x-1.5 z-10">
                                <!-- Edit Button -->
                                <button type="button" @click="
                                    openEditModal = true; 
                                    editFormAction = '{{ route('teacher.class.update', $class->id) }}';
                                    editGrade = '{{ $class->class_name }}';
                                    editSection = '{{ $class->section }}';
                                    editYear = '{{ $class->school_year }}';
                                " class="bg-slate-100 p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition" title="Edit Class Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                
                                <!-- Delete Button -->
                                <form method="POST" action="{{ route('teacher.class.destroy', $class->id) }}" onsubmit="return confirm('Are you sure you want to delete this class? All associated assessments and student data will be permanently deleted.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-slate-100 p-2 rounded-xl text-slate-500 hover:text-red-600 hover:bg-red-50 transition" title="Delete Class">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>

                            <div class="p-7 flex-grow space-y-4">
                                <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-extrabold text-slate-900 pr-16 group-hover:text-blue-600 transition">{{ $class->class_name }}</h3>
                                    <p class="text-xs text-slate-500 mt-1">Section: <span class="font-bold text-slate-800">{{ $class->section }}</span> | SY: <span class="font-medium text-slate-700">{{ $class->school_year }}</span></p>
                                </div>
                                
                                <!-- Class Code Display on Dashboard -->
                                <div class="bg-blue-50/50 border border-blue-100 p-3 rounded-xl flex justify-between items-center">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-blue-600">Class Code</span>
                                    <span class="font-mono font-bold text-sm tracking-widest text-slate-900 bg-white px-2 py-1 rounded shadow-sm border border-slate-200">{{ $class->class_code }}</span>
                                </div>

                                <div class="flex justify-between items-center border-t border-slate-100 pt-4 text-xs">
                                    <span class="font-extrabold text-slate-400 uppercase tracking-wider">Enrolled Students</span>
                                    <span class="bg-slate-100 text-slate-900 font-black px-3 py-1 rounded-lg border border-slate-200">{{ $class->approved_students_count }} Students</span>
                                </div>
                            </div>

                            <a href="{{ route('teacher.classes.show', $class->id) }}" class="block w-full text-center bg-slate-900 hover:bg-blue-600 text-white font-bold py-3.5 transition text-xs uppercase tracking-wider">
                                View Class Dashboard &rarr;
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full bg-white p-16 text-center rounded-3xl shadow-sm border border-slate-200/80 space-y-3">
                            <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <p class="text-slate-800 font-bold text-base">No Classes Created</p>
                            <p class="text-slate-400 text-xs max-w-sm mx-auto">Create a new class using the controls above to start managing your students and assessments.</p>
                        </div>
                    @endforelse
                @endif
            </div>
        </div>

        <!-- MODAL 1: Create Class (FIXED STRUCTURE) -->
        <div x-show="openClassModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto" x-cloak style="display: none;">
            <div class="flex min-h-full items-start justify-center p-4 py-10 sm:py-16">
                <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-6" @click.away="openClassModal = false">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                        <h3 class="text-xl font-extrabold text-slate-900">Create New Class</h3>
                        <button @click="openClassModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('teacher.class.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Grade Level</label>
                            <select name="class_name" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800" required>
                                <option value="Grade 1">Grade 1</option>
                                <option value="Grade 2">Grade 2</option>
                                <option value="Grade 3">Grade 3</option>
                                <option value="Grade 4">Grade 4</option>
                                <option value="Grade 5">Grade 5</option>
                                <option value="Grade 6">Grade 6</option>
                                <option value="Grade 7">Grade 7</option>
                                <option value="Grade 8">Grade 8</option>
                                <option value="Grade 9">Grade 9</option>
                                <option value="Grade 10">Grade 10</option>
                                <option value="Grade 11">Grade 11</option>
                                <option value="Grade 12">Grade 12</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Section Name</label>
                            <input type="text" name="section" placeholder="e.g. Faith, Leibniz, Sampaguita" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800" required>
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

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                            <button type="button" @click="openClassModal = false" class="px-5 py-2.5 text-xs text-slate-600 hover:bg-slate-100 rounded-xl font-bold transition">Cancel</button>
                            <button type="submit" class="px-6 py-2.5 text-xs bg-slate-900 text-white hover:bg-blue-600 rounded-xl font-bold transition shadow-sm">Create Class</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL 2: Generate AI Assessment -->
        <div x-show="openAssessmentModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto" x-cloak style="display: none;">
            <div class="flex min-h-full items-start justify-center p-4 py-10 sm:py-16">
                <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-6" @click.away="openAssessmentModal = false">
                    
                    <div class="flex justify-between items-start border-b border-slate-100 pb-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-blue-50 text-blue-700 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-blue-200">Powered by Gemini AI</span>
                                <h3 class="text-xl font-extrabold text-slate-900">Generate Assessment</h3>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">Upload a lesson PDF to automatically generate a structured quiz or activity.</p>
                        </div>
                        <button type="button" @click="openAssessmentModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-2xl transition">&times;</button>
                    </div>

                    <form id="aiGenerationForm" method="POST" action="{{ route('teacher.assessment.deploy') }}" enctype="multipart/form-data" class="space-y-6" onsubmit="return handleAssessmentSubmit(event)">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Select Class</label>
                                <select name="math_class_id" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800 transition" required>
                                    <option value="" disabled selected>Choose a section...</option>
                                    @foreach(\App\Models\MathClass::where('teacher_id', Auth::id())->get() as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }} - {{ $class->section }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Assessment Title</label>
                                <input type="text" name="title" placeholder="e.g. Quarter 1 Multiplication Quiz" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800 transition" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Assessment Type</label>
                                <select name="type" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800 transition" required>
                                    <option value="quiz">Quiz</option>
                                    <option value="activity">Activity</option>
                                    <option value="assignment">Assignment</option>
                                    <option value="examination">Examination</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Lesson Topic</label>
                                <input type="text" name="topic" placeholder="e.g. Fractions, Algebra, Calculus" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800 transition" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Lesson Reference (PDF max 10MB)</label>
                            <div class="relative">
                                <input type="file" id="pdf_document" name="pdf_document" accept="application/pdf" class="w-full border border-slate-200 rounded-xl p-2.5 text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition focus:outline-none focus:ring-2 focus:ring-slate-900/10" required>
                            </div>
                        </div>

                        <!-- Dynamic Parts Matrix Builder -->
                        <div class="border-t border-slate-100 pt-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider">Assessment Structure</label>
                                <button type="button" id="addPartBtn" class="text-xs bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900 font-bold px-3 py-1.5 rounded-lg transition border border-slate-200 shadow-sm flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add Test Part
                                </button>
                            </div>

                            <div id="partsContainer" class="space-y-3">
                                <!-- Default Part Row -->
                                <div class="flex items-center gap-3 p-3.5 bg-slate-50 rounded-2xl border border-slate-200 part-row transition-all">
                                    <span class="font-extrabold text-xs text-slate-500 part-label min-w-[50px]">Part 1:</span>
                                    
                                    <select name="part_types[]" class="border-slate-200 rounded-xl text-xs font-semibold focus:ring-slate-900 focus:border-slate-900 flex-grow p-2.5 text-slate-800 transition" required>
                                        <option value="multiple_choice">Multiple Choice</option>
                                        <option value="fill_in_the_blank">Fill in the Blank</option>
                                        <option value="problem_solving">Problem Solving</option>
                                        <option value="computation">Computation</option>
                                    </select>
                                    
                                    <input type="number" name="part_counts[]" value="5" min="1" max="20" class="w-20 border-slate-200 rounded-xl text-xs font-bold text-center focus:ring-slate-900 focus:border-slate-900 p-2.5 text-slate-800 transition" required placeholder="Qty">
                                    <span class="text-xs font-medium text-slate-500">items</span>
                                </div>
                            </div>
                        </div>

                        <!-- Offline Test Mode Checkbox -->
                        <div class="flex items-center gap-2 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                            <input type="checkbox" name="bypass_ai" id="bypass_ai" value="1" class="rounded border-slate-300 text-slate-900 focus:ring-slate-900 w-4 h-4 transition">
                            <label for="bypass_ai" class="text-xs font-bold text-slate-700 cursor-pointer">
                                Enable Offline Test Mode <span class="font-normal text-slate-500">(Skip Gemini API call and generate mock items instantly)</span>
                            </label>
                        </div>

                        <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                            <button type="button" @click="openAssessmentModal = false" class="px-5 py-2.5 text-xs text-slate-600 hover:bg-slate-100 rounded-xl font-bold transition">Cancel</button>
                            <button type="submit" id="submitAssessmentBtn" class="px-6 py-2.5 text-xs bg-slate-900 text-white hover:bg-blue-600 rounded-xl font-bold transition shadow-md flex items-center gap-2">
                                <span>Generate Assessment</span>
                                <span>&rarr;</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL 3: Edit Class Parameters -->
        <div x-show="openEditModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm overflow-y-auto" x-cloak style="display: none;">
            <div class="flex min-h-full items-start justify-center p-4 py-10 sm:py-16">
                <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-6" @click.away="openEditModal = false">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                        <h3 class="text-xl font-extrabold text-slate-900">Edit Class Details</h3>
                        <button @click="openEditModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
                    </div>

                    <form method="POST" :action="editFormAction" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Grade Level</label>
                            <select name="class_name" x-model="editGrade" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800" required>
                                <option value="Grade 1">Grade 1</option>
                                <option value="Grade 2">Grade 2</option>
                                <option value="Grade 3">Grade 3</option>
                                <option value="Grade 4">Grade 4</option>
                                <option value="Grade 5">Grade 5</option>
                                <option value="Grade 6">Grade 6</option>
                                <option value="Grade 7">Grade 7</option>
                                <option value="Grade 8">Grade 8</option>
                                <option value="Grade 9">Grade 9</option>
                                <option value="Grade 10">Grade 10</option>
                                <option value="Grade 11">Grade 11</option>
                                <option value="Grade 12">Grade 12</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Section Name</label>
                            <input type="text" name="section" x-model="editSection" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800" required>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">School Year</label>
                            <select name="school_year" x-model="editYear" class="w-full border-slate-200 rounded-xl text-sm focus:ring-slate-900 focus:border-slate-900 p-3 font-semibold text-slate-800" required>
                                <option value="" disabled>Select School Year</option>
                                @foreach($schoolYears as $sy)
                                    <option value="{{ $sy->year }}">{{ $sy->year }} ({{ $sy->semester }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                            <button type="button" @click="openEditModal = false" class="px-5 py-2.5 text-xs text-slate-600 hover:bg-slate-100 rounded-xl font-bold transition">Cancel</button>
                            <button type="submit" class="px-6 py-2.5 text-xs bg-slate-900 text-white hover:bg-blue-600 rounded-xl font-bold transition shadow-sm">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- JavaScript to Handle Validation, Loader, and Dynamic Rows -->
    <script>
        function handleAssessmentSubmit(event) {
            const fileInput = document.getElementById('pdf_document');
            const bypassAi = document.getElementById('bypass_ai').checked;
            const partsContainer = document.getElementById('partsContainer');
            
            if (partsContainer.children.length === 0) {
                event.preventDefault();
                alert('Validation Error: You must add at least one Assessment Structure part.');
                return false;
            }

            if (fileInput.files.length > 0) {
                const fileSizeInMB = fileInput.files[0].size / 1024 / 1024;
                if (fileSizeInMB > 10) {
                    event.preventDefault();
                    alert('Validation Error: The selected PDF exceeds the 10MB limit. Please upload a smaller file.');
                    return false;
                }
            }

            const loaderOverlay = document.getElementById('aiLoadingOverlay');
            const loaderTitle = document.getElementById('aiLoaderTitle');
            const loaderDesc = document.getElementById('aiLoaderDesc');
            const submitBtn = document.getElementById('submitAssessmentBtn');

            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>Processing...</span>
            `;

            if (bypassAi) {
                loaderTitle.innerText = "Generating Offline Test";
                loaderDesc.innerText = "Bypassing AI and creating mock items instantly. Please hold...";
            } else {
                loaderTitle.innerText = "Gemini AI is Working";
                loaderDesc.innerText = "Analyzing your PDF document and synthesizing structured evaluation items. This usually takes 15-30 seconds.";
            }

            loaderOverlay.classList.remove('hidden');
            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('partsContainer');
            const addBtn = document.getElementById('addPartBtn');

            if (addBtn && container) {
                addBtn.addEventListener('click', function() {
                    const partCount = container.children.length + 1;
                    const newRow = document.createElement('div');
                    newRow.className = 'flex items-center gap-3 p-3.5 bg-slate-50 rounded-2xl border border-slate-200 part-row transition-all';
                    newRow.innerHTML = `
                        <span class="font-extrabold text-xs text-slate-500 part-label min-w-[50px]">Part ${partCount}:</span>
                        <select name="part_types[]" class="border-slate-200 rounded-xl text-xs font-semibold focus:ring-slate-900 focus:border-slate-900 flex-grow p-2.5 text-slate-800 transition" required>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="fill_in_the_blank">Fill in the Blank</option>
                            <option value="problem_solving">Problem Solving</option>
                            <option value="computation">Computation</option>
                        </select>
                        <input type="number" name="part_counts[]" value="5" min="1" max="20" class="w-20 border-slate-200 rounded-xl text-xs font-bold text-center focus:ring-slate-900 focus:border-slate-900 p-2.5 text-slate-800 transition" required placeholder="Qty">
                        <span class="text-xs font-medium text-slate-500">items</span>
                        <button type="button" class="text-red-500 hover:text-red-700 font-black text-sm px-2 remove-row transition">&times;</button>
                    `;
                    container.appendChild(newRow);
                });

                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-row')) {
                        e.target.closest('.part-row').remove();
                        document.querySelectorAll('.part-label').forEach((label, idx) => {
                            label.textContent = `Part ${idx + 1}:`;
                        });
                    }
                });
            }

            const searchInput = document.getElementById('classSearch');
            if(searchInput) {
                searchInput.addEventListener('input', function(e) {
                    let searchTerm = e.target.value.toLowerCase();
                    let cards = document.querySelectorAll('.class-card');
                    
                    cards.forEach(card => {
                        let className = card.getAttribute('data-name').toLowerCase();
                        if(className.includes(searchTerm)) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>