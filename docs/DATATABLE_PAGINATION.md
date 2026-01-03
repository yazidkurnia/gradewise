# AppDataTable - Pagination Documentation

## Overview
AppDataTable sekarang dilengkapi dengan fitur **Pagination** yang powerful dan mudah dikonfigurasi. Fitur ini bekerja dengan **client-side pagination** untuk performa yang cepat dan terintegrasi sempurna dengan search & filter.

## Features

### ✅ Pagination Controls
- Previous/Next buttons
- Numbered page buttons dengan smart display
- Jump to first/last page
- Responsive pagination layout
- Auto-hide jika data cuma 1 halaman

### ✅ Page Size Selector
- Dropdown untuk mengatur jumlah data per halaman
- Customizable options (5, 10, 25, 50, 100)
- Live update saat diubah
- Tetap ingat page size yang dipilih

### ✅ Pagination Info
- Tampilan "Menampilkan X - Y dari Z data"
- Real-time update
- Smart calculation

### ✅ Integration
- Terintegrasi dengan search
- Terintegrasi dengan filter
- Auto-reset ke page 1 saat search/filter berubah
- Smart numbering (row number berlanjut antar halaman)

## Default Configuration

Pagination sudah enabled by default dengan konfigurasi:
- **Page Size:** 10 data per halaman
- **Page Size Options:** [5, 10, 25, 50, 100]
- **Show Info:** Yes
- **Show Size Selector:** Yes

## Configuration Options

### Basic Usage (Default Settings)

Tidak perlu konfigurasi apapun! Pagination otomatis aktif:

```javascript
dataTable = new AppDataTable({
    tableId: 'my_table',
    apiUrl: '/api/data',
    columns: @json($tableConfig['columns'])
    // Pagination sudah aktif dengan default settings
});
```

### Custom Configuration

Customize pagination sesuai kebutuhan:

```javascript
dataTable = new AppDataTable({
    tableId: 'my_table',
    apiUrl: '/api/data',
    columns: @json($tableConfig['columns']),
    options: {
        // Pagination settings
        enablePagination: true,              // Enable/disable pagination
        pageSize: 25,                        // Default: 10
        pageSizeOptions: [10, 25, 50, 100],  // Default: [5, 10, 25, 50, 100]
        showPageSizeSelector: true,          // Show/hide size dropdown
        showPaginationInfo: true             // Show/hide "Menampilkan X-Y dari Z"
    }
});
```

### Disable Pagination

Jika Anda ingin menampilkan semua data tanpa pagination:

```javascript
options: {
    enablePagination: false
}
```

## Options Detail

### enablePagination
**Type:** Boolean
**Default:** `true`
**Description:** Enable/disable fitur pagination

```javascript
enablePagination: true   // Pagination aktif
enablePagination: false  // Tampilkan semua data
```

### pageSize
**Type:** Number
**Default:** `10`
**Description:** Jumlah data default per halaman

```javascript
pageSize: 10   // 10 data per halaman
pageSize: 25   // 25 data per halaman
pageSize: 50   // 50 data per halaman
```

### pageSizeOptions
**Type:** Array of Numbers
**Default:** `[5, 10, 25, 50, 100]`
**Description:** Pilihan jumlah data yang tersedia di dropdown

```javascript
pageSizeOptions: [5, 10, 25, 50, 100]     // Default
pageSizeOptions: [10, 20, 50]              // Custom options
pageSizeOptions: [25, 50, 100, 250, 500]  // Untuk data besar
```

### showPageSizeSelector
**Type:** Boolean
**Default:** `true`
**Description:** Tampilkan/sembunyikan dropdown page size

```javascript
showPageSizeSelector: true   // Tampilkan dropdown
showPageSizeSelector: false  // Sembunyikan dropdown
```

### showPaginationInfo
**Type:** Boolean
**Default:** `true`
**Description:** Tampilkan/sembunyikan info "Menampilkan X-Y dari Z"

```javascript
showPaginationInfo: true   // Tampilkan info
showPaginationInfo: false  // Sembunyikan info
```

## UI Components

### Full Pagination Layout

```
┌──────────────────────────────────────────────────────────┐
│  🔍 [Search box...]          [Filter Status ▼]           │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│  Table Header                                             │
├──────────────────────────────────────────────────────────┤
│  Table Data (Page 1: rows 1-10)                          │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│  Menampilkan 1-10 dari 156 data                          │
│                   Tampilkan: [10 ▼] data  [< 1 2 3 ... >]│
└──────────────────────────────────────────────────────────┘
```

### Pagination Controls Only

```
┌──────────────────────────────────────────────────────────┐
│  Menampilkan 11-20 dari 156 data                         │
│                   Tampilkan: [10 ▼] data  [< 1 2 3 ... >]│
└──────────────────────────────────────────────────────────┘
```

### Components Breakdown

