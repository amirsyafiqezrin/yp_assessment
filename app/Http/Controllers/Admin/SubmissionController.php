<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = Submission::with(['user', 'exam'])->latest()->paginate(10);
        return view('admin.submissions.index', compact('submissions'));
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
                // Update specific question score and feedback
                $feedback = $request->feedback[$questionId] ?? $subQuestion->feedback;

                // Determine status manually if needed, or arguably if score > 0 it's correct/partial
                // But let's just stick to score update.
                $status = ($score > 0) ? \App\Models\SubmissionQuestion::STATUS_CORRECT : \App\Models\SubmissionQuestion::STATUS_INCORRECT;
                // If score is different from max, maybe partial? Let's leave status inference simple or manual.

                $subQuestion->update([
                    'score' => $score,
                    'feedback' => $feedback,
                    'status' => $status
                ]);
            }
        }

        // Recalculate total score from fresh DB data to be safe
        $totalScore = $submission->submissionQuestions()->sum('score');

        $submission->update([
            'total_score' => $totalScore
        ]);

        return back()->with('success', 'Submission scores updated successfully. New Total: ' . $totalScore);
    }
}
