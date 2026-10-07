<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<string, array<string, string>> */
    private array $fields = [
        'health_records' => [
            'diagnosis' => 'string',
            'symptoms' => 'string',
            'treatment' => 'string',
            'notes' => 'string',
        ],
        'medical_records' => [
            'reason_for_consultation' => 'string',
            'diagnosis' => 'string',
            'prescription' => 'string',
        ],
        'dental_examinations' => [
            'teeth_status' => 'json',
            'diagnosis' => 'string',
        ],
        'appointments' => [
            'reason' => 'string',
        ],
        'physical_examinations' => [
            'heart' => 'string',
            'lungs' => 'string',
            'eyes' => 'string',
            'ears' => 'string',
            'nose' => 'string',
            'throat' => 'string',
            'skin' => 'string',
            'remarks' => 'string',
        ],
        'prescription_records' => [
            'instruction' => 'string',
            'discontinuation_reason' => 'string',
        ],
        'immunization_records' => [
            'notes' => 'string',
            'rejection_reason' => 'string',
        ],
    ];

    public function up(): void
    {
        $this->convertColumnsToText();

        foreach ($this->fields as $table => $fields) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $primaryKey = $table === 'medical_records' ? 'medical_id' : 'id';
            if (! Schema::hasColumn($table, $primaryKey)) {
                continue;
            }

            DB::table($table)->orderBy($primaryKey)->chunkById(100, function ($rows) use ($table, $fields, $primaryKey): void {
                foreach ($rows as $row) {
                    $updates = [];

                    foreach ($fields as $field => $type) {
                        if (! Schema::hasColumn($table, $field)) {
                            continue;
                        }

                        $value = $row->{$field} ?? null;
                        if ($value === null || $value === '') {
                            continue;
                        }

                        if ($this->isEncrypted((string) $value)) {
                            continue;
                        }

                        if ($type === 'json') {
                            $decoded = json_decode((string) $value, true);
                            $plainValue = json_encode(
                                json_last_error() === JSON_ERROR_NONE ? $decoded : $value,
                                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                            );
                        } else {
                            $plainValue = (string) $value;
                        }

                        $updates[$field] = Crypt::encryptString($plainValue);
                    }

                    if ($updates !== []) {
                        DB::table($table)->where($primaryKey, $row->{$primaryKey})->update($updates);
                    }
                }
            }, $primaryKey);
        }
    }

    public function down(): void
    {
        foreach ($this->fields as $table => $fields) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $primaryKey = $table === 'medical_records' ? 'medical_id' : 'id';
            if (! Schema::hasColumn($table, $primaryKey)) {
                continue;
            }

            DB::table($table)->orderBy($primaryKey)->chunkById(100, function ($rows) use ($table, $fields, $primaryKey): void {
                foreach ($rows as $row) {
                    $updates = [];
                    foreach ($fields as $field => $type) {
                        if (! Schema::hasColumn($table, $field)) {
                            continue;
                        }
                        $value = $row->{$field} ?? null;
                        if ($value === null || $value === '') {
                            continue;
                        }
                        try {
                            $updates[$field] = Crypt::decryptString((string) $value);
                        } catch (Throwable) {
                            // Preserve plaintext or values encrypted with a different key.
                        }
                    }
                    if ($updates !== []) {
                        DB::table($table)->where($primaryKey, $row->{$primaryKey})->update($updates);
                    }
                }
            }, $primaryKey);
        }
    }

    private function convertColumnsToText(): void
    {
        foreach ($this->fields as $table => $fields) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $fields): void {
                foreach (array_keys($fields) as $field) {
                    if (Schema::hasColumn($table, $field)) {
                        $blueprint->text($field)->nullable()->change();
                    }
                }
            });
        }
    }

    private function isEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (Throwable) {
            return false;
        }
    }
};
