<x-app-layout>
    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">System Announcements</h2>
                    <p class="text-slate-500 text-sm mt-1 font-medium">Broadcast messages to users across the platform.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-blue-600 text-xs font-bold uppercase tracking-widest transition">&larr; Dashboard</a>
            </div>

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-2xl shadow-sm">
                    <p class="text-sm text-emerald-800 font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm p-8 border border-slate-200/80">
                <form method="POST" action="{{ route('admin.announcements.store') }}" class="mb-10 border-b border-slate-100 pb-8 space-y-5">
                    @csrf
                    <div class="flex flex-col md:flex-row gap-5">
                        <div class="flex-1">
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5" for="title">Announcement Title</label>
                            <input id="title" name="title" type="text" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3 focus:ring-slate-900 focus:border-slate-900" required />
                        </div>
                        <div class="w-full md:w-1/3">
                            <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5" for="target_role">Target Audience</label>
                            <select id="target_role" name="target_role" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3 focus:ring-slate-900 focus:border-slate-900" required>
                                <option value="all">Everyone (All Users)</option>
                                <option value="teacher">Teachers Only</option>
                                <option value="student">Students Only</option>
                                <option value="parent">Parents Only</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5" for="message">Message Body</label>
                        <textarea id="message" name="message" rows="4" class="w-full rounded-xl border-slate-200 text-sm font-semibold p-3 focus:ring-slate-900 focus:border-slate-900 resize-none" required></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-slate-900 text-white font-bold py-3 px-6 rounded-xl hover:bg-blue-600 transition shadow-sm text-xs uppercase tracking-wider">Post Announcement</button>
                    </div>
                </form>

                <div class="space-y-4">
                    <h3 class="text-lg font-black text-slate-900 mb-4">Recent Announcements</h3>
                    @forelse($announcements as $announcement)
                        <div class="p-5 border border-slate-100 rounded-2xl bg-slate-50 hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-extrabold text-slate-900">{{ $announcement->title }}</h4>
                                <span class="bg-blue-100 text-blue-700 text-[10px] px-2.5 py-1 rounded-md uppercase font-black tracking-wider">Target: {{ $announcement->target_role }}</span>
                            </div>
                            <p class="text-sm text-slate-600 font-medium whitespace-pre-line">{{ $announcement->message }}</p>
                            <p class="text-xs text-slate-400 mt-4 font-bold">Posted on: {{ $announcement->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-500 font-bold text-sm">No announcements posted yet.</div>
                    @endforelse
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>