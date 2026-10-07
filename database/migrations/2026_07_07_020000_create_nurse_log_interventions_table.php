<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('nurse_log_interventions')) {
            Schema::create('nurse_log_interventions', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('nurse_log_id');
                $table->enum('intervention_type', [
                    'prescribed_medicine',
                    'mgh',
                    'rest',
                    'first_aid',
                    'other',
                ]);
                $table->text('notes')->nullable();
                $table->unsignedSmallInteger('medicine_id')->nullable();
                $table->unsignedInteger('prescription_record_id')->nullable();
                $table->unsignedInteger('medicine_quantity')->nullable();
                $table->string('dosage')->nullable();
                $table->string('frequency')->nullable();
                $table->string('duration')->nullable();
                $table->text('prescription_instruction')->nullable();
                $table->string('mgh_status')->nullable();
                $table->integer('rest_minutes')->nullable();
                $table->unsignedInteger('nurse_bed_id')->nullable();
                $table->text('first_aid_details')->nullable();
                $table->timestamps();

                $table->foreign('nurse_log_id')->references('id')->on('nurse_logs')->cascadeOnDelete();
                $table->foreign('medicine_id')->references('id')->on('medicine')->nullOnDelete();
                $table->foreign('prescription_record_id')->references('id')->on('prescription_records')->nullOnDelete();
                $table->foreign('nurse_bed_id')->references('id')->on('nurse_beds')->nullOnDelete();

                $table->index(['intervention_type', 'created_at'], 'nurse_log_interventions_type_created_idx');
                $table->index(['nurse_bed_id', 'intervention_type'], 'nurse_log_interventions_bed_type_idx');
            });
        }

        $this->backfillExistingSingleInterventions();
    }

    /**
     * Move existing single-intervention nurse logs into the new detail table.
     */
    private function backfillExistingSingleInterventions(): void
    {
        if (! Schema::hasTable('nurse_logs') || ! Schema::hasTable('nurse_log_interventions')) {
            return;
        }

        DB::table('nurse_logs')
            ->orderBy('id')
            ->chunkById(100, function ($logs): void {
                foreach ($logs as $log) {
                    $alreadyMigrated = DB::table('nurse_log_interventions')
                        ->where('nurse_log_id', $log->id)
                        ->exists();

                    if ($alreadyMigrated) {
                        continue;
                    }

                    DB::table('nurse_log_interventions')->insert([
                        'nurse_log_id' => $log->id,
                        'intervention_type' => $log->intervention_type ?? 'other',
                        'notes' => $log->intervention_notes ?? null,
                        'medicine_id' => $log->medicine_id ?? null,
                        'prescription_record_id' => $log->prescription_record_id ?? null,
                        'medicine_quantity' => $log->medicine_quantity ?? null,
                        'mgh_status' => $log->mgh_status ?? null,
                        'rest_minutes' => $log->rest_minutes ?? null,
                        'nurse_bed_id' => $log->nurse_bed_id ?? null,
                        'first_aid_details' => $log->first_aid_details ?? null,
                        'created_at' => $log->created_at ?? now(),
                        'updated_at' => $log->updated_at ?? now(),
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurse_log_interventions');
    }
};
