<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden" x-data="{ showPassword: false }">
        <!-- Background Glow Effects -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-400/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10 text-center space-y-3 mb-6">
            <!-- Brand Logo matching the screenshot -->
            <div class="flex items-center justify-center gap-3 mb-6">
                <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="text-3xl font-black tracking-widest text-white">PROGNOSMATH</span>
            </div>
            
            <h2 class="text-3xl font-black text-white tracking-tight">Welcome back</h2>
            <p class="text-xs font-semibold text-indigo-200 uppercase tracking-widest">Sign in to access your dashboard</p>
        </div>

        <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="bg-white/95 backdrop-blur-xl py-8 px-6 shadow-2xl rounded-3xl border border-slate-100 sm:px-10">
                
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                            class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50"
                            placeholder="name@school.edu">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password with Alpine Toggle -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Password</label>
                            @if (Route::has('password.request'))
                                <a class="text-xs font-bold text-[#303070] hover:text-indigo-700 transition" href="{{ route('password.request') }}">
                                    Forgot?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50 pr-10"
                                placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.682-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692 4.692a3 3 0 01-4.243-4.243"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-[#303070] shadow-sm focus:ring-[#303070]" name="remember">
                            <span class="ml-2 text-xs font-bold text-slate-600">Keep me logged in</span>
                        </label>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-xs font-extrabold uppercase tracking-wider text-white bg-[#303070] hover:bg-[#404085] focus:outline-none transition-all duration-200">
                            Sign In to Account &rarr;
                        </button>
                    </div>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                    <p class="text-xs font-bold text-slate-500">
                        Don't have a student account? 
                        <a href="{{ route('register') }}" class="text-[#303070] hover:text-indigo-700 transition">Register here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>