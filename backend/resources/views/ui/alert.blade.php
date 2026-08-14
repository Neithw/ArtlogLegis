@props([
    'type' => 'success',
])

@php
    $config = match ($type) {
        'error' => [
            'icon' => 'fa-circle-exclamation',
            'classes' => [
                'border-red-200 bg-red-50 text-red-800',
                'dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-300',
            ],
        ],
        'warning' => [
            'icon' => 'fa-triangle-exclamation',
            'classes' => [
                'border-amber-200 bg-amber-50 text-amber-800',
                'dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300',
            ],
        ],
        default => [
            'icon' => 'fa-circle-check',
            'classes' => [
                'border-emerald-200 bg-emerald-50 text-emerald-800',
                'dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300',
            ],
        ],
    };
@endphp

<div role="{{ $type === 'error' ? 'alert' : 'status' }}"
    {{ $attributes->class([
        'flex items-center gap-3 rounded-xl border px-4 py-3 text-sm font-medium',
        ...$config['classes'],
    ]) }}>
    <i class="fa-solid {{ $config['icon'] }} shrink-0" aria-hidden="true"></i>

    <div>
        {{ $slot }}
    </div>
</div>
