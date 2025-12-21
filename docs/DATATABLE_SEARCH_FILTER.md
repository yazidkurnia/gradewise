# AppDataTable - Search & Filter Documentation

## Overview
AppDataTable sekarang dilengkapi dengan fitur **Search** dan **Filter** yang dapat dikonfigurasi dengan mudah dari controller. Fitur ini melakukan filtering di **client-side** untuk performa yang cepat.

## Features

### ✅ Search Feature
- Real-time search dengan debounce (300ms default)
- Multi-field search (cari di beberapa kolom sekaligus)
- Case-insensitive
- Configurable dari controller

### ✅ Filter Feature
- Multiple filters support
- Dropdown-based filtering
- Customizable filter options
- Kombinasi dengan search

## Configuration

### 1. Search Configuration (Controller)

Tambahkan konfigurasi `search` di `$tableConfig`:

```php
public function index()
{
    $tableConfig = [
        'tableId' => 'table_dosen',
        'url_data' => route('lecture.all'),
        'columns' => [...],

        // Search Configuration
        'search' => [
            'fields' => ['nidn', 'name', 'expertise']  // Fields yang bisa dicari
        ]
    ];

    return view('pages.manage_lecture.index', compact('tableConfig'));
}
```

**Parameters:**
- `fields` (array, required) - Daftar field yang akan di-search

**Contoh:**
```php
'search' => [
    'fields' => ['name', 'email', 'phone', 'address']  // Cari di 4 field
]
```

### 2. Filter Configuration (Controller)

Tambahkan konfigurasi `filters` di `$tableConfig`:

```php
'filters' => [
    [
        'field' => 'is_active',           // Field yang akan di-filter
        'label' => 'Status',              // Label untuk filter
        'placeholder' => 'Semua Status',  // Placeholder untuk option "All"
        'options' => [                     // Pilihan filter
            ['value' => '1', 'label' => 'Aktif'],
            ['value' => '0', 'label' => 'Tidak Aktif']
        ]
    ]
]
```

**Parameters untuk setiap filter:**
- `field` (string, required) - Field yang akan di-filter
- `label` (string, required) - Label untuk filter
- `placeholder` (string, optional) - Text untuk option "Semua"
- `options` (array, required) - Array of `['value' => '', 'label' => '']`

### 3. Multiple Filters

Anda bisa menambahkan multiple filters:

```php
'filters' => [
    // Filter 1: Status
    [
        'field' => 'is_active',
        'label' => 'Status',
        'placeholder' => 'Semua Status',
        'options' => [
            ['value' => '1', 'label' => 'Aktif'],
            ['value' => '0', 'label' => 'Tidak Aktif']
        ]
    ],
    // Filter 2: Bidang Khusus
    [
        'field' => 'expertise',
        'label' => 'Bidang',
        'placeholder' => 'Semua Bidang',
        'options' => [
            ['value' => 'Data Science', 'label' => 'Data Science'],
            ['value' => 'Web Development', 'label' => 'Web Development'],
            ['value' => 'Mobile Development', 'label' => 'Mobile Development'],
            ['value' => 'AI & Machine Learning', 'label' => 'AI & Machine Learning']
        ]
    ],
    // Filter 3: Jenjang
    [
        'field' => 'level',
        'label' => 'Jenjang',
        'placeholder' => 'Semua Jenjang',
        'options' => [
            ['value' => 'S1', 'label' => 'S1'],
            ['value' => 'S2', 'label' => 'S2'],
            ['value' => 'S3', 'label' => 'S3']
        ]
    ]
]
```

## View Integration

Di view, tambahkan konfigurasi search dan filter ke AppDataTable:

```blade
@push('scripts')
    <script src="{{ asset('assets/js/components/app-datatable.js') }}"></script>
    <script>
        $(function() {
            dataTable = new AppDataTable({
                tableId: '{{ $tableConfig['tableId'] }}',
                apiUrl: '{{ $tableConfig['url_data'] }}',
                columns: @json($tableConfig['columns']),

                // Pass search config
                @if(isset($tableConfig['search']))
                search: @json($tableConfig['search']),
                @endif

                // Pass filters config
                @if(isset($tableConfig['filters']))
                filters: @json($tableConfig['filters']),
                @endif

                options: {
                    enableSearch: {{ isset($tableConfig['search']) ? 'true' : 'false' }},
                    searchPlaceholder: 'Cari data...',
                    searchDebounce: 300  // Debounce time in ms
                }
            });
        });
    </script>
@endpush
```

