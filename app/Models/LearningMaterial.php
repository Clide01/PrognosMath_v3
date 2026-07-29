<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'competency_id',
        'title',
        'content',
        'content_url',
        'content_body',
        'type',
        'status',
        'student_answer',
    ];
}