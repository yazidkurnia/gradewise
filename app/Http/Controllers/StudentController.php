<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Contracts\StudentRepositoryInterface;

class StudentController extends Controller
{
    /**
     * Thesis Repository instance
     */
    protected $studentRepository;

    /**
     * Constructor with dependency injection
     */
    public function __construct(StudentRepositoryInterface $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }
    // READ
    public function index()
    {
        $students = Student::all();
        return view('student.index', compact('students'));
    }

    // FORM CREATE
    public function create()
    {
        return view('student.create');
    }

    // SIMPAN DATA
    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required',
            'name' => 'required',
            'faculty' => 'required',
            'program' => 'required',
            'entry_year' => 'required',
            'status' => 'required'
        ]);

        Student::create($request->all());
        return redirect()->route('student.index')
                         ->with('success', 'Data berhasil ditambahkan');
    }

    // FORM EDIT
    public function edit(Student $student)
    {
        return view('student.edit', compact('student'));
    }

    // UPDATE DATA
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nim' => 'required',
            'name' => 'required',
            'faculty' => 'required',
            'program' => 'required',
            'entry_year' => 'required',
            'status' => 'required'
        ]);

        $student->update($request->all());
        return redirect()->route('student.index')
                         ->with('success', 'Data berhasil diupdate');
    }

    // DELETE
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('student.index')
                         ->with('success', 'Data berhasil dihapus');
    }

    public function fetch_all() {
        $data = $this->studentRepository->fetch_all();

        if (empty($data)) {
            return response()->json([
                'code' => 404,
                'message' => 'Maaf data tidak ditemukan',
                'data' => NULL
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Berhasil mendapatkan data',
            'data' => []
        ]);
    }
}