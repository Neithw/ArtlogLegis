@props(['icon'])

<div {{ $attributes->class(['flex min-h-28 flex-col items-center justify-center', 'gap-1.5 py-5 text-center']) }}>
    <i class="fa-solid {{ $icon }} text-xl leading-none text-slate-300
               dark:text-neutral-700"
        aria-hidden="true"></i>

    <p class="text-sm leading-5 text-slate-500 dark:text-neutral-400">
        {{ $slot }}
    </p>
</div>
