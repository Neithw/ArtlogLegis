<div class="overflow-x-auto">
    <table
        {{ $attributes->class([
            'min-w-full text-left text-sm',
            '[&_thead]:border-b [&_thead]:border-slate-200 [&_thead]:bg-slate-50',
            '[&_th]:whitespace-nowrap [&_th]:px-4 [&_th]:py-3',
            '[&_th]:text-xs [&_th]:font-semibold [&_th]:uppercase',
            '[&_th]:tracking-wide [&_th]:text-slate-500',
            '[&_tbody]:divide-y [&_tbody]:divide-slate-100',
            '[&_td]:whitespace-nowrap [&_td]:px-4 [&_td]:py-3',
            '[&_td]:text-slate-700',
            'dark:[&_thead]:border-neutral-800 dark:[&_thead]:bg-neutral-950/40',
            'dark:[&_th]:text-neutral-400',
            'dark:[&_tbody]:divide-neutral-800',
            'dark:[&_td]:text-neutral-300',
        ]) }}>
        {{ $slot }}
    </table>
</div>
