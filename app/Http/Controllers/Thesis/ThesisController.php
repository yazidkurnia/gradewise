<?php

namespace App\Http\Controllers\Thesis;

use App\Models\Thesis;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ThesisRequest;
use App\Contracts\ThesisRepositoryInterface;

class ThesisController extends Controller
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

    /**
     * Setup datatable configuration for thesis
     *
     * @return array
     */
    private function settup_datatable()
    {
        // Init datatable configuration
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Skripsi Mahasiswa';

        // Fetch all thesis data using repository
        $data = $this->thesisRepository->fetch_all();

        $tableConfig = $this->settup_datatable();
        $compact = compact('title', 'data', 'tableConfig');
        return view('pages.theses.index', $compact);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ThesisRequest $request)
    {
        try {

            // Check if student already has thesis
            $existingThesis = Thesis::where('student_id', $request->student_id)->first();
            if ($existingThesis) {
                return response()->json([
                    'code' => 422,
                    'status' => 'error',
                    'message' => 'Mahasiswa ini sudah memiliki skripsi!'
                ], 422);
            }

            // Create thesis
            $thesis = Thesis::create([
                'student_id' => $request->student_id,
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status
            ]);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data skripsi berhasil disimpan',
                'data' => $thesis
            ]);

        } catch (ValidationException $e) {
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

    public function fetch_mahasiswa_aktif() {
        
    }
}
