<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('learning_materials', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->text('content_body')->nullable(); // For long AI text
            $table->text('student_answer')->nullable(); // For the student's reply
            $table->string('status')->default('pending'); // pending or completed
        });
    }

    public function down()
    {
        Schema::table('learning_materials', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropColumn(['student_id', 'content_body', 'student_answer', 'status']);
        });
    }
};