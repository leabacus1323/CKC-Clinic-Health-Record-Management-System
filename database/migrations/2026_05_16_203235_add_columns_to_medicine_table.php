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
        Schema::table('medicine', function (Blueprint $table) {
            if (!Schema::hasColumn('medicine', 'generic_name')) {
                $table->string('generic_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('medicine', 'manufacturer')) {
                $table->string('manufacturer')->nullable()->after('generic_name');
            }
            if (!Schema::hasColumn('medicine', 'dosage')) {
                $table->string('dosage')->nullable()->after('manufacturer');
            }
            if (!Schema::hasColumn('medicine', 'unit')) {
                $table->string('unit')->nullable()->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine', function (Blueprint $table) {
            $table->dropColumn(['generic_name', 'manufacturer', 'dosage', 'unit']);
        });
    }
};
