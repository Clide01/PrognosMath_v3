<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Executive Header -->
            <div class="bg-slate-900 p-8 rounded-3xl shadow-xl border border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-amber-500/20 text-amber-400 border border-amber-500/30 px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest">Manual Override Active</span>
                    </div>
                    <h2 class="text-3xl font-extrabold text-white tracking-tight">Learning Module Update</h2>
                    <p class="text-slate-400 text-sm mt-1">Update the learning content before assigning it to students.</p>
                </div>
                <a href="{{ route('teacher.dashboard') }}" class="text-xs font-bold text-slate-300 hover:text-white uppercase tracking-wider transition bg-slate-800 border border-slate-700 px-5 py-2.5 rounded-xl shadow-sm">
                    &larr; Return to Dashboard
                </a>
            </div>

            <!-- Editor Interface -->
            <form action="{{ route('teacher.intervention.update', $intervention->id) }}" method="POST" class="bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-slate-200/80">
                @csrf
                @method('PUT')

                <div class="space-y-8">
                    <!-- Title Input -->
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Module Title</label>
                        <input type="text" name="title" value="{{ old('title', $intervention->title) }}" required
                            class="w-full border-slate-200 rounded-xl shadow-sm focus:ring-slate-900 focus:border-slate-900 p-4 font-semibold text-slate-900 text-lg transition">
                    </div>

                    <!-- Curriculum Payload Input -->
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider">Learning Module Content (HTML)</label>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 bg-slate-100 px-2 py-1 rounded border border-slate-200">Content Source</span>
                        </div>
                        <p class="text-xs text-slate-400 mb-3 font-medium">Update the HTML content below to refine instructions, steps, or supporting guidance.</p>
                        
                        <div class="relative">
                            <div class="absolute top-0 left-0 bottom-0 w-10 bg-slate-100 border-r border-slate-200 rounded-l-xl pointer-events-none flex flex-col items-center py-4 text-xs font-mono text-slate-400 space-y-2">
                                <!-- Decorative Line Numbers -->
                                <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                            </div>
                            <textarea name="content" rows="22" required
                                class="w-full pl-14 pr-4 py-4 border-slate-200 rounded-xl shadow-inner focus:ring-slate-900 focus:border-slate-900 font-mono text-sm text-slate-700 bg-slate-50 leading-relaxed transition custom-scrollbar">{{ old('content', $intervention->content) }}</textarea>
                        </div>
                    </div>

                    <!-- Action Footer -->
                    <div class="flex justify-end pt-6 border-t border-slate-100 gap-3">
                        <button type="submit" class="bg-slate-900 hover:bg-blue-600 text-white font-bold text-xs uppercase tracking-wider py-3.5 px-8 rounded-xl transition shadow-md flex items-center gap-2">
                            <span>Save Learning Module Changes</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>