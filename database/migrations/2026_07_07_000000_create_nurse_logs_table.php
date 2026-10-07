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
        Schema::create('nurse_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('staff_user_id');
            $table->dateTime('time_in');
            $table->dateTime('time_out')->nullable();
            $table->text('complaint')->nullable();
            $table->enum('intervention_type', [
                'prescribed_medicine',
                'mgh',
                'rest',
                'first_aid',
                'other',
            ])->default('other');
            $table->text('intervention_notes')->nullable();
            $table->unsignedSmallInteger('medicine_id')->nullable();
            $table->unsignedInteger('prescription_record_id')->nullable();
            $table->unsignedInteger('medicine_quantity')->nullable();
            $table->string('mgh_status')->nullable();
            $table->integer('rest_minutes')->nullable();
            $table->text('first_aid_details')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients');
            $table->foreign('staff_user_id')->references('id')->on('users');
            $table->foreign('medicine_id')->references('id')->on('medicine');
            $table->foreign('prescription_record_id')->references('id')->on('prescription_records');

            $table->index(['time_in', 'intervention_type']);
            $table->index('patient_id');
            $table->index('staff_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurse_logs');
    }
};
