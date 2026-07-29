<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- System Notifications -->
            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-2xl shadow-sm flex items-center justify-between">
                    <p class="text-sm text-emerald-800 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Executive Header Section -->
            <div class="bg-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="space-y-2 relative z-10">
                    <div class="inline-flex items-center gap-2 bg-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-blue-500/30">
                        Parent Access Workspace
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight">Parent Dashboard</h2>
                    <p class="text-slate-400 text-sm">Welcome, <span class="text-slate-200 font-bold">{{ Auth::user()->first_name }}</span>. Monitor learner progress and performance insights for your linked learners.</p>
                </div>
            </div>

            <!-- Dependent Telemetry Grid -->
            <div class="space-y-6">
                <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Linked Learner Profiles</h3>
                        <p class="text-xs text-slate-500 mt-1">Select a profile to view performance insights and learning support resources.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @if(isset($children) && $children->count() > 0)
                        @foreach($children as $child)
                            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 flex flex-col justify-between hover:border-slate-400 transition shadow-sm group">
                                <div class="space-y-4">
                                    <div class="flex justify-between items-start">
                                        <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center shadow-sm">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        
                                        <!-- Predictive Risk Badge -->
                                        @php $risk = $child->risk_level ?? 'Evaluating'; @endphp
                                        @if($risk === 'High')
                                            <span class="bg-red-50 text-red-700 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-red-200">Critical Risk</span>
                                        @elseif($risk === 'Moderate')
                                            <span class="bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-amber-200">Elevated Risk</span>
                                        @elseif($risk === 'Low')
                                            <span class="bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-emerald-200">Stable</span>
                                        @else
                                            <span class="bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border border-slate-200">Pending Data</span>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-extrabold text-slate-900 text-xl group-hover:text-blue-600 transition">{{ $child->first_name }} {{ $child->last_name }}</h4>
                                        <p class="text-xs text-slate-500 font-mono mt-1">{{ $child->email }}</p>
                                    </div>
                                    
                                    <div class="text-xs border-t border-slate-100 pt-4 flex flex-col gap-2">
                                        <div class="flex justify-between items-center">
                                            <span class="uppercase tracking-wider font-bold text-slate-400">Current Status:</span>
                                            <span class="font-semibold text-slate-700">Active Enrollment</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="uppercase tracking-wider font-bold text-slate-400">Objective Gap:</span>
                                            <span class="font-semibold {{ ($child->weak_competency && $child->weak_competency !== 'None') ? 'text-red-600' : 'text-emerald-600' }}">
                                                {{ $child->weak_competency ?? 'Evaluating' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <a href="{{ route('parent.child.analytics', $child->id) }}" class="block w-full text-center bg-slate-50 border border-slate-200 hover:bg-slate-900 hover:border-slate-900 hover:text-white text-slate-800 font-bold py-3.5 rounded-xl transition text-xs uppercase tracking-wider shadow-sm">
                                        View Learner Insights &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-full py-16 text-center border-2 border-dashed border-slate-200 rounded-3xl bg-slate-50/50">
                            <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <p class="text-slate-700 font-bold text-base">No Dependents Linked</p>
                            <p class="text-slate-400 text-xs mt-1 max-w-md mx-auto">There are no learner profiles currently associated with your parent account. Please contact the platform administrator to establish authorization links.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>