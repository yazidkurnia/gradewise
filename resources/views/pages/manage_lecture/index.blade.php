@extends('layouts.master')

@section('content')
    <div class="section-header">
        <h1>{{ $title }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">{{ $title }}</a></div>
            <div class="breadcrumb-item">{{ $title }}</div>
        </div>
    </div>

    <div class="section-body">
        @include('components.app-datatable')
    </div>
@endsection

@push('styles')
    {{-- Add page specific styles here --}}
@endpush

@push('scripts')
    {{-- Add page specific scripts here --}}
    <script>
        function get_all_data() {
            $.ajax({
                url: '{{ $tableConfig['url_data'] }}',
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    // Tampilkan loading state
                    $('#{{ $tableConfig['tableId'] }}_body').html(
                        '<tr><td colspan="100%" class="text-center">' +
                        '<div class="spinner-border text-primary" role="status">' +
                        '<span class="sr-only">Loading...</span>' +
                        '</div>' +
                        '<p class="mt-2">Memuat data...</p>' +
                        '</td></tr>'
                    );
                },
                success: function(response) {
                    console.log('Response:', response);

                    // Cek status response
                    if (response.status === 'success' || response.status === 'ok') {
                        // Jika ada data
                        if (response.data && response.data.length > 0) {
                            renderTableData(response.data);

                            // Tampilkan notifikasi sukses
                            if (typeof iziToast !== 'undefined') {
                                iziToast.success({
                                    title: 'Sukses',
                                    message: 'Data berhasil dimuat',
                                    position: 'topRight',
                                    timeout: 2000
                                });
                            }
                        } else {
                            // Data kosong
                            showEmptyState();
                        }
                    } else if (response.status === 'failed' || response.status === 'error') {
                        // Handle failed response
                        handleFailedResponse(response);
                    } else {
                        // Status tidak dikenal
                        showEmptyState('Format response tidak valid');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Ajax Error:');
                    console.error('├─ Status Code:', xhr.status);
                    console.error('├─ Status Text:', xhr.statusText);
                    console.error('├─ Error Type:', status);
                    console.error('├─ Error Message:', error);
                    console.error('└─ Response:', xhr.responseText);

                    let errorMessage = 'Terjadi kesalahan saat memuat data';

                    // Handle berbagai jenis error
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

                    // Tampilkan error di tabel
                    showErrorState(errorMessage);

                    // Tampilkan notifikasi error
                    if (typeof iziToast !== 'undefined') {
                        iziToast.error({
                            title: 'Error',
                            message: errorMessage,
                            position: 'topRight',
                            timeout: 5000
                        });
                    }
                },
                complete: function() {
                    console.log('Request selesai');
                }
            });
        }

        function renderTableData(data) {
            let html = '';

            // Loop through data dan buat table rows
            $.each(data, function(index, item) {
                html += '<tr>';
                html += '<td class="text-center">' + (index + 1) + '</td>';
                html += '<td>' + (item.nidn || '-') + '</td>';
                html += '<td>' + (item.name || '-') + '</td>';
                html += '<td>' + (item.expertise || '-') + '</td>';
                html += '<td>' + (item.is_active) + '</td>'
                html += '<td></td>'
                html += '</tr>';
            });

            $('#{{ $tableConfig['tableId'] }}_body').html(html);

            // Initialize tooltips jika ada
            if (typeof $('[data-toggle="tooltip"]').tooltip === 'function') {
                $('[data-toggle="tooltip"]').tooltip();
            }
        }

        function handleFailedResponse(response) {
            let message = response.message || 'Gagal memuat data';

            if (response.data === null || (Array.isArray(response.data) && response.data.length === 0)) {
                // Data kosong
                showEmptyState(message);
            } else {
                // Ada error lain
                showErrorState(message);
            }

            // Tampilkan notifikasi
            if (typeof iziToast !== 'undefined') {
                iziToast.warning({
                    title: 'Perhatian',
                    message: message,
                    position: 'topRight',
                    timeout: 3000
                });
            }
        }

        function showEmptyState(message = 'Tidak ada data yang tersedia') {
            const html = `
                <tr>
                    <td colspan="100%" class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">${message}</h5>
                            <p class="text-muted">Data belum tersedia atau belum ada yang ditambahkan.</p>
                            <button type="button" class="btn btn-primary mt-3" onclick="addNewData()">
                                <i class="fas fa-plus"></i> Tambah Data Baru
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            $('#{{ $tableConfig['tableId'] }}_body').html(html);
        }

        function showErrorState(message) {
            const html = `
                <tr>
                    <td colspan="100%" class="text-center py-5">
                        <div class="error-state">
                            <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                            <h5 class="text-danger">Terjadi Kesalahan</h5>
                            <p class="text-muted">${message}</p>
                            <button type="button" class="btn btn-primary mt-3" onclick="get_all_data()">
                                <i class="fas fa-sync"></i> Muat Ulang
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            $('#{{ $tableConfig['tableId'] }}_body').html(html);
        }

        // Function untuk action buttons
        function viewData(id) {
            console.log('View data:', id);
            // Implementasi view detail
            window.location.href = '{{ url('lectures') }}/' + id;
        }

        function editData(id) {
            console.log('Edit data:', id);
            // Implementasi edit
            window.location.href = '{{ url('lectures') }}/' + id + '/edit';
        }

        function deleteData(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                $.ajax({
                    url: '{{ url('lectures') }}/' + id,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (typeof iziToast !== 'undefined') {
                            iziToast.success({
                                title: 'Sukses',
                                message: 'Data berhasil dihapus',
                                position: 'topRight'
                            });
                        }
                        // Reload data
                        get_all_data();
                    },
                    error: function(xhr) {
                        let errorMessage = 'Gagal menghapus data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        if (typeof iziToast !== 'undefined') {
                            iziToast.error({
                                title: 'Error',
                                message: errorMessage,
                                position: 'topRight'
                            });
                        }
                    }
                });
            }
        }

        function addNewData() {
            window.location.href = '{{ url('lectures/create') }}';
        }

        // Auto-run saat halaman load
        $(function() {
            get_all_data();
        });
    </script>
@endpush
