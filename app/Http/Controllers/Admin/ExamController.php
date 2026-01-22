<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Question;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = Exam::with(['subject', 'classes'])->get();
        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::all();
        return view('admin.exams.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'time_limit' => 'required|integer|min:1',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        $data = $request->all();

        if ($request->filled('start_time')) {
            $data['start_time'] = Carbon::parse($request->start_time, 'Asia/Kuala_Lumpur')->setTimezone('UTC');
        }

        if ($request->filled('end_time')) {
            $data['end_time'] = Carbon::parse($request->end_time, 'Asia/Kuala_Lumpur')->setTimezone('UTC');
        }

        Exam::create($data);

        return redirect()->route('admin.exams.index')->with('success', 'Exam created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        $exam->load(['questions', 'classes']);
        return view('admin.exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        $subjects = Subject::all();
        $classes = SchoolClass::all();
        return view('admin.exams.edit', compact('exam', 'subjects', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'time_limit' => 'required|integer|min:1',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        $data = $request->all();

        if ($request->filled('start_time')) {
            $data['start_time'] = Carbon::parse($request->start_time, 'Asia/Kuala_Lumpur')->setTimezone('UTC');
        }

        if ($request->filled('end_time')) {
            $data['end_time'] = Carbon::parse($request->end_time, 'Asia/Kuala_Lumpur')->setTimezone('UTC');
        }

        $exam->update($data);

        return redirect()->route('admin.exams.index')->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully.');
    }

    public function assignClass(Request $request, Exam $exam)
    {
        $request->validate([
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:classes,id',
        ]);

        $exam->classes()->sync($request->class_ids);

        return back()->with('success', 'Classes assigned successfully.');
    }

    public function storeQuestion(Request $request, Exam $exam)
    {
        $request->validate([
            'question_title' => 'required|string',
            'type' => 'required|in:1,2',
            'question_score' => 'required|integer|min:1',
            'question_options' => 'required_if:type,1|array|min:2',
            'question_answer' => 'required_if:type,1',
        ]);

        $options = null;
        $answer = $request->question_answer;

        if ($request->type == 1) {
            $options = array_values(array_filter($request->question_options, fn($value) => !is_null($value) && $value !== ''));
            if (count($options) < 2) {
                return back()->withErrors(['question_options' => 'At least 2 valid options are required for MCQ.']);
            }

            if (!is_array($answer)) {
                return back()->withErrors(['question_answer' => 'Please select at least one correct answer.']);
            }
        }

        $exam->questions()->create([
            'question_title' => $request->question_title,
            'type' => $request->type,
            'question_score' => $request->question_score,
            'question_options' => $options,
            'question_answer' => $answer,
        ]);

        return back()->with('success', 'Question added successfully.');
    }

    public function destroyQuestion(Exam $exam, Question $question)
    {
        if ($question->exam_id !== $exam->id) {
            abort(404);
        }

        $question->delete();

        return back()->with('success', 'Question deleted successfully.');
    }
    public function updateQuestion(Request $request, Exam $exam, Question $question)
    {
        if ($question->exam_id !== $exam->id) {
            abort(404);
        }

        $request->validate([
            'question_title' => 'required|string',
            'type' => 'required|in:1,2',
            'question_score' => 'required|integer|min:1',
            'question_options' => 'required_if:type,1|array|min:2',
            'question_answer' => 'required_if:type,1',
        ]);

        $options = null;
        $answer = $request->question_answer;

        if ($request->type == 1) {
            $options = array_values(array_filter($request->question_options, fn($value) => !is_null($value) && $value !== ''));
            if (count($options) < 2) {
                return back()->withErrors(['question_options' => 'At least 2 valid options are required for MCQ.']);
            }

            if (!is_array($answer)) {
                return back()->withErrors(['question_answer' => 'Please select at least one correct answer.']);
            }
        }

        $question->update([
            'question_title' => $request->question_title,
            'type' => $request->type,
            'question_score' => $request->question_score,
            'question_options' => $options,
            'question_answer' => $answer,
        ]);

        return back()->with('success', 'Question updated successfully.');
    }
}
