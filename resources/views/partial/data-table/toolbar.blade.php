@props(['placeholder' => 'Search...', 'showPageSize' => true])
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
    @if($showPageSize)
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <span>Show</span>
            <select x-model.number="pageSize" @change="page = 1"
                    class="ui-control w-auto px-2 py-1">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span>entries</span>
        </div>
    @else
        <div></div>
    @endif
    <div>
        <input type="text"
               x-model.debounce.200ms="search"
               placeholder="{{ $placeholder }}"
               class="ui-control sm:w-64">
    </div>
</div>
