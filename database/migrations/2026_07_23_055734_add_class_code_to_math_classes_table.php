<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('math_classes', function (Blueprint $table) {
            $table->string('class_code', 10)->unique()->nullable()->after('school_year');
        });
    }

    public function down(): void
    {
        Schema::table('math_classes', function (Blueprint $table) {
            $table->dropColumn('class_code');
        });
    }
};