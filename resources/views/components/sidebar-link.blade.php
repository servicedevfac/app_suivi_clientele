@props(['active'])

@php
$classes = ($active ?? false)
            ? 'group flex items-center px-3 py-2 text-sm font-semibold rounded-xl text-indigo-400 bg-gradient-to-r from-indigo-950/40 to-indigo-950/10 border-l-2 border-indigo-500 transition-all duration-150 ease-in-out shadow-sm shadow-indigo-950/20'
            : 'group flex items-center px-3 py-2 text-sm font-medium rounded-xl text-slate-400 hover:text-slate-200 hover:bg-slate-900 border-l-2 border-transparent transition-all duration-150 ease-in-out';

$iconClasses = ($active ?? false)
            ? 'w-5 h-5 mr-3 text-indigo-400 scale-105 transition-transform duration-150'
            : 'w-5 h-5 mr-3 text-slate-500 group-hover:text-slate-300 group-hover:scale-105 transition-all duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($icon))
        <div class="{{ $iconClasses }}">
            {{ $icon }}
        </div>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
