import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'

Alpine.plugin(collapse)

window.Alpine = Alpine

// Dark mode store
Alpine.store('darkMode', {
    on: document.documentElement.classList.contains('dark'),
    toggle() {
        this.on = !this.on
        localStorage.setItem('darkMode', this.on)
        document.documentElement.classList.toggle('dark', this.on)
        document.documentElement.style.colorScheme = this.on ? 'dark' : 'light'
    }
})

// Toast notification store
Alpine.store('toasts', {
    items: [],
    add(message, type = 'success') {
        const id = Date.now()
        this.items.push({ id, message, type })
        setTimeout(() => this.remove(id), 4000)
    },
    remove(id) {
        this.items = this.items.filter(i => i.id !== id)
    }
})

// CSRF-aware fetch wrapper
window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content

window.ajax = async (url, options = {}) => {
    const res = await fetch(url, {
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...options.headers,
        },
        ...options,
    })
    return res.json()
}

window.ajaxPost = (url, data = {}) => {
    return window.ajax(url, {
        method: 'POST',
        body: JSON.stringify(data),
    })
}

// Simple slugify
window.slugify = (text) => {
    return text.toString().toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '')
}

// Data-driven DataTable component: sort, filter, paginate.
// Rows are passed as a JSON array. Computed getters (filtered → sorted → paged)
// drive an x-for template — no DOM manipulation needed.
Alpine.data('dataTable', (config = {}) => ({
    rows: config.rows || [],
    sortKey: config.sortKey || null,
    sortDir: config.sortDir || 'asc',
    search: '',
    page: 1,
    pageSize: config.pageSize || 25,
    paginate: config.paginate !== false,
    types: config.types || {},
    searchKeys: config.searchKeys || null,
    tableName: config.tableName || null,

    get storageKey() {
        return 'dataTable:' + (this.tableName || window.location.pathname);
    },

    init() {
        this.$watch('search', () => { this.page = 1; });

        const stored = localStorage.getItem(this.storageKey);
        if (stored) {
            try {
                const { sortKey, sortDir } = JSON.parse(stored);
                if (sortKey && this.rows.length > 0 && sortKey in this.rows[0]) {
                    this.sortKey = sortKey;
                    this.sortDir = sortDir || 'asc';
                }
            } catch (e) {}
        }
    },

    sort(key) {
        if (this.sortKey === key) {
            this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortKey = key;
            this.sortDir = 'asc';
        }
        this.page = 1;
        localStorage.setItem(this.storageKey, JSON.stringify({ sortKey: this.sortKey, sortDir: this.sortDir }));
    },

    get filtered() {
        if (!this.search) return this.rows;
        const s = this.search.toLowerCase();
        const keys = this.searchKeys || Object.keys(this.rows[0] || {});
        return this.rows.filter(row =>
            keys.some(k => String(row[k] ?? '').toLowerCase().includes(s))
        );
    },

    get sorted() {
        const key = this.sortKey;
        if (!key) return this.filtered;
        const dir = this.sortDir === 'asc' ? 1 : -1;
        const type = this.types[key] || 'string';
        return [...this.filtered].sort((a, b) => {
            let va = a[key] ?? '', vb = b[key] ?? '';
            if (type === 'number') return ((parseFloat(va) || 0) - (parseFloat(vb) || 0)) * dir;
            if (type === 'date') return ((new Date(va).getTime() || 0) - (new Date(vb).getTime() || 0)) * dir;
            return dir * String(va).localeCompare(String(vb), undefined, { numeric: true, sensitivity: 'base' });
        });
    },

    get paged() {
        if (!this.paginate) return this.sorted;
        const start = (this.page - 1) * this.pageSize;
        return this.sorted.slice(start, start + this.pageSize);
    },

    get totalFiltered() { return this.filtered.length; },
    get totalRows() { return this.rows.length; },
    get totalPages() { return this.paginate ? Math.max(1, Math.ceil(this.totalFiltered / this.pageSize)) : 1; },
    get showingFrom() { return this.totalFiltered === 0 ? 0 : (this.page - 1) * this.pageSize + 1; },
    get showingTo() { return Math.min(this.page * this.pageSize, this.totalFiltered); },
    get pageNumbers() {
        const total = this.totalPages;
        const current = this.page;
        const pages = [];
        if (total <= 7) {
            for (let i = 1; i <= total; i++) pages.push(i);
        } else {
            pages.push(1);
            if (current > 3) pages.push('...');
            for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
                pages.push(i);
            }
            if (current < total - 2) pages.push('...');
            pages.push(total);
        }
        return pages;
    },

    goToPage(n) { this.page = n; },
    prevPage() { if (this.page > 1) this.page--; },
    nextPage() { if (this.page < this.totalPages) this.page++; },
}));

Alpine.start()
