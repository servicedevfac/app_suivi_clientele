<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Notifications</h1>
                <p class="text-sm text-slate-500 mt-1">Consultez vos alertes, rappels de tâches et messages système.</p>
            </div>
        </div>
    </x-slot>

    <!-- Session Notifications -->
    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center space-x-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-xs font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="max-w-4xl mx-auto">
        <!-- Notifications Card Container -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <!-- Header Filter Bar -->
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                    Liste des Notifications
                </span>
                <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">
                    Non lues : {{ \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count() }}
                </span>
            </div>

            <!-- List Body -->
            <div class="divide-y divide-slate-100">
                @forelse ($notifications as $notification)
                    <div class="p-6 transition-all duration-150 flex items-start space-x-4 @if(!$notification->is_read) bg-indigo-50/20 border-l-4 border-indigo-500 @else hover:bg-slate-50/30 @endif">
                        <!-- Icon representation depending on type -->
                        <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center border shadow-sm
                            @if($notification->type === 'danger')
                                bg-rose-50 border-rose-100 text-rose-600
                            @elseif($notification->type === 'warning')
                                bg-amber-50 border-amber-100 text-amber-600
                            @elseif($notification->type === 'success')
                                bg-emerald-50 border-emerald-100 text-emerald-600
                            @else
                                bg-blue-50 border-blue-100 text-blue-600
                            @endif">
                            @if($notification->type === 'danger')
                                🚨
                            @elseif($notification->type === 'warning')
                                ⚠️
                            @elseif($notification->type === 'success')
                                ✅
                            @else
                                ℹ️
                            @endif
                        </div>

                        <!-- Notification details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-4">
                                <h3 class="text-sm font-bold text-slate-800 truncate">
                                    {{ $notification->titre }}
                                </h3>
                                <span class="text-[10px] text-slate-400 font-medium whitespace-nowrap">
                                    {{ $notification->created_at ? $notification->created_at->diffForHumans() : '' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                {{ $notification->message }}
                            </p>
                        </div>

                        <!-- Mark as Read / Read Action -->
                        @if(!$notification->is_read)
                            <div class="flex-shrink-0">
                                <form action="{{ route('notifications.update', $notification) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-semibold text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg border border-indigo-100/50 shadow-sm transition-colors" title="Marquer comme lu">
                                        Marquer comme lu
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-slate-400 text-xs font-medium italic">Lue</span>
                        @endif
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-450">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <p class="text-sm font-medium">Vous n'avez aucune notification.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
