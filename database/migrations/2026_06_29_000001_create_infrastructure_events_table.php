<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infrastructure_events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('actor_user_id')->nullable();
            $table->foreign('actor_user_id')->references('id')->on('users')->onDelete('set null');
            $table->string('action', 50);
            $table->string('route_name')->nullable();
            $table->string('request_method', 10)->nullable();
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('request_id', 40)->nullable()->index();
            $table->timestamp('occurred_at')->index();
            $table->index(['actor_user_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infrastructure_events');
    }
};
