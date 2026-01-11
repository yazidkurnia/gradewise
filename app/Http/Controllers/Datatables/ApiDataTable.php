<?php

namespace App\Http\Controllers\Datatables;

use App\Models\Thesis;
use App\Models\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Contracts\ThesisRepositoryInterface;

class ApiDataTable extends Controller
{
    /**
     * Thesis Repository instance
     */
    protected $thesisRepository;

    /**
     * Constructor with dependency injection
     */
    public function __construct(ThesisRepositoryInterface $thesisRepository)
    {
        $this->thesisRepository = $thesisRepository;
    }

    public function fetch_data_lecture(){
        $data = Lecture::select(
            'id',           // ✅ TAMBAHKAN ID!
            'nidn',
            'name',
            'expertise',
            'academic_rank',
            'is_active'
        )->get();

        # case data empty
        if (count($data) == 0){
            return response()->json(
                [
                    'code'    => 404,
                    'status'  => 'failed',
                    'message' => 'Maaf data tidak ditemukan',
                    'data'    => NULL
                ]
            );
        }

        $rowData = [];

        foreach ($data as $list) {

            $encryptedId = Crypt::encryptString($list->id);

            $action = '
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button"
                                class="btn btn-info"
                                onclick="viewData(\'' . $encryptedId . '\')"
                                data-toggle="tooltip"
                                title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button"
                                class="btn btn-warning"
                                onclick="editData(\'' . $encryptedId . '\')"
                                data-toggle="tooltip"
                                title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button"
                                class="btn btn-danger"
                                onclick="deleteData(\'' . $encryptedId . '\')"
                                data-toggle="tooltip"
                                title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';

            $rowData[] = [
                'id'        => $encryptedId,
                'nidn'      => $list->nidn,
                'name'      => $list->name,
                'expertise' => $list->expertise,
                'action'    => $action,
                'is_active' => $list->is_active
            ];
        }

        # good case response
        return response()->json(
            [
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil diterima',
                'data'    => $rowData
            ]
        );
    }

    public function fetch_data_thesis() {
        DB::enableQueryLog();
        $dataThesis = $this->thesisRepository->fetch_all();
        $thesisQuery = DB::getQueryLog();

        
        # case data empty
        if (count($dataThesis) == 0){
            return response()->json(
                [
                    'code'    => 404,
                    'status'  => 'failed',
                    'message' => 'Maaf data tidak ditemukan',
                    'data'    => NULL
                ]
            );
        }
        $rowData = [];

        foreach ($dataThesis as $list) {
            $encryptedId = Crypt::encryptString($list->id);
            $action = '
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button"
                                class="btn btn-info"
                                onclick="viewData(\'' . $encryptedId . '\')"
                                data-toggle="tooltip"
                                title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button"
                                class="btn btn-warning"
                                onclick="editData(\'' . $encryptedId . '\')"
                                data-toggle="tooltip"
                                title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button"
                                class="btn btn-danger"
                                onclick="deleteData(\'' . $encryptedId . '\')"
                                data-toggle="tooltip"
                                title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';

            $rowData[] = [
                'id'         => $encryptedId,
                'nim'        => $list->student ? $list->student->nim : '-',
                'name'       => $list->student ? $list->student->name : '-',
                'title'      => $list->title,
                'status'     => $list->status,
                'action'     => $action,
            ];
        }

        # good case response
        return response()->json(
            [
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil diterima',
                'data'    => $rowData
            ]
        );
    }

    /**
     * Fetch all students for select2
     */
    public function fetch_students()
    {
        $students = \App\Models\Student::select('id', 'nim', 'name')
                                        ->where('status', 'active')
                                        ->orderBy('name', 'asc')
                                        ->get();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Data mahasiswa berhasil diterima',
            'data' => $students
        ]);
    }
}
