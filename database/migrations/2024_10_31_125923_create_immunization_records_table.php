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
        Schema::create('immunization_records', function (Blueprint $table) {
            $table->increments('id');
            /**
             * Dropdown UI
             *
             * BCG, DPT, Oral Polio, Measles, MMR, Hepatitis A or B, Others
             */
            $table->string('vaccine_name');
            /**
             * Dropdown UI
             *
             * Tablet or Injectables
             */
            $table->string('vaccine_type');
            $table->string('administered_by')->nullable();
            $table->string('dosage');
            $table->string('site_of_administration');
            $table->date('expiration_date');
            $table->string('notes')->nullable();
            // Foreign ID to Patients
            $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immunization_records');
    }
};
