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
            $table->string('frequency')->nullable()->after('dosage');
            $table->string('duration')->nullable()->after('frequency');
            $table->date('date_prescribed')->nullable()->after('duration');
            $table->date('date_discontinued')->nullable()->after('date_prescribed');
            $table->enum('status', ['active', 'discontinued', 'completed'])->default('active')->after('date_discontinued');
            $table->text('discontinuation_reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_records', function (Blueprint $table) {
            $table->dropColumn(['frequency', 'duration', 'date_prescribed', 'date_discontinued', 'status', 'discontinuation_reason']);
        });
    }
};
