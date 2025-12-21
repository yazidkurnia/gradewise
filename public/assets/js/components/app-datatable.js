/**
 * AppDataTable - Reusable DataTable Component
 *
 * Best practices implementation:
 * - Single Responsibility: Each method has one clear purpose
 * - DRY (Don't Repeat Yourself): Reusable across all pages
 * - Configuration over Code: Define columns instead of writing render functions
 * - Error Handling: Comprehensive error states
 * - Extensible: Easy to add custom formatters and actions
 *
 * @class AppDataTable
 */
class AppDataTable {
    /**
     * @param {Object} config - DataTable configuration
     * @param {string} config.tableId - Table element ID
     * @param {string} config.apiUrl - API endpoint URL
     * @param {Array} config.columns - Column definitions
     * @param {Object} config.actions - Optional action handlers
     * @param {Object} config.options - Optional table options
     */
    constructor(config) {
        this.validateConfig(config);

        this.tableId = config.tableId;
        this.apiUrl = config.apiUrl;
        this.columns = config.columns;
        this.actions = config.actions || {};
        this.filters = config.filters || [];
        this.searchConfig = config.search || null;

        this.options = Object.assign({
            showNumbering: true,
            numberingHeader: 'No',
            emptyMessage: 'Tidak ada data yang tersedia',
            errorMessage: 'Terjadi kesalahan saat memuat data',
            loadingMessage: 'Memuat data...',
            enableTooltips: true,
            showNotifications: true,
            enableSearch: true,
            searchPlaceholder: 'Cari data...',
            searchDebounce: 300
        }, config.options || {});

        this.table = null;
        this.tbody = null;
        this.data = [];
        this.filteredData = [];
        this.searchTerm = '';
        this.activeFilters = {};

        this.init();
    }

    /**
     * Validate configuration
     */
    validateConfig(config) {
        if (!config.tableId) {
            throw new Error('AppDataTable: tableId is required');
        }
        if (!config.apiUrl) {
            throw new Error('AppDataTable: apiUrl is required');
        }
        if (!config.columns || !Array.isArray(config.columns)) {
            throw new Error('AppDataTable: columns must be an array');
        }
    }

    /**
     * Initialize the datatable
     */
    init() {
        this.table = document.getElementById(this.tableId);
        if (!this.table) {
            console.error(`AppDataTable: Table with ID "${this.tableId}" not found`);
            return;
        }

        this.tbody = document.getElementById(this.tableId + '_body');
        if (!this.tbody) {
            console.error(`AppDataTable: Tbody with ID "${this.tableId}_body" not found`);
            return;
        }

        this.initSearchAndFilters();
        this.loadData();
    }

    /**
     * Initialize search and filter UI
     */
    initSearchAndFilters() {
        const container = document.getElementById(this.tableId + '_controls');
        if (!container) return;

        let html = '<div class="row mb-3">';

        // Search box
        if (this.options.enableSearch && this.searchConfig) {
            html += `
                <div class="col-md-${this.filters.length > 0 ? '6' : '12'}">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        <input type="text"
                               class="form-control"
                               id="${this.tableId}_search"
                               placeholder="${this.options.searchPlaceholder}">
                    </div>
                </div>
            `;
        }

        // Filter dropdowns
        if (this.filters.length > 0) {
            html += `<div class="col-md-${this.options.enableSearch && this.searchConfig ? '6' : '12'}">
                        <div class="row">`;

            this.filters.forEach((filter, index) => {
                const colSize = Math.floor(12 / this.filters.length);
                html += `
                    <div class="col-md-${colSize}">
                        <select class="form-control" id="${this.tableId}_filter_${filter.field}">
                            <option value="">${filter.placeholder || 'Semua ' + filter.label}</option>
                            ${filter.options.map(opt =>
                                `<option value="${opt.value}">${opt.label}</option>`
                            ).join('')}
                        </select>
                    </div>
                `;
            });

            html += `</div></div>`;
        }

        html += '</div>';
        container.innerHTML = html;

        // Bind events
        this.bindSearchAndFilterEvents();
    }

