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
        Schema::table('physical_examinations', function (Blueprint $table) {
            // Drop the existing foreign key constraint for user_id
            $table->dropForeign(['user_id']);

            // Rename user_id column to patient_id
            $table->renameColumn('user_id', 'patient_id');

            // Add new foreign key constraint for patient_id with cascade delete
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('physical_examinations', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['patient_id']);

            // Rename patient_id back to user_id
            $table->renameColumn('patient_id', 'user_id');

            // Restore the original foreign key constraint
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};
