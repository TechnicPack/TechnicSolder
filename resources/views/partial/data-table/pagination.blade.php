<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mt-4 text-sm text-gray-600 dark:text-gray-400">
    <div>
        <template x-if="totalFiltered > 0">
            <span>
                Showing <span x-text="showingFrom"></span> to <span x-text="showingTo"></span>
                of <span x-text="totalFiltered"></span> entries
                <template x-if="totalFiltered < totalRows">
                    <span>(filtered from <span x-text="totalRows"></span> total)</span>
                </template>
            </span>
        </template>
        <template x-if="totalFiltered === 0">
            <span>No matching entries</span>
        </template>
    </div>
    <div class="flex items-center gap-1" x-show="totalPages > 1">
        <button @click="prevPage()" :disabled="page === 1"
                class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
            Prev
        </button>
        <template x-for="p in pageNumbers" :key="'page-'+p">
            <button x-text="p"
                    @click="typeof p === 'number' && goToPage(p)"
                    :disabled="p === '...'"
                    :class="p === page ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800'"
                    class="px-3 py-1.5 rounded-lg border text-sm transition-colors disabled:cursor-default">
            </button>
        </template>
        <button @click="nextPage()" :disabled="page === totalPages"
                class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
            Next
        </button>
    </div>
</div>
