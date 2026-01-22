<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'type',
        'question_title',
        'question_options',
        'question_answer',
        'question_score',
    ];

    protected $casts = [
        'question_options' => 'array',
        'question_answer' => 'array',
    ];

    const TYPE_MCQ = 1;
    const TYPE_TEXT = 2;

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function submissionQuestions()
    {
        return $this->hasMany(SubmissionQuestion::class);
    }
}
