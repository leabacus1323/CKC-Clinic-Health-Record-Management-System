<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! app()->environment(['local', 'testing', 'development'])) {
            return;
        }

        Schema::create('medical_access_events', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->char('event_id', 26)->unique();
            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->string('actor_role', 100)->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->string('action_type', 16);
            $table->string('event_name', 191);
            $table->string('route_name', 191)->nullable();
            $table->string('request_method', 10)->nullable();
            $table->string('request_path', 500)->nullable();
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 1000)->nullable();
            $table->string('resource_type', 191)->nullable();
            $table->string('resource_id', 191)->nullable();
            $table->json('metadata')->nullable();
            $table->char('integrity_hmac', 64);
            $table->timestamp('occurred_at');
            $table->timestamp('ingested_at')->useCurrent();
            $table->index(['actor_user_id', 'occurred_at']);
            $table->index(['patient_id', 'occurred_at']);
            $table->index(['action_type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        if (app()->environment(['local', 'testing', 'development']) && Schema::hasTable('medical_access_events')) {
            Schema::drop('medical_access_events');
        }
    }
};
