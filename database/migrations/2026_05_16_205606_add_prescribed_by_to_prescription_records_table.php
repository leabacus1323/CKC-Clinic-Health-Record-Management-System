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
        Schema::table('prescription_records', function (Blueprint $table) {
            if (!Schema::hasColumn('prescription_records', 'prescribed_by')) {
                $table->integer('prescribed_by')->nullable()->after('medicine_id');
            }
            if (!Schema::hasColumn('prescription_records', 'frequency')) {
                $table->string('frequency')->nullable();
            }
            if (!Schema::hasColumn('prescription_records', 'duration')) {
                $table->string('duration')->nullable();
            }
            if (!Schema::hasColumn('prescription_records', 'instruction')) {
                $table->string('instruction')->nullable();
            }
            if (!Schema::hasColumn('prescription_records', 'status')) {
                $table->string('status')->default('active');
            }
            if (!Schema::hasColumn('prescription_records', 'discontinuation_reason')) {
                $table->text('discontinuation_reason')->nullable();
            }
            if (!Schema::hasColumn('prescription_records', 'date_discontinued')) {
                $table->timestamp('date_discontinued')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_records', function (Blueprint $table) {
            $table->dropColumn(['prescribed_by', 'frequency', 'duration', 'instruction', 'status', 'discontinuation_reason', 'date_discontinued']);
        });
    }
};
