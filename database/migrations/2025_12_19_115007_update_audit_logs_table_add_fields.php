<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // تعديل user_id ليصبح nullable
            $table->foreignId('user_id')->nullable()->change();
            
            // إضافة الأعمدة الجديدة
            $table->text('description')->nullable()->after('action');
            $table->string('ip_address')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // حذف الأعمدة الجديدة
            $table->dropColumn(['description', 'ip_address']);
            
            // إرجاع user_id لحالته الأصلية (مش nullable)
            // ملاحظة: ممكن يعطي error إذا في صفوف فيها user_id = null
        });
    }
};
