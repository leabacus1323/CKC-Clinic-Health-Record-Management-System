<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('patients', 'bloodtype')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->string('bloodtype', 10)->nullable()->after('school_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('patients', 'bloodtype')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropColumn('bloodtype');
            });
        }
    }
};
