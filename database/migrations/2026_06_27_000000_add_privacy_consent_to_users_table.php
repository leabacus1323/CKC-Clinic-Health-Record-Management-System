<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('privacy_terms_version', 40)->nullable();
            $table->timestamp('privacy_terms_accepted_at')->nullable();
            $table->string('privacy_terms_accepted_ip', 45)->nullable();
            $table->text('privacy_terms_accepted_user_agent')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'privacy_terms_version',
                'privacy_terms_accepted_at',
                'privacy_terms_accepted_ip',
                'privacy_terms_accepted_user_agent',
            ]);
        });
    }
};
