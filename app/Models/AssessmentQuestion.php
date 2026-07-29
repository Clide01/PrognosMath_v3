<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'part_number',
        'question_type',
        'question_text',
        'options',
        'correct_answer'
    ];

    // Automatically convert the JSON options column into a PHP array
    protected $casts = [
        'options' => 'array',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}