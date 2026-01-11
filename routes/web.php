<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Datatables\ApiDataTable;
use App\Http\Controllers\Thesis\ThesisController;
use App\Http\Controllers\ManageLecture\ManageLectureController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Dashboard Route
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /**
     * ---------------------------------------------------------------------------------------------------------------------------
     * All route about lecture                                                                                                   |
     * ---------------------------------------------------------------------------------------------------------------------------
    **/
        Route::get('/manage-lecture', [ManageLectureController::class, 'index'])->name('lecture');
        Route::post('/manage-lecture/store', [ManageLectureController::class, 'store'])->name('lecture.store');
        Route::get('/manage-lecture/edit/{id}', [ManageLectureController::class, 'edit_data'])->name('lecture.edit');
        Route::put('/manage-lecture/{id}', [ManageLectureController::class, 'update'])->name('lecture.update');
        Route::delete('/manage-lecture/clear/{id}', [ManageLectureController::class, 'destroy'])->name('lecture.destroy');
        
    /**
     * ---------------------------------------------------------------------------------------------------------------------------
     * All route about Thesis                                                                                                   |
     * ---------------------------------------------------------------------------------------------------------------------------
    **/
        Route::get('/thesis', [ThesisController::class, 'index'])->name('thesis');
        Route::post('/thesis/new', [ThesisController::class, 'store'])->name('thesis.store');

    /**
     * ---------------------------------------------------------------------------------------------------------------------------
     * All route about student                                                                                                          |
     * ---------------------------------------------------------------------------------------------------------------------------
    **/
        Route::get('all/student/list', [StudentController::class, 'fetch_all'])->name('student.list');
        
    /**
     * ---------------------------------------------------------------------------------------------------------------------------
     * API Data Routes                                                                                                          |
     * ---------------------------------------------------------------------------------------------------------------------------
    **/
        Route::get('/lecture-data',[ApiDataTable::class, 'fetch_data_lecture'])->name('lecture.all');
        Route::get('/thesis-data',[ApiDataTable::class, 'fetch_data_thesis'])->name('thesis.all');
        Route::get('/student-data',[ApiDataTable::class, 'fetch_students'])->name('student.all');
    /**
     * ---------------------------------------------------------------------------------------------------------------------------
     * End route                                                                                                                 |
     * ---------------------------------------------------------------------------------------------------------------------------
    **/
    

});

// Route::get('/student', [StudentController::class, 'index'])->name('student');
// Route::resource('student', StudentController::class);

Route::get('/student', [StudentController::class, 'index'])->name('student');
Route::post('/student/store', [StudentController::class, 'create'])->name('student.create');
Route::get('/student/edit/{std}', [StudentController::class, 'edit'])->name('student.edit');
Route::put('/student/update/{student}', [StudentController::class, 'update'])->name('student.update');
Route::delete('/student/delete/{std}', [StudentController::class, 'destroy'])->name('student.destroy');



require __DIR__.'/auth.php';
