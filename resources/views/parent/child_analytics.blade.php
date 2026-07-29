<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Executive Header Section -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="space-y-2 relative z-10">
                    <h2 class="text-3xl font-extrabold tracking-tight">{{ $child->first_name }} {{ $child->last_name }}</h2>
                    <p class="text-slate-400 text-sm">Learner Performance Insights & Progress Report</p>
                </div>
                
                <div class="relative z-10 flex flex-col items-start md:items-end gap-4 w-full md:w-auto">
                    <!-- Back button positioned to the top right -->
                    <a href="{{ route('parent.dashboard') }}" class="text-slate-400 hover:text-white transition text-xs font-bold inline-flex items-center gap-1 uppercase tracking-wider bg-slate-800/80 border border-slate-700 px-4 py-2 rounded-xl shadow-sm self-start md:self-end">
                        &larr; Return to Dashboard
                    </a>

                    <div class="bg-slate-800/80 px-6 py-4 rounded-2xl border border-slate-700 text-center shadow-inner mt-2">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400 block">Clearance Level</span>
                        <div class="text-sm font-black text-emerald-400 mt-1 uppercase tracking-widest">Read-Only Access</div>
                    </div>
                </div>
            </div>

            <!-- Telemetry Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Algorithmic Profile -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
                    <h3 class="text-lg font-extrabold text-slate-900 tracking-tight pb-4 border-b border-slate-100">Performance Overview</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2">Systemic Risk Classification</p>
                            @php $risk = $child->risk_level ?? 'Evaluating'; @endphp
                            <span class="px-4 py-2 rounded-xl font-black text-xs uppercase tracking-wider inline-block {{ $risk == 'High' ? 'bg-red-100 text-red-700 border border-red-200' : ($risk == 'Moderate' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200') }}">
                                {{ $risk }} Risk Vector
                            </span>
                            @if($risk == 'High')
                                <p class="text-xs text-slate-500 mt-2 font-medium">The platform has flagged this profile for elevated risk, and learning support is already in progress.</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2">Identified Objective Gap</p>
                            @php $weakness = $child->weak_competency ?? 'None Identified'; @endphp
                            <span class="px-4 py-2 rounded-xl font-black text-xs inline-block {{ ($weakness == 'None' || $weakness == 'None Identified') ? 'bg-slate-100 text-slate-600 border border-slate-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                {{ $weakness }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Aggregated Score Index -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 flex flex-col justify-center items-center text-center space-y-3">
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Average Performance</p>
                    <div class="text-6xl font-black text-slate-900 tracking-tight">
                        {{ $submissions->count() > 0 ? round($submissions->avg('calculated_score')) : 0 }}%
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Computed across <span class="text-slate-800 font-bold">{{ $submissions->count() }}</span> completed assessment records</p>
                </div>
            </div>

            <!-- Assessment History -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-slate-100">
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Assessment History</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Chronological record of submitted assessments, percentage scores, and diagnostic notes.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-black text-slate-500 uppercase tracking-widest">
                                <th class="p-5">Timestamp</th>
                                <th class="p-5">Assessment Designation</th>
                                <th class="p-5 text-center">Evaluated Score</th>
                                <th class="p-5">Diagnostic Evaluation</th>
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
                                        <p class="font-bold text-slate-900">{{ $sub->assessment->title ?? 'Deleted Assessment' }}</p>
                                        @if($sub->assessment)
                                            <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mt-0.5">{{ $sub->assessment->type }} — {{ $sub->assessment->topic }}</p>
                                        @endif
                                    </td>
                                    <td class="p-5 text-center">
                                        <span class="font-black text-base {{ $sub->calculated_score >= 75 ? 'text-emerald-700' : 'text-red-700' }}">
                                            {{ $sub->calculated_score }}%
                                        </span>
                                    </td>
                                    <td class="p-5 text-sm">
                                        @if($sub->ai_weakness_diagnosis && $sub->ai_weakness_diagnosis !== 'None Identified')
                                            <span class="text-red-700 font-bold bg-red-50 px-3 py-1 rounded-lg border border-red-200 text-xs inline-block">{{ $sub->ai_weakness_diagnosis }}</span>
                                        @elseif($sub->calculated_score == 100)
                                            <span class="text-emerald-700 font-bold bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-200 text-xs inline-block">Nominal / No Anomalies</span>
                                        @else
                                            <span class="text-slate-600 font-bold bg-slate-100 px-3 py-1 rounded-lg border border-slate-200 text-xs inline-block">Standard Result</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-16 text-center text-slate-400 text-sm italic">
                                        No assessment logs recorded for this dependent.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Assigned Learning Modules -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-slate-100">
                    <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Assigned Learning Modules</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Track the learning modules assigned to your learner and their completion status.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-black text-slate-500 uppercase tracking-widest">
                                <th class="p-5">Deployment Timestamp</th>
                                <th class="p-5">Curriculum Module Title</th>
                                <th class="p-5">Completion Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($interventions as $intervention)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-5 text-slate-600 whitespace-nowrap text-xs font-mono">
                                        {{ $intervention->created_at->format('M d, Y — h:i A') }}
                                    </td>
                                    <td class="p-5">
                                        <p class="font-bold text-slate-900">{{ $intervention->title }}</p>
                                    </td>
                                    <td class="p-5">
                                        @if($intervention->status === 'completed')
                                            <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200 inline-block">Verified Complete</span>
                                        @else
                                            <span class="bg-amber-50 text-amber-700 text-xs font-bold px-3 py-1 rounded-full border border-amber-200 inline-block">Pending Execution</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-16 text-center text-slate-400 text-sm italic">
                                        No learning modules have been assigned to this learner yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>