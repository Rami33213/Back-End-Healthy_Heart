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
            Schema::create('medical_tests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
       // $table->integer('heart_rate')->nullable();
       // $table->integer('blood_pressure_systolic')->nullable();
      // $table->integer('blood_pressure_diastolic')->nullable();
        $table->float('blood_sugar')->nullable();
        $table->float('ck_mb')->nullable();
        $table->float('troponin')->nullable();
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_tests');
    }
};
