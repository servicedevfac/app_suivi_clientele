<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CRM Commercial') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900 bg-slate-50 min-h-screen flex flex-col lg:flex-row" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        
        <!-- Left Column: Form Section -->
        <div class="w-full lg:w-1/2 min-h-screen flex flex-col justify-between p-6 sm:p-12 md:p-16 bg-white z-10 relative shadow-2xl lg:shadow-none">
            
            <!-- Branding / Logo -->
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 text-white shadow-lg shadow-indigo-500/20">
                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-bold text-slate-900 tracking-wider block uppercase">CRM Commercial</span>
                    <span class="text-[9px] text-indigo-500 font-semibold block uppercase tracking-widest -mt-0.5">Espace Ventes</span>
                </div>
            </div>

            <!-- Content Slot (Forms) -->
            <div class="max-w-md w-full mx-auto my-auto py-8">
                {{ $slot }}
            </div>

            <!-- Footer Section -->
            <div class="text-center lg:text-left text-xs text-slate-400">
                &copy; {{ date('Y') }} CRM Commercial. Tous droits réservés.
            </div>
        </div>

        <!-- Right Column: Visual Promotional Section (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 bg-slate-950 relative overflow-hidden flex-col justify-between p-16">
            <!-- Decorative luminous orbs -->
            <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-indigo-500/10 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-violet-500/10 blur-3xl"></div>
            
            <!-- Grid pattern overlay -->
            <div class="absolute inset-0 opacity-10 bg-[linear-gradient(to_right,#808080_1px,transparent_1px),linear-gradient(to_bottom,#808080_1px,transparent_1px)] bg-[size:24px_24px]"></div>

            <div class="relative z-10 my-auto max-w-lg mx-auto">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 mb-6">
                    CRM Commercial v2.0
                </span>
                <h2 class="text-3xl font-extrabold text-white tracking-tight sm:text-4xl leading-tight">
                    Pilotez vos ventes en temps réel
                </h2>
                <p class="mt-4 text-base text-indigo-200/70 leading-relaxed">
                    Visualisez vos indicateurs clés, gérez vos prospects et maximisez votre taux de conversion avec notre interface intuitive de nouvelle génération.
                </p>

                <!-- Mockup Card -->
                <div class="mt-10 bg-slate-900/60 backdrop-blur-md rounded-2xl border border-white/5 p-6 shadow-2xl relative group hover:border-white/10 transition-all duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                        </div>
                        <div class="text-[10px] text-slate-400 font-medium tracking-wide uppercase">Aperçu direct</div>
                    </div>

                    <div class="space-y-4 text-left">
                        <div class="bg-slate-950/40 p-4 rounded-xl border border-white/5">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-slate-400 font-medium">Chiffre d'affaires mensuel</span>
                                <span class="text-xs text-emerald-400 font-semibold flex items-center bg-emerald-500/10 px-2 py-0.5 rounded-md">
                                    <svg class="w-2.5 h-2.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    +12.4%
                                </span>
                            </div>
                            <div class="text-xl font-bold text-white mt-1">45 890 XOF</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-950/40 p-4 rounded-xl border border-white/5">
                                <div class="text-xs text-slate-400 font-medium">Prospects convertis</div>
                                <div class="text-xl font-bold text-white mt-1">87 %</div>
                            </div>
                            <div class="bg-slate-950/40 p-4 rounded-xl border border-white/5">
                                <div class="text-xs text-slate-400 font-medium">Nouveaux clients</div>
                                <div class="text-xl font-bold text-white mt-1">+14</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Small bottom branding text -->
            <div class="relative z-10 flex justify-between text-xs text-slate-500">
                <span></span>
                <span>Version 2.0</span>
            </div>
        </div>

    </body>
</html>
