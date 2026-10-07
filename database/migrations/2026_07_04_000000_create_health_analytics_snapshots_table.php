<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_analytics_snapshots', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedSmallInteger('period_days')->default(30)->index();
            $table->date('period_start');
            $table->date('period_end');
            $table->json('metrics');
            $table->json('trends');
            $table->json('recommendations');
            $table->string('generated_by', 30)->default('scheduler');
            $table->unsignedInteger('generated_by_user_id')->nullable();
            $table->foreign('generated_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('generated_at')->index();
            $table->timestamps();

            $table->index(['period_days', 'generated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_analytics_snapshots');
    }
};
