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
        Schema::create('medicine', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name');
            $table->string('description', 50);
            $table->string('quantity', 50);
            $table->date('expiration_date');
            $table->string('medicine_type');
            // CRON Jobs to set status expired if the date is on the date.
            $table->enum('status', ['Active', 'Expired'])->default('Active');
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
