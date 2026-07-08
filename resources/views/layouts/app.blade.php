<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Hide Scrollbars for Sidebar only -->
        <style nonce="{{ Vite::cspNonce() }}">
            /* Masquer la barre de défilement uniquement pour la sidebar */
            .custom-scrollbar::-webkit-scrollbar {
                width: 0px;
                background: transparent;
            }
            .custom-scrollbar {
                scrollbar-width: none; 
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden bg-slate-50">
            <!-- Sidebar Navigation (Desktop and Mobile) -->
            @include('layouts.sidebar')

            <!-- Main Content Container -->
            <div class="flex-1 flex flex-col min-w-0 md:pl-64 overflow-y-auto bg-slate-50">
                <!-- Sticky Top Header -->
                <header class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm">
                    <!-- Mobile Hamburger Button -->
                    <button type="button" 
                            class="px-4 border-r border-slate-100 text-slate-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden" 
                            @click="sidebarOpen = true">
                        <span class="sr-only">Ouvrir le menu</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Top Bar Info & Actions -->
                    <div class="flex-1 px-6 flex justify-between items-center">
                        <div class="flex-1 flex">
                            <!-- Optionnel : Barre de recherche ou indicateur -->
                            <div class="w-full max-w-xs md:max-w-md hidden sm:block">
                                <div class="relative text-slate-400 focus-within:text-slate-600">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input id="search-field" class="block w-full h-9 pl-9 pr-3 py-2 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Rechercher un prospect, client ou vente..." type="search" name="search">
                                </div>
                            </div>
                        </div>

                        <!-- Profil rapide & Role badge -->
                        <div class="ml-4 flex items-center md:ml-6 space-x-4">
                            <!-- Rôle badge -->
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100 shadow-sm shadow-indigo-100/10">
                                {{ Auth::user()->getRoleNames()->first() ?? 'Collaborateur' }}
                            </span>

                            <!-- Notifications Bell -->
                            @php
                                $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                            @endphp
                            <a href="{{ route('notifications.index') }}" 
                               class="relative p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-slate-550/10 rounded-lg transition-colors duration-150"
                               title="Notifications">
                                <span class="sr-only">Voir les notifications</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                @if($unreadCount > 0)
                                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[9px] font-bold leading-none text-white bg-rose-500 rounded-full transform translate-x-1/3 -translate-y-1/3 shadow-sm">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </a>

                            <!-- Division fine -->
                            <div class="h-6 w-px bg-slate-200"></div>

                            <!-- Nom de l'utilisateur avec lien vers profil -->
                            <a href="{{ route('profile.edit') }}" class="group flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-semibold text-xs shadow-md shadow-indigo-600/10 group-hover:scale-105 transition-transform duration-200">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <span class="hidden md:inline-block text-xs font-semibold text-slate-700 group-hover:text-indigo-600 transition-colors duration-150">{{ Auth::user()->name }}</span>
                            </a>
                        </div>
                    </div>
                </header>

                <!-- Page Workspace Area -->
                <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8">
                    <!-- Page Header Slot -->
                    @isset($header)
                        <div class="max-w-[1600px] mx-auto mb-8">
                            {{ $header }}
                        </div>
                    @endisset

                    <!-- Main View Content Slot -->
                    <div class="max-w-[1600px] mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
