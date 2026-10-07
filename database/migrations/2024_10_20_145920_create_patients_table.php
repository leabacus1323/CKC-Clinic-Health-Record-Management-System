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
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('id');
            // 1 = Student, 2 = Faculty & Staff
            $table->string('school_id',20);
            $table->string('bloodtype', 10)->nullable();
            $table->integer('patient_type');
            $table->integer('edulvl_id');
            // Foreign ID to User
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('address');
            $table->string('medical_condition');
            $table->string('medical_illness');
            $table->string('operations');
            $table->string('allergies', 50);
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_number');
            $table->string('emergency_relationship');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
