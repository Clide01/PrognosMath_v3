<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('class_student', function (Blueprint $table) {
            $table->string('current_risk_level')->default('Low');
            $table->string('diagnosed_weak_competency')->default('None');
        });
    }

    public function down()
    {
        Schema::table('class_student', function (Blueprint $table) {
            $table->dropColumn(['current_risk_level', 'diagnosed_weak_competency']);
        });
    }
};