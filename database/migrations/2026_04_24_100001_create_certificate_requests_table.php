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
        Schema::create('certificate_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->unsignedInteger('certificate_type_id');
            $table->foreign('certificate_type_id')->references('id')->on('certificate_types')->onDelete('cascade');
            $table->unsignedInteger('doctor_id')->nullable();
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('set null');
            $table->unsignedInteger('appointment_id')->nullable();
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->text('reason')->nullable(); // Purpose/reason for the certificate
            $table->enum('status', ['pending', 'approved', 'rejected', 'issued'])->default('pending');
            $table->text('doctor_notes')->nullable();
            $table->timestamp('issued_date')->nullable();
            $table->string('document_path')->nullable(); // Path to uploaded certificate file
            $table->timestamps();
            
            // Indexes for common queries
            $table->index(['patient_id', 'status']);
            $table->index(['doctor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_requests');
    }
};
