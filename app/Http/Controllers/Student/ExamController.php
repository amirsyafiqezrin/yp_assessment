<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Submission;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        // Similar to dashboard, show list of available exams
        return view('student.exams.index');
    }

    public function show(Exam $exam)
    {
        // Show exam instruction page before starting
        return view('student.exams.show', compact('exam'));
    }

    public function start(Request $request, Exam $exam)
    {
        // Create a submission record to track start time
        $submission = Submission::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'exam_id' => $exam->id,
            ],
            [
                'started_at' => now(),
            ]
        );

        return view('student.exams.take', compact('exam', 'submission'));
    }

    public function submit(Request $request, Exam $exam)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required', // answer for each question
        ]);

        $submission = Submission::where('user_id', auth()->id())
            ->where('exam_id', $exam->id)
            ->firstOrFail();

        if ($submission->submitted_at) {
            return redirect()->route('student.exams.show', $exam)->with('error', 'Exam already submitted.');
        }

        $totalScore = 0;

        foreach ($request->answers as $questionId => $userAnswer) {
            $question = $exam->questions->find($questionId);
            if (!$question)
                continue;

            $score = 0;
            $status = \App\Models\SubmissionQuestion::STATUS_PENDING;
            $feedback = null;

            if ($question->type == \App\Models\Question::TYPE_MCQ) {
                // Auto-grade MCQ
                // correct_answer is stored as simple string "A" or "Paris" etc.
                if ($userAnswer == $question->question_answer) {
                    $score = $question->question_score;
                    $status = \App\Models\SubmissionQuestion::STATUS_CORRECT;
                } else {
                    $score = 0;
                    $status = \App\Models\SubmissionQuestion::STATUS_INCORRECT;
                }
            } elseif ($question->type == \App\Models\Question::TYPE_TEXT) {
                // AI Grading Placeholder
                // In a real app, you might dispatch a job here or call an external service.
                // For this assessment, we'll simulate a call.
                $aiResult = $this->gradeOpenTextWithAI($userAnswer, $question->question_answer, $question->question_score);
                $score = $aiResult['score'];
                $status = $aiResult['status'];
                $feedback = $aiResult['feedback'];
            }

            \App\Models\SubmissionQuestion::create([
                'submission_id' => $submission->id,
                'question_id' => $question->id,
                'submission_answer' => $userAnswer,
                'score' => $score,
                'status' => $status,
                'feedback' => $feedback,
            ]);

            $totalScore += $score;
        }

        $submission->update([
            'submitted_at' => now(),
            'total_score' => $totalScore,
        ]);

        return redirect()->route('student.exams.history')->with('success', 'Exam submitted successfully! Total Score: ' . $totalScore);
    }

    /**
     * Simulate AI Grading
     */
    private function gradeOpenTextWithAI($studentAnswer, $modelAnswer, $maxScore)
    {
        try {
            // Attempt to call AI Service (simulated)
            // throw new \Exception("AI Service Unavailable"); // Uncomment to test fallback logic

            // Simulation of successful response
            // return [
            //    'score' => $calculatedScore,
            //    'status' => ...,
            //    'feedback' => ...
            // ];

            // For now, let's treat the default path as "queued for AI" or "pending" 
            // because we don't have the real API key here. 
            // The requirement says: "make a fallback if feature is not usable... keep both options"

            // Returning pending essentially triggers the manual fallback workflow.
            return [
                'score' => 0,
                'status' => \App\Models\SubmissionQuestion::STATUS_PENDING,
                'feedback' => 'Pending Grading (AI Service Placeholder)',
            ];

        } catch (\Exception $e) {
            // FALLBACK: If AI fails, mark as pending for manual review
            return [
                'score' => 0,
                'status' => \App\Models\SubmissionQuestion::STATUS_PENDING,
                'feedback' => 'AI Grading Failed. Pending Manual Review.',
            ];
        }
    }

    public function history()
    {
        $submissions = Submission::where('user_id', auth()->id())
            ->whereNotNull('submitted_at')
            ->with('exam')
            ->latest()
            ->get();

        return view('student.exams.history', compact('submissions'));
    }
}
