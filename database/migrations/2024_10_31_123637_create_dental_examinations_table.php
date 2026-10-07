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
        Schema::create('dental_examinations', function (Blueprint $table) {
            $table->increments('id');
            /**
             * {
             *  "teeth_status": {
             *      "teeth_no": 0,
             *      "teeth_condition": "CA"
             *   },{
             *      "teeth_no": 1,
             *      "teeth_condition": ""
             *   },
             *  }
             * }
             */
            $table->json('teeth_status');
            // Foreign ID to Patients
            $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients');
            // Foreign ID to Doctors
            $table->unsignedInteger('doctor_id');
            $table->foreign('doctor_id')->references('id')->on('doctors');
            $table->string('diagnosis');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dental_examinations');
    }
};
