<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
    ];

    public function students()
    {
        return $this->hasMany(User::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_class', 'class_id', 'exam_id');
    }
}
