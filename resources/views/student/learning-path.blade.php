<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm flex items-center justify-between">
                    <p class="text-sm text-emerald-800 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Header Section -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="space-y-2 relative z-10">
                    <div class="inline-flex items-center gap-2 bg-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-blue-500/30">
                        My Learning Plan
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight">Personalized Learning Modules</h2>
                    <p class="text-slate-400 text-sm max-w-xl">Review modules selected for you based on the areas where you can improve most.</p>
                </div>
                <a href="{{ route('student.dashboard') }}" class="relative z-10 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-sm">
                    &larr; Back to Dashboard
                </a>
            </div>

            <!-- List of Modules -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
                <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                    <div class="space-y-1">
                        <h3 class="font-extrabold text-slate-900 text-xl tracking-tight">Assigned Learning Modules</h3>
                        <p class="text-xs text-slate-500">Targeted learning activities tailored to support your progress.</p>
                    </div>
                    <span class="bg-slate-100 text-slate-700 text-xs font-bold px-3 py-1.5 rounded-lg border border-slate-200">
                        {{ $aiInterventions->count() }} Modules Total
                    </span>
                </div>

                <div class="space-y-4">
                    @forelse($aiInterventions as $intervention)
                        <div class="border border-slate-200/80 p-6 rounded-2xl flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 hover:border-blue-400 hover:shadow-md transition bg-white group">
                            <div class="space-y-2">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="bg-blue-50 text-blue-700 text-[11px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md border border-blue-100">
                                        Module #{{ $intervention->id }}
                                    </span>
                                    <h4 class="font-bold text-slate-900 text-lg group-hover:text-blue-600 transition">{{ $intervention->title }}</h4>

                                    @if($intervention->status === 'completed')
                                        <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Completed
                                        </span>
                                    @else
                                        <span class="bg-amber-50 text-amber-700 text-xs font-bold px-3 py-1 rounded-full border border-amber-200 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> Pending
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 font-medium">
                                    Assigned on: <span class="text-slate-700">{{ $intervention->created_at->format('M d, Y') }}</span>
                                </p>
                            </div>

                            <div class="flex items-center gap-3 w-full lg:w-auto">
                                <a href="{{ route('student.intervention.show', $intervention->id) }}" class="w-full lg:w-auto text-center bg-slate-900 hover:bg-blue-600 text-white font-bold text-xs px-6 py-3 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                                    <span>{{ $intervention->status === 'completed' ? 'Review Module' : 'Open Module' }}</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16 bg-slate-50/50 rounded-2xl border-2 border-dashed border-slate-200">
                            <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center mx-auto text-slate-400 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                            </div>
                            <p class="text-slate-800 font-bold text-base">No learning modules assigned right now.</p>
                            <p class="text-slate-400 text-xs mt-1 max-w-sm mx-auto">You’re on track. New modules will appear here when they’re needed.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>