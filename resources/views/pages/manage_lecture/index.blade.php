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
                <button type="button" class="btn btn-primary" onclick="save()">Save changes</button>
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
            $('#nidn').val('');
            $('#nama_dosen').val('');
            $('#spesialis').val('');
            $('#status_aktif').val('');
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

        // Initialize DataTable when DOM is ready
        $(function() {
            // Initialize AppDataTable with configuration from backend
            dataTable = new AppDataTable({
                tableId: '{{ $tableConfig['tableId'] }}',
                apiUrl: '{{ $tableConfig['url_data'] }}',
                columns: @json($tableConfig['columns']),
                @if(isset($tableConfig['search']))
                search: @json($tableConfig['search']),
                @endif
                @if(isset($tableConfig['filters']))
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
                $('#myModal').modal('show');
                $('.modal-backdrop fade show').remove();
            });
        });

        // Helper function to reload data (backward compatibility)
        function get_all_data() {
            if (dataTable) {
                dataTable.reload();
            }
        }

        function viewData(id) {
            console.log('View data:', id);
            window.location.href = '{{ url('lectures') }}/' + id;
        }

        function editData(id) {
            console.log('Edit data:', id);
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

        function save() {
            $('#myModal').modal('hide');

            // mendefinisikan url yang akan dituju untuk melakukan post data
            var url = "{!! route('lecture.store') !!}";

            // mengambil seluruh data dari elemen yang ada didalam form
            var formElement = $('#formPost')[0];

            // mengumpulkan data yang telah diambil kedalam collection
            var formData = new FormData(formElement);

            console.log(formData);

            alertLoadingState();

            // implementasi insert dengan ajax
            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                processData: false, // TAMBAHKAN INI - jangan proses data
                contentType: false, // TAMBAHKAN INI - jangan set content type
                success: function(responseData) {
                    Swal.fire({
                        title: "Success!",
                        icon: "success",
                        draggable: true
                    });
                    resetForm();
                    get_all_data();
                },
                error: function(xhr, textResponse, error) {
                    Swal.fire({
                        title: "Oops!",
                        icon: "error",
                        text: xhr.responseJSON.message,
                        draggable: true
                    });
                }
            })
        }
    </script>
@endpush
