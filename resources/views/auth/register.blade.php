<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Glow Effects -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-400/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-2xl relative z-10 text-center space-y-3 mb-8">
            <!-- Brand Logo matching the screenshot -->
            <div class="flex items-center justify-center gap-3 mb-6">
                <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="text-3xl font-black tracking-widest text-white">PROGNOSMATH</span>
            </div>

            <h2 class="text-3xl font-black text-white tracking-tight">Create an Account</h2>
            <p class="text-xs font-semibold text-indigo-200 uppercase tracking-widest">Register your student profile</p>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-2xl relative z-10">
            <div class="bg-white/95 backdrop-blur-xl py-8 px-6 shadow-2xl rounded-3xl border border-slate-100 sm:px-10">
                
                <form method="POST" action="{{ route('register') }}" class="space-y-8" x-data="{ showPassword: false, showConfirmPassword: false, hasParent: false }">
                    @csrf

                    <!-- SECTION 1: STUDENT INFORMATION -->
                    <div>
                        <h3 class="text-lg font-black text-slate-900 border-b border-slate-200 pb-2 mb-4">Student Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">First Name</label>
                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus
                                    class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50">
                                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Last Name</label>
                                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required
                                    class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50">
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>

                            <!-- Student Email -->
                            <div>
                                <label for="email" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Student Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                    class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50"
                                    placeholder="student@school.edu">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Grade Level -->
                            <div>
                                <label for="grade_level" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Grade Level</label>
                                <select id="grade_level" name="grade_level" required class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50">
                                    <option value="" disabled {{ old('grade_level') ? '' : 'selected' }}>Select Grade Level</option>
                                    <option value="Grade 1" {{ old('grade_level') == 'Grade 1' ? 'selected' : '' }}>Grade 1</option>
                                    <option value="Grade 2" {{ old('grade_level') == 'Grade 2' ? 'selected' : '' }}>Grade 2</option>
                                    <option value="Grade 3" {{ old('grade_level') == 'Grade 3' ? 'selected' : '' }}>Grade 3</option>
                                    <option value="Grade 4" {{ old('grade_level') == 'Grade 4' ? 'selected' : '' }}>Grade 4</option>
                                    <option value="Grade 5" {{ old('grade_level') == 'Grade 5' ? 'selected' : '' }}>Grade 5</option>
                                    <option value="Grade 6" {{ old('grade_level') == 'Grade 6' ? 'selected' : '' }}>Grade 6</option>
                                    <option value="Grade 7" {{ old('grade_level') == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                                    <option value="Grade 8" {{ old('grade_level') == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                                    <option value="Grade 9" {{ old('grade_level') == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                                    <option value="Grade 10" {{ old('grade_level') == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                                    <option value="Grade 11" {{ old('grade_level') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                                    <option value="Grade 12" {{ old('grade_level') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                                </select>
                                <x-input-error :messages="$errors->get('grade_level')" class="mt-2" />
                            </div>

                            <!-- Password with Alpine Toggle -->
                            <div>
                                <label for="password" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                                <div class="relative">
                                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password"
                                        class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50 pr-10"
                                        placeholder="••••••••">
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.682-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692 4.692a3 3 0 01-4.243-4.243"/></svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password with Alpine Toggle -->
                            <div>
                                <label for="password_confirmation" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Confirm Password</label>
                                <div class="relative">
                                    <input id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                                        class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50 pr-10"
                                        placeholder="••••••••">
                                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                        <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.682-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692 4.692a3 3 0 01-4.243-4.243"/></svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- OPTIONAL PARENT TOGGLE -->
                    <div class="pt-4 border-t border-slate-200">
                        <label class="flex items-center cursor-pointer bg-slate-50 p-4 rounded-2xl border border-slate-200 hover:bg-slate-100 transition">
                            <div class="relative">
                                <input type="checkbox" x-model="hasParent" name="has_parent" class="sr-only">
                                <div class="block bg-slate-300 w-10 h-6 rounded-full transition-colors" :class="hasParent ? 'bg-[#303070]' : 'bg-slate-300'"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform" :class="hasParent ? 'transform translate-x-4' : ''"></div>
                            </div>
                            <div class="ml-4">
                                <span class="block text-sm font-extrabold text-slate-800">Add Parent/Guardian Details (Optional)</span>
                            </div>
                        </label>
                    </div>

                    <!-- SECTION 2: PARENT LINK (HIDDEN BY DEFAULT) -->
                    <div x-show="hasParent" x-collapse x-cloak>
                        <h3 class="text-lg font-black text-slate-900 border-b border-slate-200 pb-2 mb-4">Parent Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            
                            <!-- Parent First Name -->
                            <div>
                                <label for="parent_first_name" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Parent First Name</label>
                                <input id="parent_first_name" type="text" name="parent_first_name" value="{{ old('parent_first_name') }}" :required="hasParent"
                                    class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50">
                                <x-input-error :messages="$errors->get('parent_first_name')" class="mt-2" />
                            </div>

                            <!-- Parent Last Name -->
                            <div>
                                <label for="parent_last_name" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Parent Last Name</label>
                                <input id="parent_last_name" type="text" name="parent_last_name" value="{{ old('parent_last_name') }}" :required="hasParent"
                                    class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50">
                                <x-input-error :messages="$errors->get('parent_last_name')" class="mt-2" />
                            </div>

                            <!-- Parent Email -->
                            <div>
                                <label for="parent_email" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Parent Email</label>
                                <input id="parent_email" type="email" name="parent_email" value="{{ old('parent_email') }}" :required="hasParent"
                                    class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50"
                                    placeholder="parent@example.com">
                                <x-input-error :messages="$errors->get('parent_email')" class="mt-2" />
                            </div>

                            <!-- Parent Mobile Number (For SMS) -->
                            <div>
                                <label for="parent_phone" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Mobile Number</label>
                                <input id="parent_phone" type="tel" name="parent_phone" value="{{ old('parent_phone') }}" :required="hasParent" 
                                    class="w-full rounded-xl border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#303070] focus:ring-[#303070] p-3.5 bg-slate-50/50"
                                    placeholder="63 9171234567">
                                <x-input-error :messages="$errors->get('parent_phone')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-xs font-extrabold uppercase tracking-wider text-white bg-[#303070] hover:bg-[#404085] focus:outline-none transition-all duration-200">
                            Register Account &rarr;
                        </button>
                    </div>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                    <p class="text-xs font-bold text-slate-500">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-[#303070] hover:text-indigo-700 transition">Sign in here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>