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
        //
        Schema::create('staff', function (Blueprint $table) {
            $table->increments('staff_id');
            $table->string('name', 50);
            $table->smallInteger('age');
            $table->tinyInteger('sex');
            $table->date('date_of_birth');
            $table->string('contact_number', 50);
            $table->string('position', 50);
            $table->smallInteger('work_experience');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