    /**
     * Bind search and filter events
     */
    bindSearchAndFilterEvents() {
        // Search event with debounce
        const searchInput = document.getElementById(this.tableId + '_search');
        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    this.searchTerm = e.target.value.toLowerCase();
                    this.applySearchAndFilters();
                }, this.options.searchDebounce);
            });
        }

        // Filter events
        this.filters.forEach(filter => {
            const filterSelect = document.getElementById(this.tableId + '_filter_' + filter.field);
            if (filterSelect) {
                filterSelect.addEventListener('change', (e) => {
                    if (e.target.value === '') {
                        delete this.activeFilters[filter.field];
                    } else {
                        this.activeFilters[filter.field] = e.target.value;
                    }
                    this.applySearchAndFilters();
                });
            }
        });
    }

    /**
     * Apply search and filters to data
     */
    applySearchAndFilters() {
        let data = [...this.data];

        // Apply search
        if (this.searchTerm && this.searchConfig) {
            data = data.filter(item => {
                return this.searchConfig.fields.some(field => {
                    const value = this.getNestedProperty(item, field);
                    return value && String(value).toLowerCase().includes(this.searchTerm);
                });
            });
        }

        // Apply filters
        Object.keys(this.activeFilters).forEach(field => {
            const filterValue = this.activeFilters[field];
            data = data.filter(item => {
                const itemValue = String(this.getNestedProperty(item, field));
                return itemValue === filterValue;
            });
        });

        this.filteredData = data;
        this.renderFiltered();
    }

    /**
     * Render filtered data
     */
    renderFiltered() {
        if (this.filteredData.length === 0 && (this.searchTerm || Object.keys(this.activeFilters).length > 0)) {
            this.showEmpty('Tidak ada data yang sesuai dengan pencarian atau filter');
            return;
        }

        let html = '';

        this.filteredData.forEach((item, index) => {
            html += '<tr>';

            if (this.options.showNumbering) {
                html += `<td class="text-center">${index + 1}</td>`;
            }

            this.columns.forEach(column => {
                html += this.renderCell(item, column);
            });

            html += '</tr>';
        });

        this.tbody.innerHTML = html;

        if (this.options.enableTooltips && typeof $('[data-toggle="tooltip"]').tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip();
        }
    }

    /**
     * Load data from API
     */
    loadData() {
        $.ajax({
            url: this.apiUrl,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: () => {
                this.showLoading();
            },
            success: (response) => {
                this.handleSuccess(response);
            },
            error: (xhr, status, error) => {
                this.handleError(xhr, status, error);
            }
        });
    }

    /**
     * Handle successful API response
     */
    handleSuccess(response) {
        if (response.status === 'success' || response.status === 'ok') {
            if (response.data && response.data.length > 0) {
                this.data = response.data;
                this.filteredData = [...response.data];
                this.render();

                if (this.options.showNotifications) {
                    this.showNotification('success', 'Data berhasil dimuat');
                }
            } else {
                this.showEmpty();
            }
        } else if (response.status === 'failed' || response.status === 'error') {
            this.showEmpty(response.message || this.options.emptyMessage);

            if (this.options.showNotifications) {
                this.showNotification('warning', response.message || 'Gagal memuat data');
            }
        } else {
            this.showEmpty('Format response tidak valid');
        }
    }

    /**
     * Handle API error
     */
    handleError(xhr, status, error) {
        console.error('AppDataTable Error:', {
            status: xhr.status,
            statusText: xhr.statusText,
            error: error,
            response: xhr.responseText
        });

        let errorMessage = this.options.errorMessage;

        if (xhr.status === 0) {
            errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
        } else if (xhr.status === 404) {
            errorMessage = 'URL endpoint tidak ditemukan (404)';
        } else if (xhr.status === 500) {
            errorMessage = 'Kesalahan server internal (500)';
        } else if (xhr.status === 401) {
            errorMessage = 'Sesi Anda telah berakhir. Silakan login kembali.';
        } else if (xhr.status === 403) {
            errorMessage = 'Anda tidak memiliki akses untuk melihat data ini.';
        } else if (status === 'timeout') {
            errorMessage = 'Request timeout. Server terlalu lama merespon.';
        } else if (status === 'parsererror') {
            errorMessage = 'Kesalahan parsing data dari server.';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        }

        this.showError(errorMessage);

        if (this.options.showNotifications) {
            this.showNotification('error', errorMessage);
        }
    }

    /**
     * Render table data
     */
    render() {
        let html = '';

        this.data.forEach((item, index) => {
            html += '<tr>';

            // Add numbering column if enabled
            if (this.options.showNumbering) {
                html += `<td class="text-center">${index + 1}</td>`;
            }

            // Render each column
            this.columns.forEach(column => {
                html += this.renderCell(item, column);
            });

            html += '</tr>';
        });

        this.tbody.innerHTML = html;

        // Initialize tooltips if enabled
        if (this.options.enableTooltips && typeof $('[data-toggle="tooltip"]').tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip();
        }
    }

    /**
     * Render a single cell
     */
    renderCell(item, column) {
        let value = this.getCellValue(item, column);
        let cellClass = column.class || '';
        let cellStyle = column.style || '';

        return `<td class="${cellClass}" style="${cellStyle}">${value}</td>`;
    }

    /**
     * Get cell value with formatter support
     */
    getCellValue(item, column) {
        let value = this.getNestedProperty(item, column.field);

        // Apply formatter if exists
        if (column.formatter && typeof column.formatter === 'function') {
            return column.formatter(value, item, this);
        }

        // Apply built-in formatters
        if (column.type) {
            return this.applyBuiltInFormatter(value, column.type, item);
        }

        // Return raw value or default
        return value !== null && value !== undefined ? value : '-';
    }

    /**
     * Get nested property from object (supports dot notation)
     */
    getNestedProperty(obj, path) {
        if (!path) return null;

        return path.split('.').reduce((current, prop) => {
            return current && current[prop] !== undefined ? current[prop] : null;
        }, obj);
    }

    /**
     * Apply built-in formatters
     */
    applyBuiltInFormatter(value, type, item) {
        const formatters = {
            'badge': (val) => {
                const badges = {
                    'aktif': '<span class="badge badge-success">Aktif</span>',
                    'tidak aktif': '<span class="badge badge-danger">Tidak Aktif</span>',
                    '1': '<span class="badge badge-success">Aktif</span>',
                    '0': '<span class="badge badge-danger">Tidak Aktif</span>',
                    'true': '<span class="badge badge-success">Aktif</span>',
                    'false': '<span class="badge badge-danger">Tidak Aktif</span>'
                };
                return badges[String(val).toLowerCase()] || `<span class="badge badge-secondary">${val}</span>`;
            },
            'date': (val) => {
                if (!val) return '-';
                return new Date(val).toLocaleDateString('id-ID');
            },
            'datetime': (val) => {
                if (!val) return '-';
                return new Date(val).toLocaleString('id-ID');
            },
            'currency': (val) => {
                if (!val) return 'Rp 0';
                return 'Rp ' + Number(val).toLocaleString('id-ID');
            },
            'number': (val) => {
                if (!val) return '0';
                return Number(val).toLocaleString('id-ID');
            },
            'actions': (val) => val || '',
            'raw': (val) => val || '-'
        };

        return formatters[type] ? formatters[type](value) : value;
    }

    /**
     * Show loading state
     */
    showLoading() {
        const message = this.options.loadingMessage;
        this.tbody.innerHTML = `
            <tr>
                <td colspan="100%" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0">${message}</p>
                </td>
            </tr>
        `;
    }

    /**
     * Show empty state
     */
    showEmpty(message = null) {
        const msg = message || this.options.emptyMessage;
        this.tbody.innerHTML = `
            <tr>
                <td colspan="100%" class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">${msg}</h5>
                        <p class="text-muted">Data belum tersedia atau belum ada yang ditambahkan.</p>
                    </div>
                </td>
            </tr>
        `;
    }

    /**
     * Show error state
     */
    showError(message) {
        this.tbody.innerHTML = `
            <tr>
                <td colspan="100%" class="text-center py-5">
                    <div class="error-state">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                        <h5 class="text-danger">Terjadi Kesalahan</h5>
                        <p class="text-muted">${message}</p>
                        <button type="button" class="btn btn-primary mt-3" onclick="window.appDataTable_${this.tableId}.reload()">
                            <i class="fas fa-sync"></i> Muat Ulang
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    /**
     * Show notification using iziToast
     */
    showNotification(type, message) {
        if (typeof iziToast === 'undefined') return;

        const notifications = {
            'success': () => iziToast.success({
                title: 'Sukses',
                message: message,
                position: 'topRight',
                timeout: 2000
            }),
            'error': () => iziToast.error({
                title: 'Error',
                message: message,
                position: 'topRight',
                timeout: 5000
            }),
            'warning': () => iziToast.warning({
                title: 'Perhatian',
                message: message,
                position: 'topRight',
                timeout: 3000
            }),
            'info': () => iziToast.info({
                title: 'Info',
                message: message,
                position: 'topRight',
                timeout: 3000
            })
        };

        if (notifications[type]) {
            notifications[type]();
        }
    }

    /**
     * Reload table data
     */
    reload() {
        this.loadData();
    }

    /**
     * Get current data
     */
    getData() {
        return this.data;
    }

    /**
     * Find row by ID
     */
    findById(id, idField = 'id') {
        return this.data.find(item => item[idField] == id);
    }

    /**
     * Update options
     */
    updateOptions(newOptions) {
        this.options = Object.assign(this.options, newOptions);
    }
}

/**
 * Built-in Action Formatters
 */
const ActionFormatters = {
    /**
     * Standard CRUD actions (View, Edit, Delete)
     */
    crud: (item, table, config = {}) => {
        const id = item.id || item[config.idField || 'id'];
        const buttons = [];

        if (config.view !== false) {
            buttons.push(`
                <button class="btn btn-sm btn-info" onclick="${config.viewAction || 'viewData'}(${id})"
                    data-toggle="tooltip" title="Lihat Detail">
                    <i class="fas fa-eye"></i>
                </button>
            `);
        }

        if (config.edit !== false) {
            buttons.push(`
                <button class="btn btn-sm btn-warning" onclick="${config.editAction || 'editData'}(${id})"
                    data-toggle="tooltip" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
            `);
        }

        if (config.delete !== false) {
            buttons.push(`
                <button class="btn btn-sm btn-danger" onclick="${config.deleteAction || 'deleteData'}(${id})"
                    data-toggle="tooltip" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            `);
        }

        return `<div class="btn-group">${buttons.join('')}</div>`;
    },

    /**
     * Custom actions with array of button configs
     */
    custom: (item, table, buttons = []) => {
        const id = item.id || item.id;
        return buttons.map(btn => `
            <button class="btn btn-sm btn-${btn.variant || 'primary'}"
                onclick="${btn.action}(${id}${btn.extraParams ? ', ' + btn.extraParams : ''})"
                data-toggle="tooltip" title="${btn.title || ''}">
                <i class="fas fa-${btn.icon}"></i> ${btn.label || ''}
            </button>
        `).join(' ');
    }
};

// Export for global usage
window.AppDataTable = AppDataTable;
window.ActionFormatters = ActionFormatters;
