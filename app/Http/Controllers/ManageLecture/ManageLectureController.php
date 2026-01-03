<?php

namespace App\Http\Controllers\ManageLecture;

use App\Models\Lecture;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class ManageLectureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        # init title page
        $title = 'Manage Data Dosen';

        # init datatable configuration
        $tableConfig = [
            # judul datatable
            'title' => 'Table data dosen',
            # table header
            'tableHead' => [
                'No',
                'NIDN',
                'Nama Dosen',
                'Bidang Khusus',
                'Status Aktif',
                'Tindakan'
            ],
            # table id
            'tableId' => 'table_dosen',
            # API endpoint URL
            'url_data' => route('lecture.all'),
            # Column configuration for AppDataTable
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
                    'type' => 'badge'
                ],
                [
                    'field' => 'action',
                    'label' => 'Tindakan',
                    'type' => 'actions'
                ]
            ],
            # Search configuration - Define which fields are searchable
            'search' => [
                'fields' => ['nidn', 'name', 'expertise']  // Search in these fields
            ],
            # Filter configuration - Define custom filters
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
                // You can add more filters here
                // [
                //     'field' => 'expertise',
                //     'label' => 'Bidang',
                //     'placeholder' => 'Semua Bidang',
                //     'options' => [
                //         ['value' => 'Data Science', 'label' => 'Data Science'],
                //         ['value' => 'Web Development', 'label' => 'Web Development']
                //     ]
                // ]
            ]
        ];

        $compact = compact('tableConfig', 'title');

        return view('pages.manage_lecture.index', $compact);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateRequest = $request->validate([
            'nidn' => 'required',
            'nama_dosen' => 'required',
            'spesialis' => 'required',
            'status_aktif' => 'required'
        ]);

        $insertDataLecture = [
            'nidn' => $request->nidn,
            'nama_dosen' => $request->nama_dosen,
            'expertise' => $request->spesialis,
            'is_active' => $request->status_aktif
        ];

        Lecture::create($insertDataLecture);

        return response()->json(            [
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil dikirim'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit_data(string $id)
    {
        try {
            # lakukan dekrip pada id yang terenkrpisi
            $decryptedId = Crypt::decryptString($id);

            # validasi id
            if (!is_numeric($decryptedId)) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'ID tidak valid atau corrupt!',
                    'data' => null
                ], 400);
            }

            # jika data id valid ambil data dosen berdasarkan id tersebut
            $getLecture = Lecture::find($decryptedId);  // ✅ PERBAIKAN: Gunakan $decryptedId, bukan $id

            # validasi apakah data ditemukan
            if (!$getLecture) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Data dosen tidak ditemukan!',
                    'data' => null
                ], 404);
            }

            # return data untuk form edit
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data dosen berhasil ditemukan',
                'data' => [
                    'id' => Crypt::encryptString($getLecture->id),
                    'nidn' => $getLecture->nidn,
                    'nama_dosen' => $getLecture->name,
                    'spesialis' => $getLecture->expertise,
                    'status_aktif' => $getLecture->is_active
                ]
            ]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            # Handle decryption error
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'ID tidak valid atau sudah kedaluwarsa!',
                'data' => null
            ], 400);
        } catch (\Exception $e) {
            # Handle general error
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            # Decrypt the encrypted ID
            $decryptedId = Crypt::decryptString($id);

            # Validate ID is numeric
            if (!is_numeric($decryptedId)) {
                return response()->json([
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'ID tidak valid atau corrupt!'
                ], 400);
            }

            # Find lecture by decrypted ID
            $lecture = Lecture::find($decryptedId);

            # Validate data found
            if (!$lecture) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Data dosen tidak ditemukan!'
                ], 404);
            }

            # Validate request data
            $validateRequest = $request->validate([
                'nidn' => 'required',
                'nama_dosen' => 'required',
                'spesialis' => 'required',
                'status_aktif' => 'required'
            ]);

            # Update data
            $lecture->update([
                'nidn' => $request->nidn,
                'name' => $request->nama_dosen,
                'expertise' => $request->spesialis,
                'is_active' => $request->status_aktif
            ]);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data berhasil diupdate'
            ]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'ID tidak valid atau sudah kedaluwarsa!'
            ], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
