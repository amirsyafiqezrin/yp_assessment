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

        if (!$user->schoolClass || !$user->schoolClass->subjects->contains($subject)) {
            abort(403, 'You are not enrolled in this subject.');
        }

        $exams = Exam::where('subject_id', $subject->id)
            ->whereHas('classes', function ($q) use ($user) {
                $q->where('classes.id', $user->class_id);
            })
            ->orderBy('start_time', 'asc')
            ->get();

        $upcomingExams = $exams->filter(function ($exam) use ($user) {
            $isSubmitted = $exam->submissions()->where('user_id', $user->id)->whereNotNull('submitted_at')->exists();
            $isExpired = $exam->end_time && now()->gt($exam->end_time);

            return !$isSubmitted && !$isExpired;
        });

        $historyExams = $exams->filter(function ($exam) use ($user) {
            $isSubmitted = $exam->submissions()->where('user_id', $user->id)->whereNotNull('submitted_at')->exists();
            $isExpired = $exam->end_time && now()->gt($exam->end_time);

            return $isSubmitted || $isExpired;
        });

        return view('student.subjects.show', compact('subject', 'upcomingExams', 'historyExams'));
    }
}
