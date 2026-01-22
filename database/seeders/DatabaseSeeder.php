<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Question;
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
        $lecturer = User::create([
            'name' => 'Lecturer A',
            'email' => 'lecturer@yp.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_LECTURER,
        ]);

        $classA = SchoolClass::create([
            'name' => 'Class 1A',
        ]);

        $subjectMath = Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'lecturer_id' => $lecturer->id,
        ]);

        $classA->subjects()->attach($subjectMath->id);

        $student = User::create([
            'name' => 'Student A',
            'email' => 'student@yp.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_STUDENT,
            'class_id' => $classA->id,
        ]);

        $exam = Exam::create([
            'title' => 'Math Exam',
            'subject_id' => $subjectMath->id,
            'time_limit' => 5,
            'start_time' => Carbon::now()->subMinutes(5)->startOfMinute(),
            'end_time' => Carbon::now()->addDays(2)->startOfMinute(),
        ]);

        $exam->classes()->attach($classA->id);

        $exam->questions()->create([
            'question_title' => 'What is 2 + 2?',
            'type' => Question::TYPE_MCQ,
            'question_score' => 2,
            'question_options' => ['3', '4', '5', '6'],
            'question_answer' => ['4'],
        ]);

        $exam->questions()->create([
            'question_title' => 'Explain the concept of Zero.',
            'type' => Question::TYPE_TEXT,
            'question_score' => 5,
            'question_answer' => 'Zero is both a number and a numerical digit used to represent that number in numerals.',
        ]);
    }
}
