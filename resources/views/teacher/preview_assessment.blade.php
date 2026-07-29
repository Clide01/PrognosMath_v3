<x-app-layout>
    <!-- ML Analysis Loading Overlay -->
    <div id="mlLoadingOverlay" class="fixed inset-0 z-[100] bg-slate-900/80 backdrop-blur-sm flex flex-col justify-center items-center hidden transition-opacity">
        <div class="bg-white p-8 rounded-3xl shadow-2xl flex flex-col items-center max-w-sm w-full mx-4 text-center border border-slate-100">
            <div class="relative w-16 h-16 mb-6">
                <div class="absolute inset-0 bg-emerald-500 rounded-full blur-xl opacity-20 animate-pulse"></div>
                <svg class="animate-spin relative z-10 w-16 h-16 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-extrabold text-slate-900 mb-2 tracking-tight">Machine Learning Analysis</h3>
            <p class="text-sm text-slate-500 font-medium leading-relaxed">Processing NLP difficulty heuristics and updating classroom risk trajectories...</p>
        </div>
    </div>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col justify-between items-start relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 bg-amber-500/20 text-amber-400 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-amber-500/30 mb-2">
                        Step 2 of 2: AI Review
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight">Review & Edit Assessment</h2>
                    <p class="text-slate-400 text-sm mt-1">Make any necessary adjustments to the AI-generated questions before analyzing difficulty and deploying to students.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('teacher.assessment.store') }}" class="space-y-6" onsubmit="document.getElementById('mlLoadingOverlay').classList.remove('hidden');">
                @csrf
                <!-- Pass metadata quietly to Step 3 -->
                @foreach($assessmentMetadata as $key => $value)
                    <input type="hidden" name="metadata[{{ $key }}]" value="{{ $value }}">
                @endforeach

                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-200/80 space-y-6">
                    @foreach($generatedQuestions as $index => $q)
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 relative group">
                            
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-xs font-black text-slate-500 uppercase tracking-widest bg-white px-3 py-1 rounded-lg border border-slate-200">
                                    Part {{ $q['part_number'] }} | {{ str_replace('_', ' ', $q['question_type']) }}
                                </span>
                            </div>
                            
                            <input type="hidden" name="questions[{{ $index }}][part_number]" value="{{ $q['part_number'] }}">
                            <input type="hidden" name="questions[{{ $index }}][question_type]" value="{{ $q['question_type'] }}">

                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Question Text</label>
                            <textarea name="questions[{{ $index }}][question_text]" class="w-full border-slate-300 focus:border-slate-900 focus:ring-slate-900 rounded-xl mb-4 text-sm font-semibold text-slate-800">{{ $q['question_text'] }}</textarea>

                            @if(isset($q['options']) && is_array($q['options']))
                                <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Options (JSON Format)</label>
                                <textarea name="questions[{{ $index }}][options]" class="w-full border-slate-300 focus:border-slate-900 focus:ring-slate-900 font-mono text-xs rounded-xl mb-4 text-slate-700 bg-slate-100">{{ json_encode($q['options']) }}</textarea>
                            @endif

                            <label class="block text-xs font-extrabold text-emerald-600 uppercase tracking-wider mb-2">Correct Answer</label>
                            <input type="text" name="questions[{{ $index }}][correct_answer]" value="{{ $q['correct_answer'] }}" class="w-full border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500 bg-emerald-50 rounded-xl text-sm font-bold text-emerald-800">
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('teacher.dashboard') }}" class="px-6 py-3 text-sm font-bold text-slate-600 hover:bg-slate-200 rounded-xl transition">Discard Draft</a>
                    <button type="submit" class="px-8 py-3 text-sm font-bold bg-slate-900 hover:bg-blue-600 active:bg-blue-700 text-white rounded-xl shadow-md transition flex items-center gap-2">
                        <span>Analyze & Deploy Assessment</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>