<?php

namespace App\Http\Controllers\Datatables;

use App\Models\Lecture;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiDataTable extends Controller
{
    public function fetch_data_lecture(){
        $data = Lecture::select(
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
                    'code' => 404,
                    'status' => 'failed',
                    'message' => 'Maaf data tidak ditemukan',
                    'data' => NULL
                ]
            );
        }

        $rowData = [];

        foreach ($data as $list) {
            $rowData[] = [
                'nidn' => $list->nidn,
                'name' => $list->name,
                'expertise' => $list->expertise,
                'action' => 'empty',
                'is_active' => $list->is_active
            ];
        }

        # good case response
        return response()->json(
            [
                'code' => 200,
                'status' => 'success',
                'message' => 'Data berhasil diterima',
                'data' => $rowData
            ]
        );
    }
}
