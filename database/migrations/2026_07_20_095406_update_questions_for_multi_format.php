<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Clear the old table to rebuild it cleanly for the new format
        Schema::dropIfExists('assessment_questions');

        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->integer('part_number')->default(1);
            $table->string('question_type'); // 'multiple_choice', 'fill_in_the_blank', 'problem_solving'
            $table->text('question_text');
            $table->json('options')->nullable(); // Only used if multiple_choice
            $table->text('correct_answer'); // Replaced correct_option to handle text/numbers
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessment_questions');
    }
};