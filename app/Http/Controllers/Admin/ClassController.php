<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = SchoolClass::withCount('students')->get();
        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.classes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classes',
        ]);

        SchoolClass::create($request->all());

        return redirect()->route('admin.classes.index')->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SchoolClass $class)
    {
        return view('admin.classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolClass $class)
    {
        return view('admin.classes.edit', compact('class'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchoolClass $class)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classes,name,' . $class->id,
        ]);

        $class->update($request->all());

        return redirect()->route('admin.classes.index')->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolClass $class)
    {
        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Class deleted successfully.');
    }

    public function assignSubject(Request $request, SchoolClass $schoolClass)
    {
        $request->validate([
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $schoolClass->subjects()->sync($request->subject_ids);

        return back()->with('success', 'Subjects assigned successfully.');
    }
}
