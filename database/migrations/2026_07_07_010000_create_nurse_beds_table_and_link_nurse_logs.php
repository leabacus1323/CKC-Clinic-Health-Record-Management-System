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
        if (! Schema::hasTable('nurse_beds')) {
            Schema::create('nurse_beds', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('location')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique('name');
                $table->index('is_active');
            });
        }

        if (Schema::hasTable('nurse_logs') && ! Schema::hasColumn('nurse_logs', 'nurse_bed_id')) {
            Schema::table('nurse_logs', function (Blueprint $table) {
                $table->unsignedInteger('nurse_bed_id')->nullable()->after('rest_minutes');
                $table->foreign('nurse_bed_id')->references('id')->on('nurse_beds')->nullOnDelete();
                $table->index(['nurse_bed_id', 'time_out'], 'nurse_logs_bed_time_out_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('nurse_logs') && Schema::hasColumn('nurse_logs', 'nurse_bed_id')) {
            Schema::table('nurse_logs', function (Blueprint $table) {
                $table->dropForeign(['nurse_bed_id']);
                $table->dropIndex('nurse_logs_bed_time_out_idx');
                $table->dropColumn('nurse_bed_id');
            });
        }

        Schema::dropIfExists('nurse_beds');
    }
};
