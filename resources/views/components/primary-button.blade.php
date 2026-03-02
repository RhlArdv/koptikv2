@props(['disabled' => false])

<button
    @disabled($disabled)
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-amber-600 to-amber-700 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-amber-700 hover:to-amber-800 focus:from-amber-700 focus:to-amber-800 active:scale-95 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg shadow-amber-500/30']) }}
>
    {{ $slot }}
</button>
