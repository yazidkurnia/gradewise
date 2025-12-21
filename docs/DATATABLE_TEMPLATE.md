# Template untuk Halaman dengan AppDataTable

## 1. Controller Template

```php
<?php

namespace App\Http\Controllers\YourModule;

use App\Models\YourModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class YourController extends Controller
{
    public function index()
    {
        $title = 'Judul Halaman';

        $tableConfig = [
            'title' => 'Judul Tabel',
            'tableHead' => [
                'No',
                'Kolom 1',
                'Kolom 2',
                'Kolom 3',
                'Status',
                'Aksi'
            ],
            'tableId' => 'table_your_table',  // Unique table ID
            'url_data' => route('your.route.all'),  // API endpoint
            'columns' => [
                [
                    'field' => 'field1',  // Sesuaikan dengan response API
                    'label' => 'Kolom 1',
                ],
                [
                    'field' => 'field2',
                    'label' => 'Kolom 2',
                ],
                [
                    'field' => 'field3',
                    'label' => 'Kolom 3',
                ],
                [
                    'field' => 'status',
                    'label' => 'Status',
                    'type' => 'badge'  // Auto format as badge
                ],
                [
                    'field' => 'action',
                    'label' => 'Aksi',
                    'type' => 'actions'
                ]
            ]
        ];

        return view('pages.your_module.index', compact('tableConfig', 'title'));
    }

    // API endpoint untuk fetch data
    public function fetchData()
    {
        $data = YourModel::all();

        // Format data untuk table
        $formattedData = $data->map(function($item) {
            return [
                'id' => $item->id,
                'field1' => $item->field1,
                'field2' => $item->field2,
                'field3' => $item->field3,
                'status' => $item->is_active,
                'action' => $this->generateActionButtons($item->id)
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedData
        ]);
    }

    private function generateActionButtons($id)
    {
        return '
            <div class="btn-group">
                <button class="btn btn-sm btn-info" onclick="viewData('.$id.')"
                    data-toggle="tooltip" title="Lihat">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-warning" onclick="editData('.$id.')"
                    data-toggle="tooltip" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteData('.$id.')"
                    data-toggle="tooltip" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }
}
```

## 2. View Template (Blade)

```blade
@extends('layouts.master')

@section('content')
    <div class="section-header">
        <h1>{{ $title }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">{{ $title }}</div>
        </div>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header row">
                <div class="col-6 d-flex justify-content-start">
                    <h4>{{ $title }}</h4>
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="btnAdd">
                        <i class="fas fa-plus"></i> Tambah Data
                    </button>
                </div>
            </div>
            <div class="card-body mx-0">
                @include('components.app-datatable')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Include AppDataTable component --}}
    <script src="{{ asset('assets/js/components/app-datatable.js') }}"></script>

    <script>
        let dataTable;

        $(function() {
            // Initialize AppDataTable - HANYA INI YANG DIPERLUKAN!
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

            // Store globally for access from other functions
            window.appDataTable_{{ $tableConfig['tableId'] }} = dataTable;

            // Event handlers
            $('#btnAdd').on('click', function() {
                // Show modal atau redirect ke form
                $('#myModal').modal('show');
            });
        });

        // CRUD Functions
        function viewData(id) {
            console.log('View data:', id);
            window.location.href = '{{ url('your-route') }}/' + id;
        }

        function editData(id) {
            console.log('Edit data:', id);
            window.location.href = '{{ url('your-route') }}/' + id + '/edit';
        }

        function deleteData(id) {
            Swal.fire({
                title: 'Hapus data?',
                text: "Data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url('your-route') }}/' + id,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire('Berhasil!', 'Data telah dihapus.', 'success');
                            dataTable.reload();  // Reload table
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON?.message || 'Gagal menghapus data';
                            Swal.fire('Error!', errorMessage, 'error');
                        }
                    });
                }
            });
        }

        // Helper function untuk reload (backward compatibility)
        function get_all_data() {
            if (dataTable) {
                dataTable.reload();
            }
        }
    </script>
@endpush
```

## 3. Route Template

```php
// routes/web.php

use App\Http\Controllers\YourModule\YourController;

Route::get('/your-route', [YourController::class, 'index'])->name('your.index');
Route::get('/your-route-data', [YourController::class, 'fetchData'])->name('your.route.all');
Route::delete('/your-route/{id}', [YourController::class, 'destroy'])->name('your.destroy');
```

## Checklist untuk Implementasi

- [ ] Buat controller dengan method `index()` dan `fetchData()`
- [ ] Definisikan `$tableConfig` dengan array `columns`
- [ ] Buat view dengan `@include('components.app-datatable')`
- [ ] Include script `app-datatable.js` di `@push('scripts')`
- [ ] Initialize `new AppDataTable()` dengan config
- [ ] Buat route untuk index dan fetchData
- [ ] Pastikan API response format: `{status: 'success', data: [...]}`
- [ ] Test CRUD operations

## Perbandingan Kode

### ❌ Cara Lama (Tanpa AppDataTable)
```javascript
// Harus menulis semua ini di setiap halaman!
function get_all_data() {
    $.ajax({
        url: '/api/data',
        method: 'GET',
        beforeSend: function() { /* show loading */ },
        success: function(response) {
            renderTableData(response.data);
        },
        error: function(xhr) { /* handle error */ }
    });
}

function renderTableData(data) {
    let html = '';
    $.each(data, function(index, item) {
        html += '<tr>';
        html += '<td>' + (index + 1) + '</td>';
        html += '<td>' + item.field1 + '</td>';
        html += '<td>' + item.field2 + '</td>';
        // ... 50-100 baris kode repetitif
        html += '</tr>';
    });
    $('#table_body').html(html);
}

function showLoading() { /* ... */ }
function showEmpty() { /* ... */ }
function showError() { /* ... */ }
// Total: 200-300 baris kode per halaman!
```

### ✅ Cara Baru (Dengan AppDataTable)
```javascript
// Hanya 5 baris! Sisanya di-handle oleh AppDataTable
$(function() {
    dataTable = new AppDataTable({
        tableId: 'my_table',
        apiUrl: '/api/data',
        columns: @json($tableConfig['columns'])
    });
});
// Total: 5-10 baris kode per halaman!
```

## Tips Implementasi

1. **Copy template ini** sebagai starting point
2. **Sesuaikan field names** dengan database Anda
3. **Gunakan type yang tepat** (badge, date, currency, dll)
4. **Konsisten naming** untuk tableId dan route
5. **Test API response** menggunakan Postman/browser
6. **Reload after CRUD** untuk update tampilan
