@props([
    'href' => null,
    'variant' => 'primary',
    'type' => 'button',
    'navigate' => true,
])

@php
    $variantClasses = match ($variant) {
        'secondary' => [
            'border border-slate-300 bg-white text-slate-700',
            'hover:bg-slate-50 hover:text-slate-950',
            'dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300',
            'dark:hover:bg-neutral-800 dark:hover:text-white',
        ],
        'edit' => ['bg-amber-500 text-white hover:bg-amber-600', 'dark:bg-amber-600 dark:hover:bg-amber-500'],
        'danger' => ['bg-red-600 text-white hover:bg-red-700', 'dark:bg-red-600 dark:hover:bg-red-500'],
        'warning' => ['bg-amber-500 text-white hover:bg-amber-600', 'dark:bg-amber-600 dark:hover:bg-amber-500'],
        default => ['bg-indigo-600 text-white hover:bg-indigo-700', 'dark:bg-indigo-600 dark:hover:bg-indigo-500'],
    };

    $classes = [
        'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2',
        'text-sm font-semibold shadow-sm transition',
        'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
        'disabled:pointer-events-none disabled:opacity-50',
        'dark:focus:ring-offset-neutral-900',
        ...$variantClasses,
    ];
@endphp

@if ($href)
    <a href="{{ $href }}" @if ($navigate) wire:navigate.hover @endif
        {{ $attributes->class($classes) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </button>
@endif
