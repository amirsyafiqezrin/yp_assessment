<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Exam;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a Lecturer (Admin)
        $lecturer = User::create([
            'name' => 'Lecturer Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_LECTURER, // 1
        ]);

        // 2. Create a Test Class
        $classA = SchoolClass::create([
            'name' => 'Class 1A',
        ]);

        // 3. Create a Test Subject & Assign to Class
        $subjectMath = Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'lecturer_id' => $lecturer->id,
        ]);

        $classA->subjects()->attach($subjectMath->id);

        // 4. Create a Student & Assign to Class
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_STUDENT, // 0
            'class_id' => $classA->id,
        ]);

        // 5. Create a Mock Exam
        $exam = Exam::create([
            'title' => 'Midterm Math Exam',
            'subject_id' => $subjectMath->id,
            'time_limit' => 30,
            'start_time' => Carbon::now()->subMinutes(5), // Available now
            'end_time' => Carbon::now()->addDays(2),
        ]);

        // Assign Exam to Class
        $exam->classes()->attach($classA->id);

        // Add Questions to Exam
        // Q1: MCQ
        $exam->questions()->create([
            'question_title' => 'What is 2 + 2?',
            'type' => \App\Models\Question::TYPE_MCQ,
            'question_score' => 2,
            'question_options' => ['3', '4', '5', '6'],
            'question_answer' => '4',
        ]);

        // Q2: Open Text
        $exam->questions()->create([
            'question_title' => 'Explain the concept of Zero.',
            'type' => \App\Models\Question::TYPE_TEXT,
            'question_score' => 5,
            'question_answer' => 'Zero is both a number and a numerical digit used to represent that number in numerals.', // Model answer
        ]);
    }
}
