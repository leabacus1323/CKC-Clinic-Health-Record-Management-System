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
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            // Will be concatenated with f_name, l_name, and m_name
            $table->string('name');
            // 1 = Student, 2 = Faculty & Staff
            $table->integer('user_type');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('f_name');
            $table->string('m_name');
            $table->string('l_name');
            $table->date('dob');
            $table->string('address');
            $table->string('gender');
            $table->string('contact_no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
