<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubmissionQuestion;
use App\Models\Exam;
use App\Models\Submission;

class DashboardController extends Controller
{
    public function index()
    {
        // Alert Logic: Count pending open-text questions
        // In a real app, filtering by lecturer's subjects would be better.
        // Assuming Admin sees all for now or filter by Auth user's subjects.

        $lecturerId = auth()->id();

        // Get exams created by this lecturer (via subjects)
        $exams = Exam::whereHas('subject', function ($q) use ($lecturerId) {
            $q->where('lecturer_id', $lecturerId);
        })->pluck('id');

        $pendingGradingCount = SubmissionQuestion::whereIn('status', [SubmissionQuestion::STATUS_PENDING])
            ->whereHas('submission', function ($q) use ($exams) {
                $q->whereIn('exam_id', $exams);
            })
            ->count();

        return view('admin.dashboard', compact('pendingGradingCount'));
    }
}
