<?php 

namespace App\Services\Thesis;

use App\Models\Thesis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Repositories\ThesisRepository;

class ThesisService {

    protected $thesisRepository;

    public function __construct(
        ThesisRepository $thesisRepository
    ){
        $this->thesisRepository = $thesisRepository;
    }

    public function settup_datatable() {
                return [
            // Judul datatable
            'title' => 'Table Data Skripsi Mahasiswa',
            // Table header
            'tableHead' => [
                'No',
                'Nama Mahasiswa',
                'NIM',
                'Judul Skripsi',
                'Status',
                'Tindakan'
            ],
            // Table id
            'tableId' => 'table_thesis',
            // API endpoint URL
            'url_data' => route('thesis.all'),
            // Column configuration for AppDataTable
            'columns' => [
                [
                    'field' => 'name',
                    'label' => 'Nama Mahasiswa',
                ],
                [
                    'field' => 'nim',
                    'label' => 'NIM',
                ],
                [
                    'field' => 'title',
                    'label' => 'Judul Skripsi',
                ],
                [
                    'field' => 'status',
                    'label' => 'Status',
                    'type' => 'badge'
                ],
                [
                    'field' => 'action',
                    'label' => 'Tindakan',
                    'type' => 'actions'
                ]
            ],
            // Search configuration - Define which fields are searchable
            'search' => [
                'fields' => ['title', 'name', 'nim']
            ],
            // Filter configuration - Define custom filters
            'filters' => [
                [
                    'field' => 'status',
                    'label' => 'Status',
                    'placeholder' => 'Semua Status',
                    'options' => [
                        ['value' => 'pending', 'label' => 'Pending'],
                        ['value' => 'approved', 'label' => 'Approved'],
                        ['value' => 'rejected', 'label' => 'Rejected']
                    ]
                ]
            ]
        ];
    }

    public function store(array $dataStore) {
        $studentId       = Crypt::decryptString($dataStore['student_id']);
        $thesisTitle     = $dataStore['title'];
        $thesisStartDate = $dataStore['start_date'];
        $fileThesis      = $dataStore['final_document_url'];

        // find thesis by student_id
        $checkExistingData = $this->thesisRepository->fetch_with_student((int)$studentId);

        if ($checkExistingData != NULL) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Oops! sepertinya kamu sudah pernah mendaftarkan diri silahkan check proses!'
            ]);
        }

        if($thesisTitle == '' || empty($thesisTitle)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Oops! Sepertinya kamu mengosongkan field judul skripsi silahkan periksa kembali!'
            ]);
        }

        if ($thesisStartDate == '' || empty($thesisStartDate)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Oops! Sepertinya kamu mengosongkan field tanggal awal mulai skripsi skripsi silahkan periksa kembali!'
            ]);
        }

        // ------------------------------------------------------------------------------------------------------------------ //
        //                                                     handle file upload                                             //
        // ------------------------------------------------------------------------------------------------------------------ //

        $hashFileName = $fileThesis->hashName();
        if ($fileThesis->getSize() > 5024) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Oops! Sepertinya ukuran file terlalu besar, silakukan lakukan kompres file sebelum kembali mengupload!'
            ]);
        }

        $allowedExt = ['pdf', 'doc', 'jpg', 'JPG', 'png', 'PNG'];

        $fileExt = $fileThesis->getClientOriginalExtension();

        if (!in_array($fileExt, $allowedExt)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Oops! Maaf tipe file yang diupload tidak diizinkan!'
            ]);
        }

        $path = $fileThesis->store();

        // ------------------------------------------------------------------------------------------------------------------ //
        //                                                 end handle file upload                                             //
        // ------------------------------------------------------------------------------------------------------------------ //
        
        if (empty($path)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Oops! Maaf sepertinya upload file tidak berhasil silahkan coba kembali jika terus berulang silahkan hubungi administrator!'
            ]);
        }

        // ------------------------------------------------------------------------------------------------------------------ //
        //                                                     DB Transaction                                                 //
        // ------------------------------------------------------------------------------------------------------------------ //

        try {
            # code...
            DB::beginTransaction();
              $createNewData = [
                'student_id'         => $studentId,
                'title'              => $thesisTitle,
                'final_document_url' => $path,
                'start_date'         => $thesisStartDate,
                'status'             => 'Aktif'
            ];
            Thesis::create($createNewData);
            DB::commit();

            return response()->json([
                'status' => 'Success',
                'message' => 'Yeay! Data dan file berhasil disimpan!'
            ]);
        } catch (\Throwable $e) {
            # code...
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' => 'Oops! Maaf sepertinya upload file tidak berhasil silahkan coba kembali jika terus berulang silahkan hubungi administrator!'
            ]);
        }

        // ------------------------------------------------------------------------------------------------------------------ //
        //                                                 end DB Transaction                                                 //
        // ------------------------------------------------------------------------------------------------------------------ //
    }

    public function delete_data($thesisId){
        
        if ($thesisId == '') {
            return response()->json([
                'status' => 'failed',
                'message' => 'Oups! terjadi kesalahan saat menghapus data! data corrupt atau tidak lagi tersedia'
                ]);
        }

        $decryptedId = Crypt::decryptString($thesisId);

        try {
            DB::beginTransaction();
            Thesis::destroy($decryptedId);
            DB::commit();
            return response()->json([
                'status' => 'Success',
                'message' => 'Yeay! Data berhasil dihapus!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Oops! Data tidak berhasil dihapus silahkan coba kembali jika terus berulang silahkan hubungi administrator sistem!'
            ]);
        }
    }
}