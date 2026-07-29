<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('math_scores', function (Blueprint $table) {
            $table->id();
            $table->uuid('local_sync_id')->unique()->nullable(); // Created via JS when offline
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('topic');
            $table->decimal('score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->boolean('is_synced')->default(true); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('math_scores');
    }
};
