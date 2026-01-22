<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $assignedExams = [];
        $subjects = [];

        if ($user->class_id) {
            $assignedExams = Exam::whereHas('classes', function ($query) use ($user) {
                $query->where('classes.id', $user->class_id);
            })
                ->where(function ($query) {
                    $now = now();
                    $query->whereNull('end_time')
                        ->orWhere('end_time', '>=', $now);
                })
                ->whereDoesntHave('submissions', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->whereNotNull('submitted_at');
                })
                ->orderBy('start_time', 'asc')
                ->get();

            $subjects = $user->schoolClass ? $user->schoolClass->subjects : [];
        }

        return view('student.dashboard', compact('assignedExams', 'subjects'));
    }
}
