/**
 * DataTable Helper
 * Reusable DataTable initialization
 * 
 * @author Yazid
 * @version 1.0
 */

var DataTableHelper = (function() {
    
    /**
     * Default configuration
     */
    var defaultConfig = {
        processing: true,
        serverSide: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang ditemukan",
            emptyTable: "Tidak ada data tersedia",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        order: [[0, 'asc']],
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    };
    
    /**
     * Initialize DataTable
     * 
     * @param {string} tableId - Table element ID
     * @param {object} config - Configuration object
     * @returns {object} DataTable instance
     */
    function init(tableId, config) {
        var mergedConfig = $.extend(true, {}, defaultConfig, config);
        
        // Destroy existing datatable if exists
        if ($.fn.DataTable.isDataTable('#' + tableId)) {
            $('#' + tableId).DataTable().destroy();
        }
        
        return $('#' + tableId).DataTable(mergedConfig);
    }
    
    /**
     * Reload DataTable
     * 
     * @param {object} table - DataTable instance
     * @param {boolean} resetPaging - Reset to first page
     */
    function reload(table, resetPaging) {
        resetPaging = resetPaging !== undefined ? resetPaging : false;
        table.ajax.reload(null, resetPaging);
    }
    
    /**
     * Create action buttons column renderer
     * 
     * @param {object} buttons - Button configuration
     * @returns {function} Render function
     */
    function actionButtons(buttons) {
        return function(data, type, row) {
            var html = '<div class="btn-group btn-group-sm" role="group">';
            
            if (buttons.view) {
                html += '<button type="button" class="btn btn-info btn-view" data-id="' + row.DT_RowId + '" title="Lihat">' +
                       '<i class="fa fa-eye"></i></button>';
            }
            
            if (buttons.edit) {
                html += '<button type="button" class="btn btn-warning btn-edit" data-id="' + row.DT_RowId + '" title="Edit">' +
                       '<i class="fa fa-edit"></i></button>';
            }
            
            if (buttons.delete) {
                html += '<button type="button" class="btn btn-danger btn-delete" data-id="' + row.DT_RowId + '" title="Hapus">' +
                       '<i class="fa fa-trash"></i></button>';
            }
            
            // Custom buttons
            if (buttons.custom) {
                buttons.custom.forEach(function(btn) {
                    html += '<button type="button" class="btn btn-' + (btn.class || 'secondary') + ' ' + btn.className + '" ' +
                           'data-id="' + row.DT_RowId + '" title="' + (btn.title || '') + '">' +
                           '<i class="fa fa-' + btn.icon + '"></i></button>';
                });
            }
            
            html += '</div>';
            return html;
        };
    }
    
    /**
     * Format date
     * 
     * @param {string} format - Date format
     * @returns {function} Render function
     */
    function formatDate(format) {
        format = format || 'DD/MM/YYYY';
        
        return function(data, type, row) {
            if (!data) return '-';
            if (type === 'display' || type === 'filter') {
                return moment(data).format(format);
            }
            return data;
        };
    }
    
    /**
     * Format number
     * 
     * @param {number} decimals - Decimal places
     * @param {string} decimalSeparator - Decimal separator
     * @param {string} thousandsSeparator - Thousands separator
     * @returns {function} Render function
     */
    function formatNumber(decimals, decimalSeparator, thousandsSeparator) {
        decimals = decimals !== undefined ? decimals : 0;
        decimalSeparator = decimalSeparator || ',';
        thousandsSeparator = thousandsSeparator || '.';
        
        return function(data, type, row) {
            if (type === 'display' || type === 'filter') {
                var num = parseFloat(data);
                if (isNaN(num)) return data;
                
                return num.toFixed(decimals)
                    .replace('.', decimalSeparator)
                    .replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);
            }
            return data;
        };
    }
    
    /**
     * Format currency (IDR)
     * 
     * @returns {function} Render function
     */
    function formatCurrency() {
        return function(data, type, row) {
            if (type === 'display' || type === 'filter') {
                var num = parseFloat(data);
                if (isNaN(num)) return data;
                
                return 'Rp ' + num.toFixed(0)
                    .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
            return data;
        };
    }
    
    /**
     * Format status badge
     * 
     * @param {object} statusMap - Status mapping {value: {label, class}}
     * @returns {function} Render function
     */
    function formatStatus(statusMap) {
        return function(data, type, row) {
            if (type === 'display') {
                var status = statusMap[data] || {label: data, class: 'secondary'};
                return '<span class="badge badge-' + status.class + '">' + status.label + '</span>';
            }
            return data;
        };
    }
    
    /**
     * Bind action button events
     * 
     * @param {string} tableId - Table element ID
     * @param {object} callbacks - Callback functions
     */
    function bindActions(tableId, callbacks) {
        var $table = $('#' + tableId);
        
        if (callbacks.view) {
            $table.on('click', '.btn-view', function() {
                var id = $(this).data('id');
                callbacks.view(id);
            });
        }
        
        if (callbacks.edit) {
            $table.on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                callbacks.edit(id);
            });
        }
        
        if (callbacks.delete) {
            $table.on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                callbacks.delete(id);
            });
        }
        
        // Custom button callbacks
        if (callbacks.custom) {
            $.each(callbacks.custom, function(className, callback) {
                $table.on('click', '.' + className, function() {
                    var id = $(this).data('id');
                    callback(id);
                });
            });
        }
    }
    
    // Public API
    return {
        init: init,
        reload: reload,
        actionButtons: actionButtons,
        formatDate: formatDate,
        formatNumber: formatNumber,
        formatCurrency: formatCurrency,
        formatStatus: formatStatus,
        bindActions: bindActions
    };
    
})();