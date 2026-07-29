<nav x-data="{ open: false }" class="bg-indigo-900 border-b border-indigo-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-white font-black text-2xl tracking-widest flex items-center gap-2">
                        <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        PROGNOSMATH
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    
                    @if(Auth::user()->role === 'admin')
                        <!--<x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.teachers.index')" :active="request()->routeIs('admin.teachers.*')">
                            {{ __('Account Management') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.terms.index')" :active="request()->routeIs('admin.terms.*', 'admin.competencies.*')">
                            {{ __('Academic Setup') }}
                        </x-nav-link>-->

                    @elseif(Auth::user()->role === 'student')
                        <!--<x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.learning-path.index')" :active="request()->routeIs('student.learning-path.*')">
                            {{ __('AI Tutorials') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.grades.index')" :active="request()->routeIs('student.grades.*')">
                            {{ __('My Progress') }}
                        </x-nav-link>-->

                    @elseif(Auth::user()->role === 'parent')
                        <!--<x-nav-link :href="route('parent.dashboard')" :active="request()->routeIs('parent.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="'#'">
                            {{ __('Child Performance') }}
                        </x-nav-link>-->
                    @endif

                </div>
            </div>

            <!-- Settings Dropdown & Offline Indicator -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Offline Sync Status Indicator -->
                <div id="connection-status" class="mr-6 flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-500 text-white shadow shadow-emerald-500/50 transition-all duration-300" title="Data will sync automatically when online">
                    <span class="relative flex h-2 w-2 mr-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                    </span>
                    System Online
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-900 bg-indigo-50 hover:bg-white focus:outline-none transition ease-in-out duration-150 shadow-sm">
                            <div>{{ Auth::user()->first_name }} ({{ ucfirst(Auth::user()->role) }})</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Edit Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>