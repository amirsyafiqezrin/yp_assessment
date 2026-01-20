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

        // Get exams assigned to student's class
        // Logic: Exams assigned to Class where Class has Student
        // Relationship: Exam -> classes (many-to-many), Student -> class (belongsTo)

        $assignedExams = [];
        $subjects = [];

        if ($user->class_id) {
            $assignedExams = Exam::whereHas('classes', function ($query) use ($user) {
                $query->where('classes.id', $user->class_id);
            })
                ->where(function ($query) {
                    // Check if valid time window
                    $now = now();
                    $query->whereNull('start_time')
                        ->orWhere('start_time', '<=', $now);
                })
                // Exclude already submitted exams
                ->whereDoesntHave('submissions', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->whereNotNull('submitted_at');
                })
                ->orderBy('start_time', 'asc') // Sort by upcoming (closest time first)
                ->get();

            // Get Subjects assigned to Class
            $subjects = $user->schoolClass ? $user->schoolClass->subjects : [];
        }

        return view('student.dashboard', compact('assignedExams', 'subjects'));
    }
}
