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
        Schema::table('health_records', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('patient_id')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('treatment')->nullable();
            $table->text('notes')->nullable();
            $table->date('date_recorded')->nullable();

            // Add foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_records', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['patient_id']);
            $table->dropColumn(['user_id', 'patient_id', 'diagnosis', 'symptoms', 'treatment', 'notes', 'date_recorded']);
        });
    }
};
