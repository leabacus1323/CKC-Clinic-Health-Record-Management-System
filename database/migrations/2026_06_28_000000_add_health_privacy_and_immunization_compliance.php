<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table): void {
            $table->string('registry_type', 30)->nullable()->after('patient_type')->index();
            $table->string('privacy_regime', 40)->nullable()->after('registry_type')->index();
        });

        $studentRegime = (string) config('health_privacy.student_privacy_regime', 'ferpa_student');
        $nonStudentRegime = (string) config('health_privacy.non_student_privacy_regime', 'hipaa_workforce');

        DB::table('patients')->orderBy('id')->chunkById(200, function ($patients) use ($studentRegime, $nonStudentRegime): void {
            foreach ($patients as $patient) {
                $student = (int) $patient->patient_type === 1;
                DB::table('patients')->where('id', $patient->id)->update([
                    'registry_type' => $student ? 'student' : 'non_student',
                    'privacy_regime' => $student ? $studentRegime : $nonStudentRegime,
                ]);
            }
        });

        Schema::create('patient_guardians', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->unsignedInteger('guardian_user_id')->nullable();
            $table->foreign('guardian_user_id')->references('id')->on('users')->onDelete('set null');
            $table->string('invited_email');
            $table->string('full_name');
            $table->string('relationship', 80);
            $table->boolean('can_manage_consents')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable()->index();
            $table->unsignedInteger('approved_by_user_id')->nullable();
            $table->foreign('approved_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->text('approval_notes')->nullable();
            $table->unsignedInteger('created_by_user_id')->nullable();
            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
            $table->index(['invited_email', 'is_active']);
            $table->unique(['patient_id', 'invited_email']);
        });

        Schema::create('data_authorizations', function (Blueprint $table): void {
            $table->increments('id');
            $table->uuid('public_id')->unique();
            $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->unsignedInteger('grantor_user_id');
            $table->foreign('grantor_user_id')->references('id')->on('users')->onDelete('restrict');
            $table->unsignedInteger('recipient_user_id')->nullable();
            $table->foreign('recipient_user_id')->references('id')->on('users')->onDelete('set null');
            $table->string('recipient_name');
            $table->string('recipient_email');
            $table->json('scopes');
            $table->text('purpose');
            $table->string('signature_name');
            $table->string('signature_hash', 64);
            $table->string('authorization_version', 40);
            $table->longText('authorization_text');
            $table->string('status', 20)->default('active')->index();
            $table->timestamp('effective_at')->useCurrent();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('revoked_at')->nullable();
            $table->unsignedInteger('revoked_by_user_id')->nullable();
            $table->foreign('revoked_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->text('revocation_reason')->nullable();
            $table->string('signed_ip', 45)->nullable();
            $table->text('signed_user_agent')->nullable();
            $table->timestamps();
            $table->index(['patient_id', 'status']);
            $table->index(['recipient_user_id', 'status']);
            $table->index(['recipient_email', 'status']);
        });

        Schema::create('break_glass_events', function (Blueprint $table): void {
            $table->increments('id');
            $table->uuid('public_id')->unique();
            $table->unsignedInteger('actor_user_id');
            $table->foreign('actor_user_id')->references('id')->on('users')->onDelete('restrict');
            $table->unsignedInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('restrict');
            $table->text('threat_description');
            $table->text('justification');
            $table->string('recipient_party')->nullable();
            $table->json('disclosed_scopes');
            $table->string('session_fingerprint', 64);
            
            // Fixed timestamp definitions here to avoid MySQL 1067 strict date errors
            $table->timestamp('activated_at')->useCurrent();
            $table->timestamp('expires_at')->nullable()->index();
            
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('previous_hash', 64)->nullable();
            $table->string('entry_hash', 64)->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['actor_user_id', 'created_at']);
            $table->index(['patient_id', 'created_at']);
        });

        Schema::create('access_audit_logs', function (Blueprint $table): void {
            $table->increments('id');
            $table->uuid('public_id')->unique();
            $table->unsignedInteger('actor_user_id')->nullable();
            $table->foreign('actor_user_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
            $table->string('action', 80)->index();
            $table->string('access_type', 30)->index();
            $table->json('scopes')->nullable();
            $table->text('purpose')->nullable();
            $table->string('route_name')->nullable();
            $table->string('request_method', 10)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->string('previous_hash', 64)->nullable();
            $table->string('entry_hash', 64)->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['patient_id', 'created_at']);
            $table->index(['actor_user_id', 'created_at']);
        });

        Schema::create('immunization_requirements', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('registry_type', 30)->default('student')->index();
            $table->unsignedSmallInteger('minimum_doses')->default(1);
            $table->unsignedInteger('due_age_months')->nullable();
            $table->unsignedInteger('booster_interval_months')->nullable();
            $table->date('required_by_date')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('immunization_records', function (Blueprint $table): void {
            $table->unsignedInteger('immunization_requirement_id')->nullable()->after('id');
            $table->foreign('immunization_requirement_id')->references('id')->on('immunization_requirements')->onDelete('set null');
            $table->date('administered_at')->nullable()->after('vaccine_type');
            $table->unsignedSmallInteger('dose_number')->default(1)->after('administered_at');
            $table->string('verification_status', 20)->default('verified')->after('notes')->index();
            $table->string('source', 30)->default('clinic_entry')->after('verification_status');
            $table->string('document_path')->nullable();
            $table->string('document_original_name')->nullable();
            $table->string('document_mime', 120)->nullable();
            $table->unsignedBigInteger('document_size')->nullable();
            $table->string('document_sha256', 64)->nullable();
            $table->unsignedInteger('uploaded_by_user_id')->nullable();
            $table->foreign('uploaded_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedInteger('verified_by_user_id')->nullable();
            $table->foreign('verified_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->index(['patient_id', 'verification_status']);
            $table->date('expiration_date')->nullable()->change();
        });

        $this->installAppendOnlyTriggers();
    }

    public function down(): void
    {
        $this->dropAppendOnlyTriggers();

        Schema::table('immunization_records', function (Blueprint $table): void {
            // Drop foreign keys first to prevent constraint violations
            $table->dropForeign(['immunization_requirement_id']);
            $table->dropForeign(['uploaded_by_user_id']);
            $table->dropForeign(['verified_by_user_id']);
        });

        Schema::table('immunization_records', function (Blueprint $table): void {
            // Revert expiration date state change safely during rollback
            $table->date('expiration_date')->nullable(false)->change();

            $table->dropColumn([
                'immunization_requirement_id', 'administered_at', 'dose_number',
                'verification_status', 'source', 'document_path', 'document_original_name',
                'document_mime', 'document_size', 'document_sha256', 'uploaded_by_user_id',
                'verified_by_user_id', 'verified_at', 'rejected_at', 'rejection_reason',
            ]);
        });

        Schema::dropIfExists('immunization_requirements');
        Schema::dropIfExists('access_audit_logs');
        Schema::dropIfExists('break_glass_events');
        Schema::dropIfExists('data_authorizations');
        Schema::dropIfExists('patient_guardians');

        Schema::table('patients', function (Blueprint $table): void {
            $table->dropColumn(['registry_type', 'privacy_regime']);
        });
    }

    private function installAppendOnlyTriggers(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared("CREATE TRIGGER prevent_break_glass_update BEFORE UPDATE ON break_glass_events FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Break-glass records are append-only'");
        DB::unprepared("CREATE TRIGGER prevent_break_glass_delete BEFORE DELETE ON break_glass_events FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Break-glass records cannot be deleted'");
        DB::unprepared("CREATE TRIGGER prevent_access_audit_update BEFORE UPDATE ON access_audit_logs FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Audit records are append-only'");
        DB::unprepared("CREATE TRIGGER prevent_access_audit_delete BEFORE DELETE ON access_audit_logs FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Audit records cannot be deleted'");
    }

    private function dropAppendOnlyTriggers(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS prevent_break_glass_update');
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_break_glass_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_access_audit_update');
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_access_audit_delete');
    }
};