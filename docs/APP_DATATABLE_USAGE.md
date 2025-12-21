# AppDataTable - Dokumentasi Penggunaan

## Deskripsi
AppDataTable adalah komponen reusable untuk menampilkan data dalam bentuk tabel dengan fitur-fitur lengkap seperti loading state, empty state, error handling, dan formatters.

## Best Practices yang Diterapkan
1. **Single Responsibility Principle** - Setiap method memiliki satu tanggung jawab
2. **DRY (Don't Repeat Yourself)** - Tidak perlu menulis ulang fungsi renderTableData
3. **Configuration over Code** - Cukup definisikan kolom, tidak perlu menulis kode rendering
4. **Separation of Concerns** - Logika rendering terpisah dari halaman
5. **Error Handling** - Comprehensive error states dan notifications
6. **Extensibility** - Mudah menambahkan custom formatters

## Cara Penggunaan

### 1. Konfigurasi di Controller

```php
public function index()
{
    $tableConfig = [
        'title' => 'Table data dosen',
        'tableHead' => [
            'No',
            'NIDN',
            'Nama Dosen',
            'Bidang Khusus',
            'Status Aktif',
            'Tindakan'
        ],
        'tableId' => 'table_dosen',
        'url_data' => route('lecture.all'),

        // Konfigurasi kolom - HANYA PERLU INI!
        'columns' => [
            [
                'field' => 'nidn',
                'label' => 'NIDN',
            ],
            [
                'field' => 'name',
                'label' => 'Nama Dosen',
            ],
            [
                'field' => 'expertise',
                'label' => 'Bidang Khusus',
            ],
            [
                'field' => 'is_active',
                'label' => 'Status Aktif',
                'type' => 'badge'  // Built-in formatter
            ],
            [
                'field' => 'action',
                'label' => 'Tindakan',
                'type' => 'actions'
            ]
        ]
    ];

    return view('pages.manage_lecture.index', compact('tableConfig', 'title'));
}
```

### 2. Di View (Blade Template)

```blade
@push('scripts')
    {{-- Include AppDataTable component --}}
    <script src="{{ asset('assets/js/components/app-datatable.js') }}"></script>

    <script>
        let dataTable;

        $(function() {
            // Initialize AppDataTable
            dataTable = new AppDataTable({
                tableId: '{{ $tableConfig['tableId'] }}',
                apiUrl: '{{ $tableConfig['url_data'] }}',
                columns: @json($tableConfig['columns']),
                options: {
                    showNumbering: true,
                    enableTooltips: true,
                    showNotifications: true
                }
            });

            // Store globally for reload
            window.appDataTable_{{ $tableConfig['tableId'] }} = dataTable;
        });

        // Helper function untuk reload
        function get_all_data() {
            if (dataTable) {
                dataTable.reload();
            }
        }
    </script>
@endpush
```

### 3. Format API Response

API endpoint harus mengembalikan response dalam format:

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "nidn": "123456",
            "name": "John Doe",
            "expertise": "Data Science",
            "is_active": "1",
            "action": "<button>...</button>"
        }
    ]
}
```

## Konfigurasi Kolom

### Field Types (Built-in Formatters)

#### 1. **badge** - Status Badge
```php
[
    'field' => 'is_active',
    'type' => 'badge'
]
```
Output:
- `1`, `true`, `aktif` → Badge hijau "Aktif"
- `0`, `false`, `tidak aktif` → Badge merah "Tidak Aktif"

#### 2. **date** - Format Tanggal
```php
[
    'field' => 'created_at',
    'type' => 'date'
]
```
Output: `21/12/2025`

#### 3. **datetime** - Format Tanggal & Waktu
```php
[
    'field' => 'updated_at',
    'type' => 'datetime'
]
```
Output: `21/12/2025, 14:30:00`

#### 4. **currency** - Format Rupiah
```php
[
    'field' => 'salary',
    'type' => 'currency'
]
```
Output: `Rp 5.000.000`

#### 5. **number** - Format Angka
```php
[
    'field' => 'total_students',
    'type' => 'number'
]
```
Output: `1.234`

#### 6. **actions** - HTML Actions (Raw)
```php
[
    'field' => 'action',
    'type' => 'actions'
]
```
Output: Render HTML as-is

### Custom Formatter

Anda juga bisa membuat custom formatter:

```php
'columns' => [
    [
        'field' => 'email',
        'label' => 'Email',
        'formatter' => 'formatEmail'  // Nama function JavaScript
    ]
]
```

Di JavaScript:
```javascript
function formatEmail(value, item, table) {
    return `<a href="mailto:${value}">${value}</a>`;
}

