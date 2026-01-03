<?php

namespace App\Http\Controllers\Datatables;

use App\Models\Lecture;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class ApiDataTable extends Controller
{
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
}
