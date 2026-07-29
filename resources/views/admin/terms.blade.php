<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Academic Terms</h2>
                    <p class="text-slate-500 text-sm mt-1 font-medium">Manage school years and active semesters.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-blue-600 text-xs font-bold uppercase tracking-widest transition">&larr; Dashboard</a>
            </div>

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-2xl shadow-sm">
                    <p class="text-sm text-emerald-800 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm p-8 border border-slate-200/80">
                <form method="POST" action="{{ route('admin.terms.store') }}" class="flex flex-col md:flex-row gap-4 items-end mb-10 border-b border-slate-100 pb-8">
                    @csrf
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5" for="year">School Year</label>
                        <input id="year" name="year" type="text" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3 focus:ring-slate-900 focus:border-slate-900" placeholder="e.g. 2026-2027" required />
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5" for="semester">Semester / Quarter</label>
                        <input id="semester" name="semester" type="text" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3 focus:ring-slate-900 focus:border-slate-900" placeholder="e.g. 1st Semester" required />
                    </div>
                    <button type="submit" class="w-full md:w-auto bg-slate-900 text-white font-bold py-3 px-6 rounded-xl hover:bg-blue-600 transition shadow-sm text-xs uppercase tracking-wider h-[46px]">Add Term</button>
                </form>

                <div class="space-y-4">
                    <h3 class="text-lg font-black text-slate-900 mb-4">Term History</h3>
                    @forelse($schoolYears as $year)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 rounded-2xl border transition-all {{ $year->is_active ? 'border-emerald-500 bg-emerald-50 shadow-sm' : 'border-slate-200 bg-slate-50 hover:border-slate-300' }}">
                            <div class="mb-4 sm:mb-0">
                                <p class="font-extrabold text-slate-900 text-lg">{{ $year->year }}</p>
                                <p class="text-sm font-semibold text-slate-500">{{ $year->semester }}</p>
                            </div>
                            <div>
                                @if($year->is_active)
                                    <span class="bg-emerald-500 text-white text-[10px] font-black tracking-widest px-4 py-2 rounded-lg shadow-sm uppercase">Active Term</span>
                                @else
                                    <form method="POST" action="{{ route('admin.terms.activate', $year->id) }}">
                                        @csrf
                                        <button type="submit" class="bg-white border border-slate-300 text-slate-700 text-xs font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-slate-100 hover:text-slate-900 transition uppercase tracking-wider">Set as Active</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-500 font-bold text-sm bg-slate-50 rounded-2xl border border-slate-100">No academic terms created yet.</div>
                    @endforelse
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>