<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Drop the tables if they already exist from the partial crash
        Schema::dropIfExists('student_submissions');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessments');

        // 2. Recreate them cleanly
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('math_class_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['quiz', 'activity', 'assignment', 'examination']);
            $table->string('topic');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });

        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->json('options'); 
            $table->string('correct_option');
            $table->timestamps();
        });

        Schema::create('student_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->json('answers_submitted'); 
            $table->json('scratchpad_data');    
            $table->json('time_per_question');  
            $table->integer('total_time_spent');
            $table->integer('calculated_score');
            $table->text('ai_weakness_diagnosis')->nullable();
            $table->timestamps();
        });
        
        // Note: The pivot table (math_class_user) code was removed from here 
        // because we successfully migrated it to 'class_student' in the other file!
    }

    public function down()
    {
        Schema::dropIfExists('student_submissions');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessments');
    }
};