<?php 

namespace App\Services\Thesis;

use Illuminate\Support\Facades\Crypt;

class ThesisService {

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

        $dataStore = [
            'student_id' => $studentId,
            'title'      => $thesisTitle,
            'start_date' => $thesisStartDate
        ];
    }

    public function destroy($thesisId){
        dd($thesisId);
    }
}