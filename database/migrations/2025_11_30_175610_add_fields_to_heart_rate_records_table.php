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
        Schema::table('heart_rate_records', function (Blueprint $table) {
            $table->decimal('confidence', 5, 3)->nullable()->after('heart_rate_value');
            $table->decimal('processing_time', 8, 2)->nullable()->after('confidence');
            $table->json('metadata')->nullable()->after('processing_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heart_rate_records', function (Blueprint $table) {
            $table->dropColumn(['confidence', 'processing_time', 'metadata']);
        });
    }
};