## Options

### Search Options

```javascript
options: {
    enableSearch: true,                          // Enable/disable search
    searchPlaceholder: 'Cari data...',          // Placeholder text
    searchDebounce: 300                          // Debounce delay in milliseconds
}
```

## How It Works

### Search Flow
1. User mengetik di search box
2. Debounce timer (300ms default) untuk menghindari search terlalu sering
3. Search dilakukan di semua fields yang didefinisikan di config
4. Hasil filtered ditampilkan di table
5. Jika tidak ada hasil, tampilkan empty state

### Filter Flow
1. User memilih option di dropdown filter
2. Data di-filter berdasarkan value yang dipilih
3. Filter bisa dikombinasikan dengan search
4. Multiple filters bisa aktif bersamaan (AND logic)
5. Reset filter dengan memilih "Semua..."

### Combined Search + Filter
```
Original Data (100 rows)
    ↓
Search: "john" → 20 rows
    ↓
Filter: is_active = "1" → 15 rows
    ↓
Display: 15 rows
```

## Examples

### Example 1: Simple Search Only

**Controller:**
```php
$tableConfig = [
    'tableId' => 'table_mahasiswa',
    'url_data' => route('mahasiswa.all'),
    'columns' => [
        ['field' => 'nim', 'label' => 'NIM'],
        ['field' => 'name', 'label' => 'Nama'],
        ['field' => 'major', 'label' => 'Jurusan']
    ],
    'search' => [
        'fields' => ['nim', 'name', 'major']
    ]
];
```

### Example 2: Search + Single Filter

**Controller:**
```php
$tableConfig = [
    'tableId' => 'table_dosen',
    'url_data' => route('lecture.all'),
    'columns' => [...],
    'search' => [
        'fields' => ['nidn', 'name', 'expertise']
    ],
    'filters' => [
        [
            'field' => 'is_active',
            'label' => 'Status',
            'placeholder' => 'Semua Status',
            'options' => [
                ['value' => '1', 'label' => 'Aktif'],
                ['value' => '0', 'label' => 'Tidak Aktif']
            ]
        ]
    ]
];
```

### Example 3: Search + Multiple Filters

**Controller:**
```php
$tableConfig = [
    'tableId' => 'table_products',
    'url_data' => route('products.all'),
    'columns' => [...],
    'search' => [
        'fields' => ['name', 'sku', 'description']
    ],
    'filters' => [
        [
            'field' => 'category',
            'label' => 'Kategori',
            'placeholder' => 'Semua Kategori',
            'options' => [
                ['value' => 'electronics', 'label' => 'Elektronik'],
                ['value' => 'fashion', 'label' => 'Fashion'],
                ['value' => 'food', 'label' => 'Makanan']
            ]
        ],
        [
            'field' => 'status',
            'label' => 'Status',
            'placeholder' => 'Semua Status',
            'options' => [
                ['value' => 'available', 'label' => 'Tersedia'],
                ['value' => 'out_of_stock', 'label' => 'Habis']
            ]
        ]
    ]
];
```

### Example 4: Filter Only (No Search)

**Controller:**
```php
$tableConfig = [
    'tableId' => 'table_reports',
    'url_data' => route('reports.all'),
    'columns' => [...],
    // No search config
    'filters' => [
        [
            'field' => 'year',
            'label' => 'Tahun',
            'placeholder' => 'Semua Tahun',
            'options' => [
                ['value' => '2024', 'label' => '2024'],
                ['value' => '2023', 'label' => '2023'],
                ['value' => '2022', 'label' => '2022']
            ]
        ]
    ]
];
```

## Dynamic Filter Options

Jika filter options perlu diambil dari database:

