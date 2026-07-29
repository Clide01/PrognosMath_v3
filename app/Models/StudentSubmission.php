<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessment_id',
        'answers_submitted',
        'scratchpad_data',
        'time_per_question',
        'total_time_spent',
        'calculated_score',
        'ai_weakness_diagnosis'
    ];

    protected $casts = [
        'answers_submitted' => 'array',
        'scratchpad_data' => 'array',
        'time_per_question' => 'array',
        'calculated_score' => 'float',
        'total_time_spent' => 'integer',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}