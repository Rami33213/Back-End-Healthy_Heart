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
        Schema::table('expert_consultations', function (Blueprint $table) {
            // نص التشخيص النهائي (مثلاً: "نقص تروية يؤدي لاحتشاء عضلة القلب")
            $table->string('diagnosis_label')->nullable()->after('recommendation');

            // مستوى الخطورة: high / medium / low
            $table->string('risk_level')->nullable()->after('diagnosis_label');

            // درجة الخطورة رقمية بين 0 و 1 (أو نسبة مئوية لو حبيت)
            $table->decimal('risk_score', 4, 2)->nullable()->after('risk_level');
            // مثال قيم: 0.95, 0.60, 0.30
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expert_consultations', function (Blueprint $table) {
            $table->dropColumn(['diagnosis_label', 'risk_level', 'risk_score']);
        });
    }
};
