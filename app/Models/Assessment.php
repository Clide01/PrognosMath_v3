<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'math_class_id',
        'title',
        'type',
        'topic',
        'pdf_path',
        'difficulty_score',
        'expected_pass_rate'
    ];

    protected $casts = [
        'difficulty_score' => 'float',
        'expected_pass_rate' => 'float',
    ];

    public function mathClass()
    {
        return $this->belongsTo(MathClass::class);
    }

    public function questions()
    {
        return $this->hasMany(AssessmentQuestion::class);
    }
}