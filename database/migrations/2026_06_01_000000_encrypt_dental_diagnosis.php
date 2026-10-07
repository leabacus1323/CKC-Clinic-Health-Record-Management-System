<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add searchable hash column
        Schema::table('dental_examinations', function (Blueprint $table) {
            $table->string('diagnosis_search')->nullable()->after('diagnosis');
        });

        // Backfill: encrypt plaintext diagnosis values and populate search hash
        DB::table('dental_examinations')->orderBy('id')->chunk(100, function ($rows) {
            foreach ($rows as $row) {
                $diag = $row->diagnosis;
                if ($diag === null || $diag === '') {
                    continue;
                }

                // If already encrypted, skip
                try {
                    Crypt::decryptString($diag);
                    // decrypt succeeded -> already encrypted
                    continue;
                } catch (\Throwable $e) {
                    // Not encrypted, encrypt and store
                    $hash = hash('sha256', Str::lower(trim($diag)));
                    $encrypted = Crypt::encryptString($diag);
                    DB::table('dental_examinations')->where('id', $row->id)->update([
                        'diagnosis' => $encrypted,
                        'diagnosis_search' => $hash,
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dental_examinations', function (Blueprint $table) {
            $table->dropColumn('diagnosis_search');
        });
    }
};