```php
public function index()
{
    // Get unique expertise values from database
    $expertiseOptions = Lecture::select('expertise')
        ->distinct()
        ->get()
        ->map(function($item) {
            return [
                'value' => $item->expertise,
                'label' => $item->expertise
            ];
        })
        ->toArray();

    $tableConfig = [
        'tableId' => 'table_dosen',
        'url_data' => route('lecture.all'),
        'columns' => [...],
        'search' => [
            'fields' => ['nidn', 'name', 'expertise']
        ],
        'filters' => [
            [
                'field' => 'is_active',
                'label' => 'Status',
                'placeholder' => 'Semua Status',
                'options' => [
                    ['value' => '1', 'label' => 'Aktif'],
                    ['value' => '0', 'label' => 'Tidak Aktif']
                ]
            ],
            [
                'field' => 'expertise',
                'label' => 'Bidang Khusus',
                'placeholder' => 'Semua Bidang',
                'options' => $expertiseOptions  // Dynamic options from DB
            ]
        ]
    ];

    return view('pages.manage_lecture.index', compact('tableConfig'));
}
```

## UI Layout

### Search Only
```
┌─────────────────────────────────────┐
│  🔍 [Search box....................]│
└─────────────────────────────────────┘
┌─────────────────────────────────────┐
│  Table Header                       │
├─────────────────────────────────────┤
│  Table Data                         │
└─────────────────────────────────────┘
```

### Search + Single Filter
```
┌──────────────────────┬──────────────┐
│  🔍 [Search box..]   │ [Filter 1 ▼] │
└──────────────────────┴──────────────┘
┌─────────────────────────────────────┐
│  Table Header                       │
├─────────────────────────────────────┤
│  Table Data                         │
└─────────────────────────────────────┘
```

### Search + Multiple Filters
```
┌──────────────────────┬────────────────────────┐
│  🔍 [Search box..]   │ [Filter 1▼][Filter 2▼] │
└──────────────────────┴────────────────────────┘
┌───────────────────────────────────────────────┐
│  Table Header                                 │
├───────────────────────────────────────────────┤
│  Table Data                                   │
└───────────────────────────────────────────────┘
```

## Best Practices

### 1. Search Fields
✅ **DO:**
- Include commonly searched fields
- Keep it 2-5 fields for best performance
- Use descriptive placeholder

❌ **DON'T:**
- Search in all columns (performance issue)
- Include ID or internal fields
- Search in action columns

### 2. Filter Options
✅ **DO:**
- Use filters for categorical data (status, category, type)
- Limit options to 10-15 per filter
- Use clear, user-friendly labels
- Provide "Semua..." option

❌ **DON'T:**
- Filter on text fields (use search instead)
- Create too many filter options
- Use technical field names as labels

### 3. Performance
- Client-side filtering works best for < 1000 rows
- For larger datasets, consider server-side filtering
- Use debounce for search (default 300ms is good)

### 4. UX
- Combine search for text, filters for categories
- Show count of filtered results
- Clear indication when filters are active
- Reset button if needed

## Advanced: Server-Side Filtering

Untuk dataset yang sangat besar (> 10,000 rows), pertimbangkan server-side filtering:

```php
public function fetchData(Request $request)
{
    $query = Lecture::query();

    // Server-side search
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nidn', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('expertise', 'like', "%{$search}%");
        });
    }

    // Server-side filters
    if ($request->has('is_active')) {
        $query->where('is_active', $request->is_active);
    }

    $data = $query->get();

    return response()->json([
        'status' => 'success',
        'data' => $data
    ]);
}
```

## Troubleshooting

### Search tidak muncul
- Pastikan `search` config ada di controller
- Pastikan `enableSearch: true` di options
- Check console untuk error

### Filter tidak muncul
- Pastikan `filters` array ada dan tidak kosong
- Check format: `[{field, label, placeholder, options}]`
- Verify options format: `[{value, label}]`

### Search tidak jalan
- Check search fields exist in data
- Verify field names match data response
- Look for JavaScript errors in console

### Filter menghilangkan semua data
- Check filter value match data values exactly
- Verify field names
- Check data types (string vs number)

## Summary

✅ **Benefits:**
- Easy to configure from controller
- No repetitive code
- Consistent UI across all pages
- Fast client-side filtering
- Customizable per table

✅ **Usage:**
1. Add `search` config with fields array
2. Add `filters` config with filter definitions
3. Pass to AppDataTable in view
4. Done! Search & filters automatically rendered

**Example - Full Configuration:**
```php
$tableConfig = [
    'tableId' => 'my_table',
    'url_data' => route('data.all'),
    'columns' => [...],
    'search' => ['fields' => ['name', 'email']],
    'filters' => [
        ['field' => 'status', 'label' => 'Status', ...]
    ]
];
```

Simple, clean, reusable! 🎉
