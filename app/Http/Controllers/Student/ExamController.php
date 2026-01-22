<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Submission;
use App\Models\SubmissionQuestion;
use App\Models\Question;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->class_id) {
            $exams = collect();
        } else {
            $exams = Exam::whereHas('classes', function ($q) use ($user) {
                $q->where('classes.id', $user->class_id);
            })
                ->whereDoesntHave('submissions', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->whereNotNull('submitted_at');
                })
                ->where(function ($q) {
                    $q->whereNull('start_time')->orWhere('start_time', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('end_time')->orWhere('end_time', '>', now());
                })
                ->with('subject')
                ->latest()
                ->get();
        }

        return view('student.exams.index', compact('exams'));
    }

    public function show(Exam $exam)
    {
        if ($exam->end_time && now()->gt($exam->end_time)) {
            return redirect()->route('student.exams.index')->with('error', 'This exam has ended and can no longer be taken.');
        }

        if ($exam->start_time && now()->lt($exam->start_time)) {
            return redirect()->route('student.exams.index')->with('error', 'This exam has not started yet.');
        }

        $submission = Submission::where('user_id', auth()->id())
            ->where('exam_id', $exam->id)
            ->first();

        return view('student.exams.show', compact('exam', 'submission'));
    }

    public function start(Request $request, Exam $exam)
    {
        if ($exam->end_time && now()->gt($exam->end_time)) {
            return redirect()->route('student.exams.index')->with('error', 'This exam has ended.');
        }

        if ($exam->start_time && now()->lt($exam->start_time)) {
            return redirect()->route('student.exams.index')->with('error', 'This exam has not started yet.');
        }

        $submission = Submission::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'exam_id' => $exam->id,
            ],
            [
                'started_at' => now(),
            ]
        );

        return redirect()->route('student.exams.take', $exam);
    }

    public function take(Exam $exam)
    {
        if ($exam->end_time && now()->gt($exam->end_time)) {
            return redirect()->route('student.exams.history')->with('error', 'The exam availability period has ended.');
        }

        if ($exam->start_time && now()->lt($exam->start_time)) {
            return redirect()->route('student.exams.index')->with('error', 'This exam has not started yet.');
        }

        $submission = Submission::where('user_id', auth()->id())
            ->where('exam_id', $exam->id)
            ->firstOrFail();

        if ($submission->submitted_at) {
            return redirect()->route('student.exams.history')->with('error', 'You have already submitted this exam.');
        }

        $startTime = Carbon::parse($submission->started_at);
        $endTime = $startTime->copy()->addMinutes($exam->time_limit);
        $remainingSeconds = max(0, now()->diffInSeconds($endTime, false));

        return view('student.exams.take', compact('exam', 'submission', 'remainingSeconds'));
    }

    public function submit(Request $request, Exam $exam)
    {
        $request->validate([
            'answers' => 'nullable|array',
        ]);

        $submission = Submission::where('user_id', auth()->id())
            ->where('exam_id', $exam->id)
            ->firstOrFail();

        if ($submission->submitted_at) {
            return redirect()->route('student.exams.show', $exam)->with('error', 'Exam already submitted.');
        }

        $totalScore = 0;
        $answers = $request->answers ?? [];

        foreach ($exam->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;

            $score = 0;
            $status = SubmissionQuestion::STATUS_PENDING;
            $feedback = null;

            $hasAnswer = false;
            if (is_array($userAnswer)) {
                $hasAnswer = !empty($userAnswer);
            } else {
                $hasAnswer = !is_null($userAnswer) && $userAnswer !== '';
            }

            if (!$hasAnswer) {
                $score = 0;
                $status = SubmissionQuestion::STATUS_INCORRECT;
                $feedback = "Not Answered";
                $userAnswer = null;
            } else {
                if ($question->type == Question::TYPE_MCQ) {
                    $correctAnswers = $question->question_answer ?? [];
                    if (!is_array($correctAnswers))
                        $correctAnswers = [];

                    $userSelected = is_array($userAnswer) ? $userAnswer : [$userAnswer];

                    $matches = array_intersect($userSelected, $correctAnswers);
                    $correctCount = count($matches);
                    $totalCorrect = count($correctAnswers);

                    $mistakes = array_diff($userSelected, $correctAnswers);
                    $mistakeCount = count($mistakes);

                    if ($totalCorrect > 0) {
                        $ratio = max(0, ($correctCount - $mistakeCount) / $totalCorrect);
                        $score = round($ratio * $question->question_score, 2);
                    } else {
                        $score = 0;
                    }

                    if ($score == $question->question_score) {
                        $status = SubmissionQuestion::STATUS_CORRECT;
                    } elseif ($score > 0) {
                        $status = SubmissionQuestion::STATUS_CORRECT;
                    } else {
                        $status = SubmissionQuestion::STATUS_INCORRECT;
                    }

                    $feedback = "Final Score: $score / " . $question->question_score;

                } elseif ($question->type == Question::TYPE_TEXT) {
                    $score = 0;
                    $status = SubmissionQuestion::STATUS_PENDING;
                    $feedback = "Pending Grading";
                }
            }

            SubmissionQuestion::create([
                'submission_id' => $submission->id,
                'question_id' => $question->id,
                'submission_answer' => is_array($userAnswer) ? json_encode($userAnswer) : $userAnswer,
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

    public function review(Exam $exam)
    {
        $submission = Submission::where('user_id', auth()->id())
            ->where('exam_id', $exam->id)
            ->whereNotNull('submitted_at')
            ->with(['submissionQuestions.question'])
            ->firstOrFail();

        return view('student.exams.review', compact('exam', 'submission'));
    }

    public function history()
    {
        $user = auth()->user();

        $exams = Exam::whereHas('classes', function ($q) use ($user) {
            $q->where('classes.id', $user->class_id);
        })
            ->where(function ($q) use ($user) {
                $q->whereHas('submissions', function ($sq) use ($user) {
                    $sq->where('user_id', $user->id)->whereNotNull('submitted_at');
                })
                    ->orWhere('end_time', '<', now());
            })
            ->with([
                'submissions' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                },
                'subject'
            ])
            ->latest()
            ->get();

        return view('student.exams.history', compact('exams'));
    }
}
