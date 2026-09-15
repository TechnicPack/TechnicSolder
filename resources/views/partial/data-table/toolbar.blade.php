@props(['placeholder' => 'Search...', 'showPageSize' => true, 'embedded' => false])
<div @class(['flex flex-col sm:flex-row items-start sm:items-center gap-3', 'justify-between' => $showPageSize, 'sm:justify-end' => !$showPageSize, 'mb-4' => !$embedded])>
    @if($showPageSize)
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <span>Show</span>
            <select x-model.number="pageSize" @change="page = 1"
                    class="ui-control w-auto pl-2 py-1">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span>entries</span>
        </div>
    @endif
    <div>
        <input type="text"
               x-model.debounce.200ms="search"
               placeholder="{{ $placeholder }}"
               class="ui-control sm:w-64">
    </div>
</div>
