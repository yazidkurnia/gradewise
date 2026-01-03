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
        <div class="card">
            <div class="card-header row">
                <div class="col-6 d-flex justify-content-start">
                    <h4>{{ $title }}</h4>
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="btnLaunchModal">
                        + Tambah Data Baru
                    </button>
                </div>
            </div>
            <div class="card-body mx-0">
                @include('components.app-datatable')
            </div>
        </div>
    </div>
@endsection

@push('styles')
    {{-- Add page specific styles here --}}
@endpush
{{-- PINDAHKAN MODAL KE SINI - DI LUAR CARD --}}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Tambah Data Dosen
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- 
                    add class : is-valid / is-invalid
                    untuk validasi suksess pada tag input / textarea dll
                --}}
                <form action="" method="post" id="formPost">
                    @csrf
                    <div class="form-group">
                        <label>NIDN</label>
                        <input type="text" class="form-control" name="nidn" id="nidn">
                        <div id="form-error-message"></div>
                    </div>
                    <div class="form-group">
                        <label>NAMA DOSEN</label>
                        <input type="text" class="form-control" name="nama_dosen" id="nama_dosen">
                        <div id="form-error-message"></div>
                    </div>
                    <div class="form-group">
                        <label>BIDANG SPESIALIS</label>
                        <input type="text" class="form-control" name="spesialis" id="spesialis">
                        <div id="form-error-message"></div>
                    </div>
                    <div class="form-group">
                        <label>STATUS AKTIF</label>
                        <select class="form-control" name="status_aktif" id="status_aktif">
                            <option value="">Pilih Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                        <div id="form-error-message"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSave" onclick="save()">Save changes</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    {{-- Include AppDataTable component --}}
    <script src="{{ asset('assets/js/components/app-datatable.js') }}"></script>

    <script>
        // Global variable to store datatable instance
        let dataTable;

        function resetForm() {
            // Clear form fields
            $('#nidn').val('');
            $('#nama_dosen').val('');
            $('#spesialis').val('');
            $('#status_aktif').val('');

            // Reset form mode and data
            $('#formPost').removeData('edit-id');
            $('#formPost').removeData('mode');

            // Reset modal title and button
            $('#myModalLabel').text('Tambah Data Dosen');
            $('#btnSave').text('Save changes');

            // Remove validation classes
            $('.form-control').removeClass('is-valid is-invalid');
            $('.invalid-feedback').remove();
        }

        function alertLoadingState() {
            let timerInterval;
            Swal.fire({
                title: "Memproses !!",
                html: "Mohon Menunggu <b></b> milliseconds.",
                timer: 5000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                    const timer = Swal.getPopup().querySelector("b");
                    timerInterval = setInterval(() => {
                        timer.textContent = `${Swal.getTimerLeft()}`;
                    }, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.timer) {
                    console.log("I was closed by the timer");
                }
            });
        }

        function loadDataTable() {
            // Initialize AppDataTable with configuration from backend
            dataTable = new AppDataTable({
                tableId: '{{ $tableConfig['tableId'] }}',
                apiUrl: '{{ $tableConfig['url_data'] }}',
                columns: @json($tableConfig['columns']),
                @if (isset($tableConfig['search']))
                    search: @json($tableConfig['search']),
                @endif
                @if (isset($tableConfig['filters']))
                    filters: @json($tableConfig['filters']),
                @endif
                options: {
                    showNumbering: true,
                    enableTooltips: true,
                    showNotifications: true,
                    enableSearch: {{ isset($tableConfig['search']) ? 'true' : 'false' }},
                    searchPlaceholder: 'Cari NIDN, Nama, atau Bidang Khusus...'
                }
            });

            // Store instance globally for reload functionality
            window.appDataTable_{{ $tableConfig['tableId'] }} = dataTable;

            // Event handler untuk button modal
            $('#btnLaunchModal').on('click', function(e) {
                e.preventDefault();
                console.log('Button clicked - showing modal');
                resetForm(); // Reset form before showing modal for create
                $('#myModal').modal('show');
                $('.modal-backdrop fade show').remove();
            });
        }

        // Initialize DataTable when DOM is ready
        $(function() {
            loadDataTable();
        });

        // Helper function to reload data (backward compatibility)
        function get_all_data() {
            if (dataTable) {
                dataTable.reload();
            }
        }

        /**
         * View data detail
         */
        function viewData(encryptedId) {
            console.log('View data:', encryptedId);

        }

        /**
         * Edit data
         */
        function editData(encryptedId) {
            var urlEdit = '{{ url('manage-lecture/edit') }}/' + encryptedId;

            // Show loading
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: urlEdit,
                method: "GET",
                success: function(response) {
                    Swal.close();

                    if (response.status === 'success') {
                        // Populate form fields with data
                        $('#nidn').val(response.data.nidn);
                        $('#nama_dosen').val(response.data.nama_dosen);
                        $('#spesialis').val(response.data.spesialis);
                        $('#status_aktif').val(response.data.status_aktif);

                        // Store encrypted ID in form data attribute for update
                        $('#formPost').data('edit-id', response.data.id);
                        $('#formPost').data('mode', 'edit');

                        // Change modal title and save button text
                        $('#myModalLabel').text('Edit Data Dosen');
                        $('#btnSave').text('Update Data');

                        // Show modal
                        $('#myModal').modal('show');
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message || 'Gagal memuat data',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();

                    let errorMessage = 'Gagal memuat data dosen';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        /**
         * Delete data with confirmation
         */
        function deleteData(encryptedId) {
            // Get item data for confirmation
            const item = dataTable.findById(encryptedId);

            Swal.fire({
                title: 'Hapus Data Dosen?',
                html: item ?
                    `Anda akan menghapus data dosen:<br><strong>${item.name}</strong> (${item.nidn})` :
                    'Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Delete via AJAX
                    $.ajax({
                        url: '{{ url('lectures') }}/' + encryptedId,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Data dosen berhasil dihapus',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Reload datatable
                            get_all_data();
                        },
                        error: function(xhr) {
                            let errorMessage = 'Gagal menghapus data';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'Gagal!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        }

        function save() {
            // Check if in edit mode or create mode
            var mode = $('#formPost').data('mode') || 'create';
            var editId = $('#formPost').data('edit-id');

            // Determine URL based on mode
            var url;
            var method;

            if (mode === 'edit' && editId) {
                url = '{{ url('manage-lecture') }}/' + editId;
                method = 'PUT';
            } else {
                url = "{!! route('lecture.store') !!}";
                method = 'POST';
            }

            // Get form data
            var formElement = $('#formPost')[0];
            var formData = new FormData(formElement);

            // Add _method for Laravel PUT request
            if (method === 'PUT') {
                formData.append('_method', 'PUT');
            }

            // Close modal and show loading
            $('#myModal').modal('hide');
            alertLoadingState();

            // Submit via AJAX
            $.ajax({
                url: url,
                method: "POST", // Always POST, Laravel will handle PUT via _method
                data: formData,
                processData: false,
                contentType: false,
                success: function(responseData) {
                    Swal.fire({
                        title: "Success!",
                        text: mode === 'edit' ? 'Data berhasil diupdate' : 'Data berhasil ditambahkan',
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    resetForm();
                    get_all_data();
                },
                error: function(xhr) {
                    let errorMessage = 'Gagal menyimpan data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: "Oops!",
                        text: errorMessage,
                        icon: "error",
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    </script>
@endpush
