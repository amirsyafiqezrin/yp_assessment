<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'question_id',
        'submission_answer',
        'score',
        'status',
        'feedback',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    const STATUS_PENDING = 0;
    const STATUS_CORRECT = 1;
    const STATUS_INCORRECT = 2;
    const STATUS_PARTIALLY_CORRECT = 3;

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
