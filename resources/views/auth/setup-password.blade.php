<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden" x-data="{ showPassword: false, showConfirmPassword: false }">
        <!-- Background Glow Effects -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10 text-center space-y-3 mb-6">
            <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center mx-auto shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Account Activation</h2>
            <p class="text-xs font-semibold text-indigo-200 uppercase tracking-widest">Please set a permanent password</p>
        </div>

        <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
            <div class="bg-white/95 backdrop-blur-xl py-8 px-6 shadow-2xl rounded-3xl border border-slate-100 sm:px-10">
                
                <div class="bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold p-4 rounded-xl mb-6 flex gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p>For your security, you must replace your 6-digit Activation Code with a private password before continuing.</p>
                </div>

                <form method="POST" action="{{ route('password.setup.store') }}" class="space-y-5">
                    @csrf
                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">New Password</label>
                        <div class="relative">
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autofocus
                                class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50 pr-10">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.682-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692 4.692a3 3 0 01-4.243-4.243"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Confirm New Password</label>
                        <div class="relative">
                            <input id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required
                                class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50 pr-10">
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.682-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692 4.692a3 3 0 01-4.243-4.243"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-xs font-extrabold uppercase tracking-wider text-white bg-[#303070] hover:bg-[#404085] focus:outline-none transition-all duration-200">
                            Save Password & Access Dashboard &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>