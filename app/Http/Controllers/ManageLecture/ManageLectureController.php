<?php

namespace App\Http\Controllers\ManageLecture;

use App\Models\Lecture;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
