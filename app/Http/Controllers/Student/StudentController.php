<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Repositories\StudentRepository;
use Illuminate\Support\Facades\Crypt;

class StudentController extends Controller
{
    private function settup_datatable(){
        return         # init datatable configuration
        $tableConfig = [
            # judul datatable 
            'title' => 'Table data mahasiswa',
            # table header
            'tableHead' => [
                'No',
                'NIM',
                'Nama Mahasiswa',
                'Fakultas',
                'Program Studi',
                'Tahun Masuk',
                'Status',
                'Tindakan'
            ],
            # table id
            'tableId' => 'table_mahasiswa',
            # API endpoint URL
            'url_data' => route('student.all'),
            # Column configuration for AppDataTable
            'columns' => [
                [
                    'field' => 'nim',
                    'label' => 'NIM',
                ],
                [
                    'field' => 'name',
                    'label' => 'Nama Mahasiswa',
                ],
                [
                    'field' => 'faculty',
                    'label' => 'Fakultas',
                ],
                [
                    'field' => 'program',
                    'label' => 'Program Studi',
                ],
                [
                    'field' => 'entry_year',
                    'label' => 'Tahun Masuk',
                ],
                [
                    'field' => 'status',
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
                'fields' => ['nim', 'name', 'faculty']  // Search in these fields
            ],
            # Filter configuration - Define custom filters
            'filters' => [
                [
                    'field' => 'status',
                    'label' => 'Status Aktif',
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
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        # init title page
        $title = 'Manage Data Mahasiswa';

        # count total mahasiswa
        $totalAllStudent = Student::where('status', 1)->get();
        $totalAllStudent = count($totalAllStudent);

        $tableConfig = $this->settup_datatable();

        $compact = compact('tableConfig', 'title', 'totalAllStudent');

        return view('pages.student.index', $compact);
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
            'nim' => 'required',
            'nama_mahasiswa' => 'required',
            'fakultas' => 'required',
            'jurusan' => 'required',
            'tahun_masuk' => 'required',
            'status_aktif' => 'required',
        ]);

        $insertDataStudent = [
            'nim' => $request->nim,
            'name' => $request->nama_mahasiswa,
            'faculty' => $request->fakultas,
            'program' => $request->jurusan,
            'entry_year' => $request->tahun_masuk,
            'status' => $request->status_aktif
        ];

        Student::create($insertDataStudent);

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
    public function edit(string $id)
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

            # jika data id valid ambil data mahasiswa berdasarkan id tersebut
            $getStudent = Student::find($decryptedId);

            # validasi apakah data ditemukan
            if (!$getStudent) {
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
                    'id' => Crypt::encryptString($getStudent->id),
                    'nim' => $getStudent->nim,
                    'nama_mahasiswa' => $getStudent->name,
                    'fakultas' => $getStudent->faculty,
                    'jurusan' => $getStudent->program,
                    'tahun_masuk' => $getStudent->entry_year,
                    'status_aktif' => $getStudent->status
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

            # Find student by decrypted ID
            $student = Student::find($decryptedId);

            # Validate data found
            if (!$student) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Data dosen tidak ditemukan!'
                ], 404);
            }

            # Validate request data
            $validateRequest = $request->validate([
                'nim' => 'required',
                'nama_mahasiswa' => 'required',
                'fakultas' => 'required',
                'jurusan' => 'required',
                'tahun_masuk' => 'required',
                'status_aktif' => 'required'
            ]);

            # Update data
            $student->update([
                'nim' => $request->nim,
                'name' => $request->nama_mahasiswa,
                'faculty' => $request->fakultas,
                'program' => $request->jurusan,
                'status' => $request->status_aktif
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
        try {
            # Decrypt the encrypted ID
            $decryptedId = Crypt::decryptString($id);

            # Find student by decrypted ID
            $student = Student::find($decryptedId);

            # Validate data found
            if (!$student) {
                return response()->json([
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Data dosen tidak ditemukan!'
                ], 404);
            }

            # Store student name for response message
            $studentName = $student->name;

            # Delete the student
            $student->delete();

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => "Data mahasiswa '{$studentName}' berhasil dihapus"
            ]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => 'ID tidak valid atau sudah kedaluwarsa!'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}