<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Datatables\ApiDataTable;
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
Route::put('/student/update/{student}', [StudentController::class, 'update'])->name('student.update');
Route::get('/student/edit/{std}', [StudentController::class, 'edit'])->name('student.edit');
Route::delete('/student/delete/{std}', [StudentController::class, 'destroy'])->name('student.destroy');


Route::get('/lecture-data',[ApiDataTable::class, 'fetch_data_lecture'])->name('lecture.all');

require __DIR__.'/auth.php';
