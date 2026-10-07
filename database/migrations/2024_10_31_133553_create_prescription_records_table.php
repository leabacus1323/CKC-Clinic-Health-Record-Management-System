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
        Schema::create('prescription_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dosage');
            $table->string('instruction');
            // Foreign ID to Patients
            $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients');
            // Foreign ID to Doctor
            $table->integer('doctor_id');
            // Foreign ID to Medicine
            $table->integer('medicine_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_records');
    }
};
