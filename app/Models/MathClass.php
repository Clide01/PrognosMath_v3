<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MathClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_name',
        'section',
        'school_year',
        'class_code' // <-- ADD THIS LINE
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_student', 'math_class_id', 'student_id')
                    ->withPivot('status', 'current_risk_level', 'diagnosed_weak_competency')
                    ->withTimestamps();
    }
}