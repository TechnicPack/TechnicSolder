@props(['placeholder' => 'Search...', 'showPageSize' => true])
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
    @if($showPageSize)
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <span>Show</span>
            <select x-model.number="pageSize" @change="page = 1"
                    class="px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
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
               class="w-full sm:w-64 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
    </div>
</div>
