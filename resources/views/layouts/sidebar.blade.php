<!-- Sidebar de bureau (Desktop) -->
<aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-slate-950 border-r border-slate-900 z-20">
    <!-- En-tête / Logo -->
    <div class="flex items-center h-16 px-6 border-b border-slate-900 bg-slate-950/50 backdrop-blur-sm">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
            <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 text-white shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform duration-200">
                <!-- Icone Logo (Shield/CRM) -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div>
                <span class="text-sm font-semibold text-white tracking-wider block">CRM COMMERCIAL</span>
                <span class="text-[10px] text-indigo-400 font-medium block uppercase tracking-widest -mt-0.5">Espace Ventes</span>
            </div>
        </a>
    </div>

    <!-- Liens de navigation principal -->
    <div class="flex-1 flex flex-col justify-between overflow-y-auto px-4 py-6 space-y-7 custom-scrollbar">
        <nav class="space-y-6">
            <!-- SECTION : ACCUEIL -->
            <div class="space-y-2">
                <span class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Menu</span>
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <!-- Icon: Home -->
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </x-slot>
                    Dashboard
                </x-sidebar-link>
            </div>

            <!-- SECTION : CRM CORE -->
            <div class="space-y-1">
                <span class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Opérations</span>
                
                <x-sidebar-link :href="route('prospects.index')" :active="request()->routeIs('prospects.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </x-slot>
                    Prospects
                </x-sidebar-link>

                <x-sidebar-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </x-slot>
                    Clients
                </x-sidebar-link>

                <x-sidebar-link :href="route('ventes.index')" :active="request()->routeIs('ventes.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </x-slot>
                    Ventes
                </x-sidebar-link>
            </div>

            <!-- SECTION : PLANIFICATION -->
            <div class="space-y-1">
                <span class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Planification</span>
                
                <x-sidebar-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </x-slot>
                    Tâches
                </x-sidebar-link>

                <x-sidebar-link :href="route('relances.index')" :active="request()->routeIs('relances.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </x-slot>
                    Relances
                </x-sidebar-link>

                <x-sidebar-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </x-slot>
                    Notifications
                </x-sidebar-link>
            </div>

            <!-- SECTION : CATALOGUE & MARKETING -->
            @role('Administrateur|Directeur Général')
            <div class="space-y-1">
                <span class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Catalogue & Marketing</span>
                
                <x-sidebar-link :href="route('produits.index')" :active="request()->routeIs('produits.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </x-slot>
                    Produits
                </x-sidebar-link>

                <x-sidebar-link :href="route('campagnes.index')" :active="request()->routeIs('campagnes.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    </x-slot>
                    Campagnes
                </x-sidebar-link>

                <x-sidebar-link :href="route('sources.index')" :active="request()->routeIs('sources.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                    </x-slot>
                    Sources
                </x-sidebar-link>
            </div>
            @endrole
            <!-- SECTION : CONFIGURATION / ADMIN -->
            <div class="space-y-1">
                <span class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Configuration</span>
                @role('Administrateur|Directeur Général')
                <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </x-slot>
                    Utilisateurs
                </x-sidebar-link>
             

                <x-sidebar-link :href="route('filiales.index')" :active="request()->routeIs('filiales.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </x-slot>
                    Filiales
                </x-sidebar-link>
                @endrole
                @if(Auth::user()->hasRole('Administrateur'))
                <x-sidebar-link :href="route('logs.index')" :active="request()->routeIs('logs.*')">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </x-slot>
                    Journaux d'Audit
                </x-sidebar-link>
                @endif
            </div>
        </nav>

        <!-- Bas de la Sidebar (Profil de l'utilisateur) -->
        <div class="pt-6 border-t border-slate-900">
            <div class="flex items-center justify-between group/user mb-4">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <!-- Initiales ou Avatar de l'utilisateur -->
                        <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center font-bold text-slate-200 border border-slate-700 shadow-inner group-hover/user:border-indigo-500 transition-colors duration-200">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <!-- Indicateur en ligne -->
                        <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-slate-950"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-200 truncate leading-tight">{{ Auth::user()->name }}</p>
                        <!-- Role de l'utilisateur via Spatie ou par défaut -->
                        <p class="text-[10px] text-indigo-400 font-medium truncate mt-0.5 uppercase tracking-wide">
                            {{ Auth::user()->getRoleNames()->first() ?? 'Utilisateur' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions de Profil & Déconnexion -->
            <div class="grid grid-cols-2 gap-2 text-center text-xs">
                <a href="{{ route('profile.edit') }}" class="py-2 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all duration-150 flex items-center justify-center space-x-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Profil</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="w-full py-2 px-3 rounded-lg text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 transition-all duration-150 flex items-center justify-center space-x-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Quitter</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

<!-- Sidebar mobile (Drawer) -->
<div x-show="sidebarOpen" 
     class="fixed inset-0 flex z-40 md:hidden" 
     x-description="Off-canvas menu for mobile, show/hide based on off-canvas menu state." 
     x-ref="dialog" 
     aria-modal="true" 
     style="display: none;">
    
    <!-- Backdrop de floutage -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" 
         @click="sidebarOpen = false" 
         aria-hidden="true"></div>

    <!-- Conteneur Sidebar Drawer -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition ease-in-out duration-300 transform" 
         x-transition:enter-start="-translate-x-full" 
         x-transition:enter-end="translate-x-0" 
         x-transition:leave="transition ease-in-out duration-300 transform" 
         x-transition:leave-start="translate-x-0" 
         x-transition:leave-end="-translate-x-full" 
         class="relative flex-1 flex flex-col max-w-xs w-full bg-slate-950 border-r border-slate-900">
        
        <!-- Bouton Fermer -->
        <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button type="button" 
                    class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white bg-slate-900 border border-slate-800 text-slate-400 hover:text-white" 
                    @click="sidebarOpen = false">
                <span class="sr-only">Fermer la barre latérale</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- En-tête Mobile Logo -->
        <div class="flex items-center h-16 px-6 border-b border-slate-900 bg-slate-950/50">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 text-white shadow-lg shadow-indigo-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-semibold text-white tracking-wider block">CRM COMMERCIAL</span>
                    <span class="text-[10px] text-indigo-400 font-medium block uppercase tracking-widest -mt-0.5">Espace Ventes</span>
                </div>
            </div>
        </div>

        <!-- Liens Mobile -->
        <div class="flex-1 flex flex-col justify-between overflow-y-auto px-4 py-6 space-y-7">
            <nav class="space-y-6">
                <!-- SECTION : ACCUEIL -->
                <div class="space-y-2">
                    <span class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Menu</span>
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </x-slot>
                        Dashboard
                    </x-sidebar-link>
                </div>

                <!-- SECTION : CRM CORE -->
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Opérations</span>
                    
                    <x-sidebar-link :href="route('prospects.index')" :active="request()->routeIs('prospects.*')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </x-slot>
                        Prospects
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('clients.index')" :active="request()->routeIs('clients.*')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </x-slot>
                        Clients
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('ventes.index')" :active="request()->routeIs('ventes.*')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </x-slot>
                        Ventes
                    </x-sidebar-link>
                </div>

                <!-- SECTION : PLANIFICATION -->
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Planification</span>
                    
                    <x-sidebar-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </x-slot>
                        Tâches
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('relances.index')" :active="request()->routeIs('relances.*')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </x-slot>
                        Relances
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </x-slot>
                        Notifications
                    </x-sidebar-link>
                </div>

                <!-- SECTION : CATALOGUE & MARKETING -->
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Catalogue & Marketing</span>
                    
                    <x-sidebar-link :href="route('produits.index')" :active="request()->routeIs('produits.*')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </x-slot>
                        Produits
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('campagnes.index')" :active="request()->routeIs('campagnes.*')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    </x-slot>
                    Campagnes
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('sources.index')" :active="request()->routeIs('sources.*')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </x-slot>
                        Sources
                    </x-sidebar-link>
                </div>

                <!-- SECTION : CONFIGURATION / ADMIN -->
                <div class="space-y-1">
                    <span class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Configuration</span>
                    
                    <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </x-slot>
                        Utilisateurs
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('filiales.index')" :active="request()->routeIs('filiales.*')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </x-slot>
                        Filiales
                    </x-sidebar-link>

                    @if(Auth::user()->hasRole('Administrateur'))
                    <x-sidebar-link :href="route('logs.index')" :active="request()->routeIs('logs.*')" @click="sidebarOpen = false">
                        <x-slot name="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </x-slot>
                        Journaux d'Audit
                    </x-sidebar-link>
                    @endif
                </div>
            </nav>

            <!-- Bas Mobile -->
            <div class="pt-6 border-t border-slate-900">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center font-bold text-slate-200 border border-slate-700">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-slate-950"></span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-200 leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-indigo-400 font-medium uppercase tracking-wide">
                            {{ Auth::user()->getRoleNames()->first() ?? 'Utilisateur' }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-center text-xs">
                    <a href="{{ route('profile.edit') }}" @click="sidebarOpen = false" class="py-2 px-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900 border border-slate-800 flex items-center justify-center space-x-1.5">
                        <span>Profil</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="w-full py-2 px-3 rounded-lg text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 flex items-center justify-center space-x-1.5">
                            <span>Quitter</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
