<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Header Section -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="space-y-2 relative z-10">
                    <a href="{{ route('student.dashboard') }}" class="text-slate-400 hover:text-white transition text-xs font-bold inline-flex items-center gap-1 uppercase tracking-wider mb-2">
                        &larr; Back to Dashboard
                    </a>
                    <h2 class="text-3xl font-extrabold tracking-tight">My Performance</h2>
                    <p class="text-slate-400 text-sm">Review your recent assessment results and scores.</p>
                </div>
            </div>

            <!-- Performance Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-black text-slate-500 uppercase tracking-widest">
                                <th class="p-5">Date Recorded</th>
                                <th class="p-5">Assessment Details</th>
                                <th class="p-5 text-center">Score</th>
                                <th class="p-5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($submissions as $sub)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-5 text-slate-600 whitespace-nowrap text-xs font-mono">
                                        {{ $sub->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="p-5">
                                        <p class="font-bold text-slate-900">{{ $sub->assessment->title }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5"><span class="uppercase tracking-wider font-semibold">{{ $sub->assessment->type }}</span> — {{ $sub->assessment->topic }}</p>
                                    </td>
                                    <td class="p-5 text-center">
                                        <span class="font-black text-xl {{ $sub->calculated_score >= 75 ? 'text-emerald-600' : 'text-red-600' }}">
                                            {{ $sub->calculated_score }}%
                                        </span>
                                    </td>
                                    <td class="p-5 text-sm">
                                        @if($sub->calculated_score >= 90)
                                            <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1.5 rounded-lg border border-emerald-200">Excellent</span>
                                        @elseif($sub->calculated_score >= 75)
                                            <span class="bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-lg border border-blue-200">Proficient</span>
                                        @elseif($sub->calculated_score >= 60)
                                            <span class="bg-amber-50 text-amber-700 text-xs font-bold px-3 py-1.5 rounded-lg border border-amber-200">Needs Support</span>
                                        @else
                                            <span class="bg-red-50 text-red-700 text-xs font-bold px-3 py-1.5 rounded-lg border border-red-200">Requires Review</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-16 text-center text-slate-400 text-sm italic">
                                        No performance records have been recorded yet.
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