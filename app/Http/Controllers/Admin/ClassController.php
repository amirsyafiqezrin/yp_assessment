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
        // Route binding handles finding the class
        // Note: Route parameter is 'class' but model is SchoolClass, confirm binding in route or use id
        // In web.php we used resource 'classes', so param is 'class'. 
        // Laravel implicitly binds {class} to SchoolClass if typehinted.
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

        // Sync subjects (this will overwrite existing associations if not using attach, 
        // usually sync is safer for a checkbox list, or attach for adding one by one. 
        // Let's assume a form where they select all subjects for the class)
        // If the UI is "Add Subject", use attach. If "Manage Subjects", use sync.
        // Given requirements "Assign Subject to Class", sync is often better for "Manage" style.
        // But if it's a simple "Add" button, attach is better.
        // Let's go with syncWithoutDetaching if we want to add, or sync if we want to strict set.
        // Let's use sync to be robust.

        $schoolClass->subjects()->sync($request->subject_ids);

        return back()->with('success', 'Subjects assigned successfully.');
    }
}
