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

        <div class="row">
            <div class="col-4">
                <div class="card">
                    <div class="card-header text-dark">
                        Total Seluruh Data Skripsi
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                {{-- <h3 class="text-primary">{{ $totalAllLecture }}</h3> --}}
                                {{-- <span>Dosen Aktif</span> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-header text-dark">
                        Total Skripsi Akan Sidang Proposal
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                {{-- <h3 class="text-danger">{{ $totalAllLecture }}</h3> --}}
                                {{-- <span>Dosen Tidak Aktif</span> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">

                <div class="card">
                    <div class="card-header text-dark">
                        Total Skripsi Akan Sidang Akhir
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                {{-- <h3 class="text-danger">{{ $totalAllLecture }}</h3> --}}
                                {{-- <span>Dosen Tidak Aktif</span> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header row">
                        <div class="col-6 d-flex justify-content-start">
                            <h4>{{ $title }}</h4>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" id="btnLaunchModal" onclick="add_data()">
                                + Tambah Data Baru
                            </button>
                        </div>
                    </div>
                    <div class="card-body mx-0">
                        @include('components.app-datatable')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Add/Edit Thesis --}}
@endsection

@section('modal')
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Tambah Data Skripsi
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="formPost">
                        @csrf
                        <div class="form-group">
                            <label for="student_id">Mahasiswa <span class="text-danger">*</span></label>
                            <select class="form-control select-student" id="student_id" name="student_id"
                                style="width: 100%;">
                                <option value="">-- Pilih Mahasiswa --</option>
                            </select>
                            <small class="form-text text-muted">Pilih mahasiswa yang akan mengajukan skripsi</small>
                        </div>
                        <div class="form-group">
                            <label for="title">Judul Skripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="title" id="title" rows="3" placeholder="Masukkan judul skripsi"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea class="form-control" name="description" id="description" rows="4"
                                placeholder="Masukkan deskripsi singkat (opsional)"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" id="status">
                                <option value="">-- Pilih Status --</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
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
@endsection

@push('styles')
    {{-- Select2 CSS CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endpush

@push('scripts')
    {{-- Select2 JS CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- Include AppDataTable component --}}
    <script src="{{ asset('assets/js/components/app-datatable.js') }}"></script>

    <script>
        function add_data() {
            $('#myModal').modal('show');
            // $('.modal-backdrop fade show').remove();
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

        /**
         * Reset form
         */
        function resetForm() {
            // Clear form fields
            $('#student_id').val('').trigger('change'); // Reset Select2
            $('#title').val('');
            $('#description').val('');
            $('#status').val('');

            // Reset form mode and data
            $('#formPost').removeData('edit-id');
            $('#formPost').removeData('mode');

            // Reset modal title and button
            $('#myModalLabel').text('Tambah Data Skripsi');
            $('#btnSave').text('Save changes');

            // Remove validation classes
            $('.form-control').removeClass('is-valid is-invalid');
            $('.invalid-feedback').remove();
        }

        /**
         * Load students for Select2
         */
        function loadStudents() {
            $.ajax({
                url: '{{ route('student.list') }}', // Adjust this to your student API endpoint
                method: 'GET',
                success: function(response) {
                    // Clear existing options except placeholder
                    $('#student_id').empty().append('<option value="">-- Pilih Mahasiswa --</option>');

                    // Assuming response returns array of students
                    if (response.data && Array.isArray(response.data)) {
                        response.data.forEach(function(student) {
                            $('#student_id').append(
                                $('<option></option>')
                                .val(student.id)
                                .text(student.nim + ' - ' + student.name)
                            );
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Failed to load students:', xhr);
                }
            });
        }

        /**
         * Initialize DataTable when DOM is ready
         */
        $(function() {
            loadDataTable();

            // Initialize Select2
            $('.select-student').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Mahasiswa --',
                allowClear: true,
                width: '100%'
            });

            // Load students data
            loadStudents();
        });

        /**
         * Helper function to reload data (backward compatibility)
         */
        function get_all_data() {
            if (dataTable) {
                dataTable.reload();
            }
        }

        /**
         * Save thesis data
         */
        function save() {
            // Validation
            if (!$('#student_id').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih mahasiswa terlebih dahulu!'
                });
                return;
            }

            if (!$('#title').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Judul skripsi tidak boleh kosong!'
                });
                return;
            }

            if (!$('#status').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih status terlebih dahulu!'
                });
                return;
            }

            // Close modal and show loading
            $('#myModal').modal('hide');

            Swal.fire({
                title: 'Menyimpan Data...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Get form data
            var formData = new FormData($('#formPost')[0]);

            // Submit via AJAX
            $.ajax({
                url: '{{ route('thesis.store') }}', // You need to create this route
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data skripsi berhasil disimpan',
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
                        icon: 'error',
                        title: 'Oops!',
                        text: errorMessage
                    });
                }
            });
        }
    </script>
@endpush
