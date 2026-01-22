<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionQuestion;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index()
    {
        $allSubmissions = Submission::with(['user.schoolClass', 'exam', 'submissionQuestions'])->latest()->get();

        $pendingSubmissions = $allSubmissions->filter(function ($submission) {
            return $submission->submissionQuestions->contains('status', SubmissionQuestion::STATUS_PENDING);
        });

        $gradedSubmissions = $allSubmissions->reject(function ($submission) {
            return $submission->submissionQuestions->contains('status', SubmissionQuestion::STATUS_PENDING);
        })->groupBy(function ($submission) {
            return $submission->user->schoolClass ? $submission->user->schoolClass->name : 'Unassigned Class';
        });

        return view('admin.submissions.index', compact('pendingSubmissions', 'gradedSubmissions'));
    }

    public function show(Submission $submission)
    {
        $submission->load(['submissionQuestions.question', 'user', 'exam']);
        return view('admin.submissions.show', compact('submission'));
    }

    public function update(Request $request, Submission $submission)
    {
        $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'numeric|min:0',
            'feedback' => 'nullable|array',
            'feedback.*' => 'nullable|string',
        ]);

        $totalScore = 0;

        foreach ($request->scores as $questionId => $score) {
            $subQuestion = $submission->submissionQuestions()->where('question_id', $questionId)->first();

            if ($subQuestion) {
                $feedback = $request->feedback[$questionId] ?? $subQuestion->feedback;

                $status = ($score > 0) ? SubmissionQuestion::STATUS_CORRECT : SubmissionQuestion::STATUS_INCORRECT;

                $subQuestion->update([
                    'score' => $score,
                    'feedback' => $feedback,
                    'status' => $status
                ]);
            }
        }

        $totalScore = $submission->submissionQuestions()->sum('score');

        $submission->update([
            'total_score' => $totalScore
        ]);

        return back()->with('success', 'Submission scores updated successfully. New Total: ' . $totalScore);
    }
}
