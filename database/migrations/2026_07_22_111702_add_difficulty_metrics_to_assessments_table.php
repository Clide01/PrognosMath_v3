<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->decimal('difficulty_score', 5, 2)->nullable()->after('topic'); // e.g., 0.85
            $table->decimal('expected_pass_rate', 5, 2)->nullable()->after('difficulty_score'); // e.g., 65.50
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            //
        });
    }
};