#### 1. Pagination Info (Left Side)
```
Menampilkan 1-10 dari 156 data
```

#### 2. Page Size Selector (Right Side)
```
Tampilkan: [10 ▼] data
           └─────┘
           Options: 5, 10, 25, 50, 100
```

#### 3. Pagination Buttons (Right Side)
```
[<] [1] [2] [3] [...] [16] [>]
 │   │   │   │    │     │    │
 │   │   │   │    │     │    └─ Next page
 │   │   │   │    │     └────── Last page
 │   │   │   │    └──────────── Ellipsis (more pages)
 │   │   │   └───────────────── Page 3
 │   │   └───────────────────── Page 2 (active)
 │   └───────────────────────── Page 1
 └───────────────────────────── Previous page
```

## Examples

### Example 1: Default Pagination (10 per page)

**Controller:**
```php
$tableConfig = [
    'tableId' => 'table_dosen',
    'url_data' => route('lecture.all'),
    'columns' => [...],
    'search' => ['fields' => ['nidn', 'name']],
    'filters' => [...]
];
```

**View:**
```javascript
dataTable = new AppDataTable({
    tableId: '{{ $tableConfig['tableId'] }}',
    apiUrl: '{{ $tableConfig['url_data'] }}',
    columns: @json($tableConfig['columns'])
    // Pagination otomatis: 10 data per page
});
```

### Example 2: Custom Page Size (25 per page)

```javascript
dataTable = new AppDataTable({
    tableId: '{{ $tableConfig['tableId'] }}',
    apiUrl: '{{ $tableConfig['url_data'] }}',
    columns: @json($tableConfig['columns']),
    options: {
        pageSize: 25  // 25 data per halaman
    }
});
```

### Example 3: Large Dataset Options

```javascript
dataTable = new AppDataTable({
    tableId: '{{ $tableConfig['tableId'] }}',
    apiUrl: '{{ $tableConfig['url_data'] }}',
    columns: @json($tableConfig['columns']),
    options: {
        pageSize: 50,
        pageSizeOptions: [25, 50, 100, 250, 500]
    }
});
```

### Example 4: Minimal Pagination (No Info, No Selector)

```javascript
dataTable = new AppDataTable({
    tableId: '{{ $tableConfig['tableId'] }}',
    apiUrl: '{{ $tableConfig['url_data'] }}',
    columns: @json($tableConfig['columns']),
    options: {
        showPaginationInfo: false,
        showPageSizeSelector: false
        // Only show page buttons
    }
});
```

### Example 5: Disable Pagination

```javascript
dataTable = new AppDataTable({
    tableId: '{{ $tableConfig['tableId'] }}',
    apiUrl: '{{ $tableConfig['url_data'] }}',
    columns: @json($tableConfig['columns']),
    options: {
        enablePagination: false  // Show all data
    }
});
```

## How It Works

### Pagination Flow

```
1. User opens page
   ↓
2. Load all data from API (e.g., 156 rows)
   ↓
3. Calculate total pages (156 / 10 = 16 pages)
   ↓
4. Display page 1 (rows 1-10)
   ↓
5. Render pagination controls

User clicks page 2
   ↓
6. Slice data for page 2 (rows 11-20)
   ↓
7. Re-render table with page 2 data
   ↓
8. Update pagination controls (highlight page 2)
```

### With Search & Filter

```
Original Data: 156 rows
   ↓
Search "john": 23 rows
   ↓
Filter "Aktif": 15 rows
   ↓
Calculate pages: 15 / 10 = 2 pages
   ↓
Display page 1: rows 1-10
   ↓
Info: "Menampilkan 1-10 dari 15 data"
```

### Row Numbering Logic

```
Page 1: Rows 1-10  → Numbers: 1, 2, 3, ..., 10
Page 2: Rows 11-20 → Numbers: 11, 12, 13, ..., 20
Page 3: Rows 21-30 → Numbers: 21, 22, 23, ..., 30
```

Numbering continues across pages!

## Integration with Search & Filter

Pagination otomatis terintegrasi dengan search & filter:

### Behavior:

1. **Search Input Changed:**
   - Filter data based on search
   - Reset to page 1
   - Recalculate total pages
   - Update pagination controls

2. **Filter Changed:**
   - Filter data based on selection
   - Reset to page 1
   - Recalculate total pages
   - Update pagination controls

3. **Page Size Changed:**
   - Reset to page 1
   - Recalculate total pages
   - Re-render with new page size

### Example Flow:

```
Initial State:
- Total: 156 data
- Page: 1
- Showing: 1-10 of 156

User searches "Data Science":
- Filtered: 23 data
- Page: AUTO RESET to 1
- Showing: 1-10 of 23

User filters "Aktif":
- Filtered: 15 data (Data Science + Aktif)
- Page: AUTO RESET to 1
- Showing: 1-10 of 15

User changes page size to 25:
- Filtered: 15 data (same)
- Page: AUTO RESET to 1
- Showing: 1-15 of 15 (all in one page)
```

