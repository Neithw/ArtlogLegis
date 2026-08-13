@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div>
    <label for="{{ $id }}" class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
        {{ $label }}

        @if ($attributes->has('required'))
            <span class="text-red-500" aria-hidden="true">*</span>
        @endif
    </label>

    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}"
        @if ($hasError) aria-invalid="true"
            aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except('id')->class([
                'mt-1 block w-full rounded-lg bg-white text-sm text-slate-950 shadow-sm transition',
                'placeholder:text-slate-400',
                'focus:border-indigo-500 focus:ring-indigo-500',
                'disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500',
                'dark:bg-neutral-950 dark:text-neutral-100 dark:placeholder:text-neutral-600',
                'dark:disabled:bg-neutral-800 dark:disabled:text-neutral-500',
                'dark:[color-scheme:dark]',
                'border-slate-300 dark:border-neutral-700' => !$hasError,
                'border-red-500 focus:border-red-500 focus:ring-red-500' => $hasError,
            ]) }}>

    @if ($hint)
        <p class="mt-1.5 text-xs text-slate-500 dark:text-neutral-500">
            {{ $hint }}
        </p>
    @endif

    @if ($hasError)
        <p id="{{ $id }}-error" class="mt-1.5 text-sm text-red-600 dark:text-red-400">
            {{ $errors->first($name) }}
        </p>
    @endif
</div>
