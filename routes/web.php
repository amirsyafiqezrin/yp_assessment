<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\SubjectController as StudentSubjectController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Logic to redirect based on role if hitting /dashboard
    Route::get('/dashboard', function () {
        if (auth()->user()->isLecturer()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('student.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Lecturer Routes
    Route::middleware(['role:lecturer'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('classes', ClassController::class);
        Route::resource('subjects', SubjectController::class);
        Route::resource('students', StudentController::class);
        Route::resource('exams', ExamController::class);
        Route::resource('submissions', SubmissionController::class)->only(['index', 'show', 'update']);

        // Custom Assignment Routes
        Route::post('classes/{schoolClass}/assign-subject', [
            ClassController::class,
            'assignSubject'
        ])->name('classes.assign-subject');
        Route::post('exams/{exam}/assign-class', [ExamController::class, 'assignClass'])->name('exams.assign-class');

        // Question Management (Nested under exams)
        Route::post('exams/{exam}/questions', [ExamController::class, 'storeQuestion'])->name('exams.questions.store');
    });

    // Student Routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Subject Routes
        Route::get('/subjects/{subject}', [StudentSubjectController::class, 'show'])->name('subjects.show');

        // Exam Routes
        Route::get('/exams', [StudentExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/{exam}', [StudentExamController::class, 'show'])->name('exams.show');
        Route::post('/exams/{exam}/start', [StudentExamController::class, 'start'])->name('exams.start');
        Route::post('/exams/{exam}/submit', [StudentExamController::class, 'submit'])->name('exams.submit');
        Route::get('/history', [StudentExamController::class, 'history'])->name('exams.history');
    });

});

require __DIR__ . '/auth.php';