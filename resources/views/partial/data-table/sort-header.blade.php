@props(['key', 'label'])
<th class="px-5 py-3 cursor-pointer select-none group" @click="sort('{{ $key }}')">
    <span class="inline-flex items-center gap-1">
        {{ $label }}
        <span class="inline-flex flex-col text-[10px] leading-none">
            <svg class="size-3" :class="sortKey === '{{ $key }}' && sortDir === 'asc' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-300 dark:text-gray-600'" fill="currentColor" viewBox="0 0 20 20"><path d="M5 12l5-6 5 6H5z"/></svg>
            <svg class="size-3 -mt-0.5" :class="sortKey === '{{ $key }}' && sortDir === 'desc' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-300 dark:text-gray-600'" fill="currentColor" viewBox="0 0 20 20"><path d="M5 8l5 6 5-6H5z"/></svg>
        </span>
    </span>
</th>
