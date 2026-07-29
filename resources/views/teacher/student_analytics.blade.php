<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen" x-data="{ openSmsModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- System Notifications -->
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
                <div class="space-y-2 relative z-10">
                    <a href="{{ route('teacher.classes.show', $mathClass->id) }}" class="text-slate-400 hover:text-white transition text-xs font-bold inline-flex items-center gap-1 uppercase tracking-wider mb-2">
                        &larr; Back to Class Dashboard
                    </a>
                    <br>
                    <div class="inline-flex items-center gap-2 bg-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-blue-500/30">
                        Student Profile
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight">{{ $student->first_name }} {{ $student->last_name }}</h2>
                    <p class="text-slate-400 text-sm">Detailed performance report and learning history.</p>
                </div>
                <div class="relative z-10 flex flex-col md:flex-row gap-3 w-full md:w-auto">
                    <!-- WIRED SMS BUTTON -->
                    <button type="button" @click="openSmsModal = true" class="bg-slate-800 hover:bg-slate-700 text-white font-bold py-3.5 px-6 rounded-xl border border-slate-700 shadow-sm transition flex justify-center items-center gap-2 text-xs uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        Send SMS Alert
                    </button>
                    
                    <form method="POST" action="{{ route('teacher.generate.intervention', $student->id) }}" class="w-full md:w-auto">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 px-6 rounded-xl shadow-lg transition flex items-center justify-center gap-2 text-xs uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg> 
                            Generate Learning Module
                        </button>
                    </form>
                </div>
            </div>

            <!-- Performance Overview Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Risk & Weakness -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight pb-4 border-b border-slate-100">Performance Overview</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2">Current Risk Level</p>
                            @php $risk = $student->pivot->current_risk_level ?? 'Low'; @endphp
                            <span class="px-4 py-2 rounded-xl font-black text-xs uppercase tracking-wider inline-block {{ $risk == 'High' ? 'bg-red-100 text-red-700 border border-red-200' : ($risk == 'Moderate' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200') }}">
                                {{ $risk }} Risk
                            </span>
                        </div>

                        <div>
                            <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2">Identified Objective Gap</p>
                            @php $weakness = $student->pivot->diagnosed_weak_competency ?? 'None Identified'; @endphp
                            <span class="px-4 py-2 rounded-xl font-black text-xs inline-block {{ ($weakness == 'None' || $weakness == 'None Identified') ? 'bg-slate-100 text-slate-600 border border-slate-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                {{ $weakness }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Average Score -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 flex flex-col justify-center items-center text-center space-y-3">
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Average Test Score</p>
                    <div class="text-6xl font-black text-slate-900 tracking-tight">
                        {{ $submissions->count() > 0 ? round($submissions->avg('calculated_score')) : 0 }}%
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Based on <span class="text-slate-800 font-bold">{{ $submissions->count() }}</span> completed assessments</p>
                </div>
            </div>

            <!-- Assessment History -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-slate-100">
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Assessment History</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Chronological record of the student's test scores and results.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-black text-slate-500 uppercase tracking-widest">
                                <th class="p-5">Date</th>
                                <th class="p-5">Assessment Title</th>
                                <th class="p-5">Score</th>
                                <th class="p-5">System Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($submissions as $sub)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-5 text-slate-600 whitespace-nowrap text-xs font-mono">
                                        {{ $sub->created_at->format('M d, Y') }}<br>
                                        <span class="text-slate-400">{{ $sub->created_at->format('h:i A') }}</span>
                                    </td>
                                    <td class="p-5">
                                        <p class="font-bold text-slate-900">{{ $sub->assessment->title }}</p>
                                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mt-0.5">{{ $sub->assessment->type }} — {{ $sub->assessment->topic }}</p>
                                    </td>
                                    <td class="p-5">
                                        <span class="font-black text-base {{ $sub->calculated_score >= 75 ? 'text-emerald-700' : 'text-red-700' }}">
                                            {{ $sub->calculated_score }}%
                                        </span>
                                    </td>
                                    <td class="p-5 text-sm">
                                        @if($sub->ai_weakness_diagnosis && $sub->ai_weakness_diagnosis !== 'None Identified')
                                            <span class="text-red-700 font-bold bg-red-50 px-3 py-1 rounded-lg border border-red-200 text-xs inline-block">Struggled: {{ $sub->ai_weakness_diagnosis }}</span>
                                        @elseif($sub->calculated_score == 100)
                                            <span class="text-emerald-700 font-bold bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-200 text-xs inline-block">Perfect Score</span>
                                        @else
                                            <span class="text-slate-600 font-bold bg-slate-100 px-3 py-1 rounded-lg border border-slate-200 text-xs inline-block">Standard Result</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-16 text-center text-slate-400 text-sm italic">
                                        No assessments have been recorded for this student yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Generated AI Lessons -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-slate-100">
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Assigned Learning Modules</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Learning modules assigned to support the student's progress.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-black text-slate-500 uppercase tracking-widest">
                                <th class="p-5">Date Assigned</th>
                                <th class="p-5">Lesson Title</th>
                                <th class="p-5">Status</th>
                                <th class="p-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse(\App\Models\LearningMaterial::where('student_id', $student->id)->where('type', 'ai_intervention')->latest()->get() as $intervention)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-5 text-slate-600 whitespace-nowrap text-xs font-mono">
                                        {{ $intervention->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="p-5">
                                        <p class="font-bold text-slate-900">{{ $intervention->title }}</p>
                                    </td>
                                    <td class="p-5">
                                        @if($intervention->status === 'completed')
                                            <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200 inline-block">Completed</span>
                                        @else
                                            <span class="bg-amber-50 text-amber-700 text-xs font-bold px-3 py-1 rounded-full border border-amber-200 inline-block">Pending</span>
                                        @endif
                                    </td>
                                    <td class="p-5 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('teacher.intervention.edit', $intervention->id) }}" class="bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-xs px-4 py-2 rounded-xl transition shadow-sm">
                                                Edit Module
                                            </a>

                                            <form action="{{ route('teacher.intervention.destroy', $intervention->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this lesson?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 font-bold text-xs px-4 py-2 rounded-xl transition shadow-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-16 text-center text-slate-400 text-sm italic">
                                        No learning modules have been generated for this student yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
                            <p class="text-xs text-slate-500 mt-1">Send an SMS update to <span class="font-bold text-slate-700">{{ $student->first_name }}</span>'s parent.</p>
                        </div>
                        <button @click="openSmsModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
                    </div>

                    <form method="POST" action="{{ route('teacher.message.parent', $student->id) }}" x-data="{ text: '' }" class="space-y-4">
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