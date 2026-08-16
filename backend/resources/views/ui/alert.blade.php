@props([
    'type' => 'success',
    'duration' => null,
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

    $duration ??= match ($type) {
        'error' => 6000,
        'warning' => 5000,
        default => 3000,
    };
@endphp

<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, {{ $duration }})" x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
    role="{{ $type === 'error' ? 'alert' : 'status' }}"
    {{ $attributes->class([
        'fixed top-36 z-50 flex items-center gap-3 rounded-xl border px-4 py-3 text-sm font-medium shadow-xl',
        'left-1/2 -translate-x-1/2 w-11/12 max-w-sm',
        'md:left-auto md:translate-x-0 md:right-4 md:w-auto',
        ...$config['classes'],
    ]) }}>
    <i class="fa-solid {{ $config['icon'] }} shrink-0" aria-hidden="true"></i>

    <div>
        {{ $slot }}
    </div>
</div>
