<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Assessment Header -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="space-y-2 relative z-10">
                    <div class="inline-flex items-center gap-2 bg-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-blue-500/30">
                        {{ $assessment->type }}
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight">{{ $assessment->title }}</h2>
                    <p class="text-slate-400 text-sm mt-1">Topic: <span class="font-medium text-slate-200">{{ $assessment->topic }}</span></p>
                </div>
            </div>

            <!-- Quiz Form -->
            <form method="POST" id="quizForm" action="{{ route('student.quiz.submit', $assessment->id) }}" onsubmit="return handleQuizSubmit()">
                @csrf
                <input type="hidden" name="total_seconds_spent" id="total_seconds_spent" value="0">

                @php
                    $groupedQuestions = $assessment->questions->groupBy('part_number');
                @endphp

                @foreach($groupedQuestions as $partNumber => $questions)
                    <div class="mb-10">
                        <h3 class="text-xl font-extrabold text-slate-900 mb-6 border-b border-slate-200 pb-2">
                            Part {{ $partNumber }}
                        </h3>

                        <div class="space-y-6">
                            @foreach($questions as $index => $q)
                                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden grid grid-cols-1 lg:grid-cols-2 group hover:border-slate-300 transition">
                                    
                                    <!-- Question Area -->
                                    <div class="p-8 border-b lg:border-b-0 lg:border-r border-slate-100 flex flex-col">
                                        <div class="flex items-start gap-4 mb-6">
                                            <span class="bg-slate-100 text-slate-600 font-black px-3.5 py-1.5 rounded-lg text-sm border border-slate-200 shadow-sm mt-0.5">Q{{ $index + 1 }}</span>
                                            <h4 class="text-lg font-bold text-slate-800 leading-relaxed">{{ $q->question_text }}</h4>
                                        </div>
                                        
                                        <div class="mt-auto">
                                            <!-- MULTIPLE CHOICE -->
                                            @if($q->question_type === 'multiple_choice' && $q->options)
                                                <div class="space-y-3">
                                                    @foreach($q->options as $key => $text)
                                                        <label class="flex items-center p-4 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 hover:border-slate-300 transition group-focus-within:ring-2">
                                                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $key }}" class="w-5 h-5 text-slate-900 focus:ring-slate-900 border-slate-300" required>
                                                            <span class="ml-3 font-semibold text-slate-700 flex items-center gap-2">
                                                                <span class="text-slate-400 font-bold text-xs uppercase">{{ $key }}.</span> {{ $text }}
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            
                                            <!-- FILL IN THE BLANK -->
                                            @elseif($q->question_type === 'fill_in_the_blank')
                                                <input type="text" name="answers[{{ $q->id }}]" class="w-full border-slate-200 rounded-xl focus:border-slate-900 focus:ring-slate-900 p-4 font-semibold text-slate-800 placeholder-slate-400" placeholder="Type your answer here..." required>
                                            
                                            <!-- PROBLEM SOLVING / COMPUTATION -->
                                            @elseif($q->question_type === 'problem_solving' || $q->question_type === 'computation')
                                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Final Answer:</p>
                                                    <input type="text" name="answers[{{ $q->id }}]" class="w-full border-slate-200 rounded-lg focus:border-slate-900 focus:ring-slate-900 p-3 font-bold text-slate-900" placeholder="Ex: 15" required>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Scratchpad Area -->
                                    <div class="bg-slate-50/50 p-8 flex flex-col">
                                        <h4 class="font-bold text-slate-500 text-sm mb-3 flex items-center gap-2 uppercase tracking-wider">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg> 
                                            Scratchpad
                                        </h4>
                                        <textarea name="scratchpads[{{ $q->id }}]" class="w-full flex-grow border-slate-200 rounded-xl focus:border-slate-900 focus:ring-slate-900 font-mono text-sm p-4 text-slate-700 placeholder-slate-400 shadow-inner" placeholder="Show your work here... (Optional)"></textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-end pt-6 border-t border-slate-200">
                    <button type="submit" id="submitQuizBtn" class="bg-slate-900 hover:bg-blue-600 text-white font-extrabold text-sm uppercase tracking-wider py-4 px-12 rounded-xl shadow-lg transition flex items-center gap-2">
                        <span>Submit Assessment</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </form>
            
        </div>
    </div>

    <!-- PWA Offline Sync, Telemetry Timer, & UI Loader -->
    <script>
        function handleQuizSubmit() {
            const btn = document.getElementById('submitQuizBtn');
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>Evaluating Answers...</span>
            `;
            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Telemetry Timer
            let seconds = 0;
            const timerInput = document.getElementById('total_seconds_spent');
            setInterval(() => { seconds++; timerInput.value = seconds; }, 1000);

            // 2. Offline Intercept Logic
            const quizForm = document.getElementById('quizForm');
            const assessmentId = "{{ $assessment->id }}";

            quizForm.addEventListener('submit', function(e) {
                if (!navigator.onLine) {
                    e.preventDefault(); 
                    
                    const formData = new FormData(quizForm);
                    const submissionData = Object.fromEntries(formData.entries());
                    
                    localStorage.setItem('pending_quiz_' + assessmentId, JSON.stringify(submissionData));
                    
                    alert('[Offline Mode] You are currently offline. Do not worry! Your answers and scratchpad work have been saved securely to your device. Keep the app open; it will submit automatically when your internet returns.');
                    
                    const submitBtn = document.getElementById('submitQuizBtn');
                    submitBtn.innerText = "Saved Offline (Waiting for Sync...)";
                    submitBtn.disabled = true;
                    submitBtn.classList.add('bg-amber-500'); 
                }
            });

            // 3. BACKGROUND SYNC (When internet returns)
            window.addEventListener('online', function() {
                const pendingData = localStorage.getItem('pending_quiz_' + assessmentId);
                
                if (pendingData) {
                    alert('[Online Sync] Internet reconnected! Syncing your assessment to the ML server now...');
                    
                    quizForm.submit(); 
                    
                    localStorage.removeItem('pending_quiz_' + assessmentId);
                }
            });
        });
    </script>
</x-app-layout>