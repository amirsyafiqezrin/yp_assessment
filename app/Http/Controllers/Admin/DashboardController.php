<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubmissionQuestion;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $lecturerId = auth()->id();

        $subjects = Subject::where('lecturer_id', $lecturerId)->get();
        $totalStudents = User::where('role', 'student')
            ->whereHas('schoolClass', function ($q) use ($subjects) {
                $q->whereIn('id', $subjects->pluck('classes')->flatten()->pluck('id'));
            })->distinct()->count();

        $subjectStudentCounts = [];
        foreach ($subjects as $subject) {
            $count = User::where('role', 'student')
                ->whereHas('schoolClass', function ($q) use ($subject) {
                    $q->whereHas('subjects', function ($subQ) use ($subject) {
                        $subQ->where('subjects.id', $subject->id);
                    });
                })->count();
            $subjectStudentCounts[$subject->name] = $count;
        }

        $exams = Exam::whereHas('subject', function ($q) use ($lecturerId) {
            $q->where('lecturer_id', $lecturerId);
        })->pluck('id');

        $pendingGradingCount = SubmissionQuestion::whereIn('status', [SubmissionQuestion::STATUS_PENDING])
            ->whereHas('submission', function ($q) use ($exams) {
                $q->whereIn('exam_id', $exams);
            })
            ->count();

        $pendingSubmissionsCount = Submission::whereIn('exam_id', $exams)
            ->whereNotNull('submitted_at')
            ->whereHas('submissionQuestions', function ($q) {
                $q->where('status', SubmissionQuestion::STATUS_PENDING);
            })->count();

        $totalSubjects = Subject::where('lecturer_id', $lecturerId)->count();

        $totalAvailableExams = Exam::whereHas('subject', function ($q) use ($lecturerId) {
            $q->where('lecturer_id', $lecturerId);
        })
            ->where('end_time', '>=', now())
            ->count();

        return view('admin.dashboard', compact('pendingGradingCount', 'totalStudents', 'subjectStudentCounts', 'pendingSubmissionsCount', 'totalSubjects', 'totalAvailableExams'));
    }
}
