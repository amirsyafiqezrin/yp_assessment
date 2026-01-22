<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = User::where('role', User::ROLE_STUDENT)->with('schoolClass')->paginate(10);
        return view('admin.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = SchoolClass::all();
        return view('admin.students.create', compact('classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'class_id' => ['nullable', 'exists:classes,id'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_STUDENT,
            'class_id' => $request->class_id,
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $student)
    {
        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $student)
    {
        $classes = SchoolClass::all();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $student)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'class_id' => ['nullable', 'exists:classes,id'],
        ]);

        $student->update([
            'name' => $request->name,
            'email' => $request->email,
            'class_id' => $request->class_id,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Password::defaults()],
            ]);
            $student->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
    }
}
