<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Exam;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function show(Subject $subject)
    {
        $user = auth()->user();

        // Verify user has access to this subject (via Class)
        if (!$user->schoolClass || !$user->schoolClass->subjects->contains($subject)) {
            abort(403, 'You are not enrolled in this subject.');
        }

        // Get Exams for this subject assigned to user's class
        $exams = Exam::where('subject_id', $subject->id)
            ->whereHas('classes', function ($q) use ($user) {
                $q->where('classes.id', $user->class_id);
            })
            ->orderBy('start_time', 'asc') // Sort by upcoming
            ->get();

        // Categorize
        $upcomingExams = $exams->filter(function ($exam) use ($user) {
            // Check if submitted
            $isSubmitted = $exam->submissions()->where('user_id', $user->id)->whereNotNull('submitted_at')->exists();
            // Check timing (if start_time is future, or available now and not submitted)
            return !$isSubmitted;
        });

        $historyExams = $exams->filter(function ($exam) use ($user) {
            return $exam->submissions()->where('user_id', $user->id)->whereNotNull('submitted_at')->exists();
        });

        return view('student.subjects.show', compact('subject', 'upcomingExams', 'historyExams'));
    }
}