## Public Methods

### goToPage(pageNumber)
Navigate to specific page number

```javascript
dataTable.goToPage(3);  // Go to page 3
```

### reload()
Reload data and reset to page 1

```javascript
dataTable.reload();  // Reload from API
```

## Smart Pagination Display

Pagination buttons menggunakan smart display untuk dataset besar:

### Small Dataset (≤ 5 pages)
```
[<] [1] [2] [3] [4] [5] [>]
```

### Medium Dataset (6-10 pages)
```
[<] [1] [2] [3] ... [10] [>]
```

### Large Dataset (> 10 pages)
```
When on page 1:
[<] [1] [2] [3] [4] [5] ... [50] [>]

When on page 25:
[<] [1] ... [23] [24] [25] [26] [27] ... [50] [>]

When on page 50:
[<] [1] ... [46] [47] [48] [49] [50] [>]
```

Always shows:
- Current page
- 2 pages before current
- 2 pages after current
- First page
- Last page
- Ellipsis (...) when needed

## Performance

### Client-Side Pagination Performance

| Total Rows | Page Size | Performance |
|------------|-----------|-------------|
| < 100      | 10        | Excellent ⚡ |
| 100-500    | 10        | Excellent ⚡ |
| 500-1,000  | 25        | Good ✅ |
| 1,000-5,000| 50        | Good ✅ |
| 5,000-10,000| 100      | Fair ⚠️ |
| > 10,000   | -         | Use Server-Side 🔄 |

**Recommendation:**
- < 5,000 rows → Client-side pagination (current implementation)
- > 5,000 rows → Consider server-side pagination

### Why Client-Side?

**Advantages:**
✅ Instant page switching (no API call)
✅ Works with search & filter seamlessly
✅ Less server load
✅ Better user experience (no loading delay)
✅ Simpler implementation

**Disadvantages:**
❌ Initial load includes all data
❌ Not suitable for very large datasets (> 10k rows)

## Best Practices

### 1. Choose Right Page Size

```javascript
// Small datasets (< 100 rows)
pageSize: 10

// Medium datasets (100-500 rows)
pageSize: 25

// Large datasets (500-5000 rows)
pageSize: 50 or 100
```

### 2. Appropriate Options

```javascript
// General purpose
pageSizeOptions: [5, 10, 25, 50, 100]

// Small data
pageSizeOptions: [5, 10, 25]

// Large data
pageSizeOptions: [25, 50, 100, 250]
```

### 3. Disable for Small Datasets

```javascript
// If data < 20 rows, disable pagination
options: {
    enablePagination: false
}
```

### 4. Consistent with UX

```javascript
// Show info for user awareness
showPaginationInfo: true

// Allow size selection for flexibility
showPageSizeSelector: true
```

## Troubleshooting

### Pagination tidak muncul
- ✅ Check: Data > pageSize?
- ✅ Check: `enablePagination: true`?
- ✅ Check: Container `{tableId}_pagination` exists?

### Page buttons tidak clickable
- ✅ Check: Global instance registered? (`window.appDataTable_xxx`)
- ✅ Check: JavaScript errors in console?

### Wrong page after search
- ✅ Expected: Auto reset to page 1
- ✅ If not: Check `applySearchAndFilters()` method

### Row numbers wrong
- ✅ Should continue: 1-10, 11-20, 21-30
- ✅ Check: `startIndex + index + 1` in render

## Summary

### Complete Example

```php
// Controller
$tableConfig = [
    'tableId' => 'table_products',
    'url_data' => route('products.all'),
    'columns' => [...],
    'search' => ['fields' => ['name', 'sku']],
    'filters' => [...]
];
```

```javascript
// View
dataTable = new AppDataTable({
    tableId: '{{ $tableConfig['tableId'] }}',
    apiUrl: '{{ $tableConfig['url_data'] }}',
    columns: @json($tableConfig['columns']),
    search: @json($tableConfig['search']),
    filters: @json($tableConfig['filters']),
    options: {
        // Pagination
        enablePagination: true,
        pageSize: 25,
        pageSizeOptions: [10, 25, 50, 100],
        showPageSizeSelector: true,
        showPaginationInfo: true,

        // Other options
        showNumbering: true,
        enableTooltips: true,
        showNotifications: true
    }
});
```

**Result:**
- ✅ Search box untuk mencari products
- ✅ Filter dropdowns
- ✅ Table dengan 25 data per halaman
- ✅ Dropdown untuk ubah page size
- ✅ Pagination controls (Previous, 1, 2, 3, ..., Next)
- ✅ Info "Menampilkan 1-25 dari 156 data"
- ✅ Semua terintegrasi sempurna!

---

**AppDataTable Pagination - Simple, Powerful, Ready to Use!** 🎉
