<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('message');
            $table->unsignedInteger('created_by')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