dataTable = new AppDataTable({
    // ... config
    columns: [
        {
            field: 'email',
            formatter: formatEmail  // Pass function reference
        }
    ]
});
```

### Nested Fields (Dot Notation)

```php
[
    'field' => 'user.profile.name',  // Akses nested object
    'label' => 'Nama User'
]
```

### Custom CSS Class & Style

```php
[
    'field' => 'status',
    'label' => 'Status',
    'class' => 'text-center font-weight-bold',
    'style' => 'color: red;'
]
```

## Options

```javascript
new AppDataTable({
    tableId: 'my_table',
    apiUrl: '/api/data',
    columns: [...],
    options: {
        showNumbering: true,           // Tampilkan kolom nomor
        numberingHeader: 'No',          // Label kolom nomor
        emptyMessage: 'Tidak ada data', // Pesan ketika kosong
        errorMessage: 'Error!',         // Pesan ketika error
        loadingMessage: 'Loading...',   // Pesan saat loading
        enableTooltips: true,           // Enable Bootstrap tooltips
        showNotifications: true         // Tampilkan iziToast notifications
    }
});
```

## Methods

### reload()
Reload data dari API
```javascript
dataTable.reload();
```

### getData()
Dapatkan data saat ini
```javascript
let currentData = dataTable.getData();
console.log(currentData);
```

### findById(id, idField)
Cari data berdasarkan ID
```javascript
let item = dataTable.findById(5);
let item2 = dataTable.findById('ABC', 'code');  // Custom ID field
```

### updateOptions(newOptions)
Update options
```javascript
dataTable.updateOptions({
    showNotifications: false
});
```

## Contoh Lengkap

### Controller
```php
public function index()
{
    $tableConfig = [
        'title' => 'Data Mahasiswa',
        'tableHead' => ['No', 'NIM', 'Nama', 'IPK', 'Status', 'Aksi'],
        'tableId' => 'table_mahasiswa',
        'url_data' => route('mahasiswa.all'),
        'columns' => [
            ['field' => 'nim', 'label' => 'NIM'],
            ['field' => 'name', 'label' => 'Nama'],
            ['field' => 'gpa', 'label' => 'IPK', 'type' => 'number'],
            ['field' => 'status', 'label' => 'Status', 'type' => 'badge'],
            ['field' => 'action', 'label' => 'Aksi', 'type' => 'actions']
        ]
    ];

    return view('pages.mahasiswa.index', compact('tableConfig'));
}
```

### View
```blade
@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-body">
            @include('components.app-datatable')
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/components/app-datatable.js') }}"></script>
    <script>
        let dataTable;

        $(function() {
            dataTable = new AppDataTable({
                tableId: '{{ $tableConfig['tableId'] }}',
                apiUrl: '{{ $tableConfig['url_data'] }}',
                columns: @json($tableConfig['columns'])
            });
        });

        function deleteData(id) {
            Swal.fire({
                title: 'Hapus data?',
                text: "Data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/mahasiswa/${id}`,
                        method: 'DELETE',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function(response) {
                            Swal.fire('Berhasil!', 'Data telah dihapus.', 'success');
                            dataTable.reload();
                        }
                    });
                }
            });
        }
    </script>
@endpush
```

## Keuntungan Menggunakan AppDataTable

### Sebelum (Tanpa AppDataTable)
❌ Harus menulis fungsi `renderTableData()` di setiap halaman
❌ Duplikasi kode untuk loading, empty, error states
❌ Sulit maintain ketika ada perubahan format
❌ Kode JavaScript panjang dan repetitif

### Sesudah (Dengan AppDataTable)
✅ Cukup definisikan kolom di controller
✅ Tidak perlu menulis fungsi rendering manual
✅ Konsisten di semua halaman
✅ Kode lebih bersih dan maintainable
✅ Built-in formatters untuk use case umum
✅ Easy to extend dengan custom formatters

## Migration dari Kode Lama

1. **Pindahkan definisi kolom ke controller**
   - Dari: Function `renderTableData()` di view
   - Ke: Array `columns` di controller

2. **Ganti init di JavaScript**
   - Dari: `get_all_data()` dengan ajax manual
   - Ke: `new AppDataTable(config)`

3. **Hapus fungsi-fungsi manual**
   - Tidak perlu lagi: `renderTableData()`, `showLoading()`, `showEmpty()`, `showErrorState()`
   - Semua sudah handled oleh AppDataTable

## Tips & Best Practices

1. **Gunakan type yang sesuai** - Pilih formatter yang tepat (badge, date, currency, dll)
2. **Consistent naming** - Gunakan naming yang konsisten untuk field
3. **API response format** - Pastikan API mengembalikan format yang benar
4. **Global instance** - Simpan instance di `window` untuk akses global
5. **Reload after CRUD** - Selalu reload table setelah create/update/delete
6. **Custom formatters** - Buat reusable formatter untuk kebutuhan spesifik

## Troubleshooting

### Table tidak muncul
- Pastikan `tableId` di config sesuai dengan ID di HTML
- Cek console browser untuk error
- Pastikan script `app-datatable.js` sudah di-include

### Data tidak muncul
- Cek format response API (harus `{status: 'success', data: [...]}`)
- Cek field name di config sesuai dengan response
- Lihat Network tab di browser DevTools

### Badge tidak muncul
- Pastikan menggunakan `type: 'badge'`
- Pastikan value adalah `1`, `0`, `true`, `false`, `aktif`, atau `tidak aktif`

### Custom formatter tidak jalan
- Pastikan function sudah didefinisikan sebelum init AppDataTable
- Gunakan function reference, bukan string nama function
