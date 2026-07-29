<x-app-layout>
    <!-- Clean Typography Styles -->
    <style>
        .ai-lesson { font-family: 'Inter', system-ui, sans-serif; color: #1e293b; }
        .ai-lesson h3 { font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem; letter-spacing: -0.025em; }
        .ai-lesson h4 { font-size: 1.15rem; font-weight: 700; color: #334155; margin-top: 2rem; margin-bottom: 0.75rem; }
        .ai-lesson p { font-size: 1.05rem; line-height: 1.75; color: #475569; margin-bottom: 1.25rem; }
        .ai-lesson ul, .ai-lesson ol { margin-left: 1.5rem; margin-bottom: 1.5rem; color: #475569; }
        .ai-lesson ul { list-style-type: square; }
        .ai-lesson ol { list-style-type: decimal; }
        .ai-lesson li { margin-bottom: 0.5rem; line-height: 1.65; }
        .ai-lesson strong { font-weight: 700; color: #0f172a; background-color: #f1f5f9; padding: 0.15rem 0.4rem; border-radius: 0.25rem; border: 1px solid #e2e8f0; }

        /* Translation Widget Overrides & Standardized Sizing */
        .skiptranslate iframe { display: none !important; }
        body { top: 0px !important; }
        .goog-te-gadget { color: transparent !important; font-size: 0; }
        
        /* Standardized Google Translate Dropdown Styling */
        .goog-te-gadget .goog-te-combo { 
            margin: 0 !important; 
            padding: 8px 12px !important; 
            border-radius: 10px !important; 
            color: #1e293b !important; 
            font-size: 12px !important; 
            font-weight: 600 !important; 
            outline: none !important; 
            border: 1px solid rgba(255, 255, 255, 0.2) !important; 
            cursor: pointer !important; 
            background-color: rgba(255, 255, 255, 0.9) !important; 
            width: 160px !important;
            max-width: 100% !important;
        }
        .goog-te-gadget .goog-te-combo:hover {
            background-color: #ffffff !important;
        }
    </style>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Header -->
            <div class="bg-slate-900 p-8 rounded-3xl shadow-xl text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute -left-10 -top-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 w-full md:w-auto">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-slate-800 text-slate-300 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-slate-700">Personalized Learning Module</span>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">{{ $intervention->title }}</h2>
                    <p class="text-slate-400 text-sm font-medium mt-1">Review the module and complete the guided practice exercises below.</p>
                </div>

                <div class="relative z-10 flex flex-col items-start md:items-end gap-4 w-full md:w-auto">
                    <a href="{{ route('student.learning-path.index') }}" class="text-xs font-bold text-slate-300 hover:text-white uppercase tracking-wider transition flex items-center gap-1 bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-2 rounded-xl shadow-sm">
                        &larr; Back to Learning Plan
                    </a>

                    <!-- Localization Widget -->
                    <div class="bg-white/10 backdrop-blur-md p-2 rounded-2xl border border-white/15 flex items-center gap-2.5 shadow-sm">
                        <span class="pl-1 text-slate-200 text-xs font-extrabold uppercase tracking-widest">Translate:</span>
                        <div id="google_translate_element"></div>
                    </div>
                </div>
            </div>

            <!-- Module Container -->
            <div class="bg-white p-8 md:p-12 rounded-3xl shadow-sm border border-slate-200/80">

                <!-- AI Content -->
                <div class="ai-lesson">
                    @if($intervention->content)
                        {!! $intervention->content !!}
                    @else
                        <div class="text-center py-16 bg-slate-50 border border-slate-200 border-dashed rounded-2xl">
                            <p class="text-slate-500 font-medium">Module content could not be loaded.</p>
                        </div>
                    @endif
                </div>

                <div class="my-12 border-t border-slate-200"></div>

                <!-- Response Form -->
                <form action="{{ route('student.intervention.submit', $intervention->id) }}" method="POST" class="bg-slate-50 p-8 rounded-3xl border border-slate-200/80">
                    @csrf
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-200">
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Your Response</h3>
                                <p class="text-xs text-slate-500 font-medium mt-1">Show your reasoning and enter your final responses for the guided exercises above.</p>
                            </div>
                        </div>

                        <textarea
                            name="student_answer"
                            rows="8"
                            class="w-full border-slate-300 rounded-2xl shadow-inner focus:ring-4 focus:ring-slate-900/10 focus:border-slate-900 p-5 text-slate-800 text-sm font-mono transition placeholder-slate-400"
                            placeholder="Type your responses here...&#10;Example:&#10;1) 5 x 4 = 20&#10;2) 10 + 5 = 15"
                            @if($intervention->status === 'completed') disabled @endif
                        >{{ old('student_answer', $intervention->student_answer) }}</textarea>

                        @error('student_answer')
                            <p class="text-red-600 text-xs mt-2 font-bold flex items-center gap-1">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> 
                                Error: {{ $message }}
                            </p>
                        @enderror
                    </div>

                    @if($intervention->status !== 'completed')
                        <div class="flex justify-end mt-4">
                            <button type="submit" class="bg-slate-900 hover:bg-blue-600 active:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider py-3.5 px-8 rounded-xl transition shadow-md flex items-center gap-2">
                                <span>Submit Response</span>
                                <span>&rarr;</span>
                            </button>
                        </div>
                    @else
                        <div class="bg-emerald-50 p-5 rounded-2xl border border-emerald-200 flex items-center justify-between">
                            <div>
                                <h4 class="font-bold text-emerald-800 text-sm">Response Submitted</h4>
                                <p class="text-xs text-emerald-600 font-medium mt-0.5">Your work has been recorded successfully.</p>
                            </div>
                            <span class="bg-emerald-100 text-emerald-700 text-xs font-black px-3 py-1.5 rounded-lg border border-emerald-200">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Done
                            </span>
                        </div>
                    @endif
                </form>

            </div>
        </div>
    </div>

    <!-- Localization Script -->
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,tl,ceb,hil,es',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</x-app-layout>