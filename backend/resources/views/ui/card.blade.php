@props([
    'padding' => false,
])

<div
    {{ $attributes->class([
        'overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm',
        'dark:border-neutral-800 dark:bg-neutral-900',
        'p-4 sm:p-6' => $padding,
    ]) }}>
    {{ $slot }}
</div>
