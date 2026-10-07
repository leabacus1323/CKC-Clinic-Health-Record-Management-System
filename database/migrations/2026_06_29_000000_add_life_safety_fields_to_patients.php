<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->boolean('life_safety_visible_to_teachers')->default(false)->after('allergies');
            $table->string('life_safety_alert', 500)->nullable()->after('life_safety_visible_to_teachers');
            $table->string('emergency_medication', 255)->nullable()->after('life_safety_alert');
            $table->text('life_safety_instructions')->nullable()->after('emergency_medication');
            $table->timestamp('life_safety_reviewed_at')->nullable()->after('life_safety_instructions');
            $table->unsignedInteger('life_safety_reviewed_by')->nullable()->after('life_safety_reviewed_at');
            $table->foreign('life_safety_reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['registry_type', 'life_safety_visible_to_teachers'], 'patients_teacher_safety_idx');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('patients_teacher_safety_idx');
            $table->dropConstrainedForeignId('life_safety_reviewed_by');
            $table->dropColumn([
                'life_safety_visible_to_teachers', 'life_safety_alert',
                'emergency_medication', 'life_safety_instructions', 'life_safety_reviewed_at',
            ]);
        });
    }
};
