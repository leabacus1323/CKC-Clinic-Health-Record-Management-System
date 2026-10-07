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
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('report_type'); // patient_statistics, user_activity, health_records, prescriptions, appointments
            $table->text('description')->nullable();
            $table->json('parameters'); // Store date range, filters, etc.
            $table->text('file_path')->nullable(); // Path to stored report file
            $table->string('generated_by_type'); // admin, staff, doctor
            $table->unsignedInteger('generated_by_id'); // user id
            $table->string('status')->default('completed'); // pending, completed, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};
