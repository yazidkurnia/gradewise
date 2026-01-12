<?php

namespace App\Http\Controllers\Thesis;

use App\Models\Thesis;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ThesisRequest;
use Illuminate\Support\Facades\Crypt;
use App\Contracts\ThesisRepositoryInterface;
use App\Contracts\StudentRepositoryInterface;
use App\Services\Thesis\ThesisService;

class ThesisController extends Controller
{
    /**
     * Thesis Repository instance
     */
    protected $thesisRepository;
    protected $studentRepository;
    protected $thesisService;

    /**
     * Constructor with dependency injection
     */
    public function __construct(
        ThesisRepositoryInterface $thesisRepository, 
        StudentRepositoryInterface $studentRepository, 
        ThesisService $thesisService) {
        $this->thesisRepository  = $thesisRepository;
        $this->studentRepository = $studentRepository;
        $this->thesisService     = $thesisService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Skripsi Mahasiswa';

        // Fetch all thesis data using repository
        $data = $this->thesisRepository->fetch_all();

        // Fetch all data student
        $dataStudent = $this->studentRepository->fetch_all();

        foreach ($dataStudent as $list) {
            $list->encrypted_id = Crypt::encryptString($list->id);
        }

        $tableConfig = $this->thesisService->settup_datatable();
        $compact = compact('title', 'data', 'tableConfig', 'dataStudent');
        return view('pages.theses.index', $compact);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ThesisRequest $request)
    {
        return $this->thesisService->store($request->validated());
    }

    /**
     * Delete data
     */
    public function destroy(Request $request) 
    {
        return $this->thesisService->delete_data($request->id);
    }
    
}
