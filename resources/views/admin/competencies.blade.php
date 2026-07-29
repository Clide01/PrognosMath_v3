<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Mathematics Learning Objectives</h2>
                    <p class="text-slate-500 text-sm mt-1 font-medium">Map curriculum objectives for AI assessment generation.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-blue-600 text-xs font-bold uppercase tracking-widest transition">&larr; Dashboard</a>
            </div>

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-2xl shadow-sm">
                    <p class="text-sm text-emerald-800 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-1 bg-white rounded-3xl shadow-sm p-6 border border-slate-200/80 h-fit">
                    <h3 class="text-lg font-black text-slate-900 mb-4 border-b border-slate-100 pb-3">Add Objective</h3>
                    <form method="POST" action="{{ route('admin.competencies.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5" for="grade_level">Target Grade Level</label>
                            <select id="grade_level" name="grade_level" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3 focus:ring-slate-900 focus:border-slate-900" required>
                                <option value="Grade 1">Grade 1</option>
                                <option value="Grade 2">Grade 2</option>
                                <option value="Grade 3">Grade 3</option>
                                <option value="Grade 4">Grade 4</option>
                                <option value="Grade 5">Grade 5</option>
                                <option value="Grade 6">Grade 6</option>
                                <option value="Grade 7" selected>Grade 7</option>
                                <option value="Grade 8">Grade 8</option>
                                <option value="Grade 9">Grade 9</option>
                                <option value="Grade 10">Grade 10</option>
                                <option value="Grade 11">Grade 11</option>
                                <option value="Grade 12">Grade 12</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5" for="code">Objective Code</label>
                            <input id="code" name="code" type="text" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3 focus:ring-slate-900 focus:border-slate-900" placeholder="e.g. M7AL-IIa-1" required />
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5" for="description">Topic Description</label>
                            <textarea id="description" name="description" rows="3" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3 focus:ring-slate-900 focus:border-slate-900 resize-none" required></textarea>
                        </div>
                        <button type="submit" class="w-full bg-slate-900 text-white font-bold py-3 rounded-xl hover:bg-blue-600 transition shadow-sm text-xs uppercase tracking-wider mt-2">Save Objective</button>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm p-6 border border-slate-200/80">
                    <h3 class="text-lg font-black text-slate-900 mb-4 border-b border-slate-100 pb-3">Mapped Objectives</h3>
                    <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
                        @forelse($competencies as $competency)
                            <div class="border-l-4 border-blue-500 pl-4 py-3 bg-slate-50 rounded-r-2xl pr-4 flex justify-between items-center border-y border-r border-slate-100 hover:border-slate-300 transition">
                                <div>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $competency->grade_level }}</span>
                                    <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $competency->description }}</p>
                                </div>
                                <span class="text-xs font-mono font-bold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-md">{{ $competency->code }}</span>
                            </div>
                        @empty
                            <div class="text-center py-12 text-slate-500 font-bold text-sm bg-slate-50 rounded-2xl border border-slate-100">No objectives mapped yet.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>