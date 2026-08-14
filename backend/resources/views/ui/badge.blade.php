@props([
    'variant' => 'neutral',
])

@php
    $classes = match ($variant) {
        'info' => ['bg-blue-100 text-blue-700', 'dark:bg-blue-500/10 dark:text-blue-300'],
        'success' => ['bg-emerald-100 text-emerald-700', 'dark:bg-emerald-500/10 dark:text-emerald-300'],
        'warning' => ['bg-amber-100 text-amber-700', 'dark:bg-amber-500/10 dark:text-amber-300'],
        'danger' => ['bg-red-100 text-red-700', 'dark:bg-red-500/10 dark:text-red-300'],
        default => ['bg-slate-100 text-slate-700', 'dark:bg-neutral-800 dark:text-neutral-300'],
    };
@endphp

<span
    {{ $attributes->class([
        'inline-flex items-center rounded-full px-2.5 py-2',
        'text-xs font-semibold leading-none',
        ...$classes,
    ]) }}>
    {{ $slot }}
</span>
