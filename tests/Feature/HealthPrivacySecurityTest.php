<?php

namespace Tests\Feature;

use App\Models\AccessAuditLog;
use App\Models\PatientGuardian;
use App\Models\Patients;
use App\Models\User;
use App\Services\AccessAuditService;
use App\Services\AuditIntegrityService;
use App\Services\PatientAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HealthPrivacySecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_principal_can_open_emergency_console(): void
    {
        $principal = $this->user(User::TYPE_PRINCIPAL);
        $administrator = $this->user(User::TYPE_ADMIN);

        $this->actingAs($principal)
            ->get(route('break-glass.index'))
            ->assertOk();

        $this->actingAs($administrator)
            ->get(route('break-glass.index'))
            ->assertForbidden();
    }


    public function test_principal_cannot_bypass_break_glass_through_legacy_clinical_routes(): void
    {
        $principal = $this->user(User::TYPE_PRINCIPAL);

        $this->actingAs($principal)
            ->get(route('health-records.index'))
            ->assertForbidden();
    }

    public function test_break_glass_requires_reauthentication_and_creates_append_only_event(): void
    {
        $principal = $this->user(User::TYPE_PRINCIPAL);
        $patient = $this->studentPatient(now()->subYears(16));

        $this->actingAs($principal)
            ->post(route('break-glass.store', $patient), [
                'threat_description' => 'The student is unconscious and emergency responders require allergy information.',
                'justification' => 'Immediate access is necessary to reduce a serious and imminent health risk.',
                'recipient_party' => 'Responding emergency medical team',
                'current_password' => 'password',
                'confirmation' => '1',
            ])
            ->assertRedirect(route('break-glass.show', $patient));

        $this->assertDatabaseCount('break_glass_events', 1);
        $this->assertDatabaseHas('access_audit_logs', ['action' => 'break_glass_activated']);
    }

    public function test_guardian_has_no_record_access_until_relationship_is_approved(): void
    {
        config(['health_privacy.student_rights_transfer_mode' => 'age_18']);

        $patient = $this->studentPatient(now()->subYears(16));
        $guardian = $this->user(User::TYPE_GUARDIAN);
        $link = PatientGuardian::create([
            'patient_id' => $patient->id,
            'guardian_user_id' => $guardian->id,
            'invited_email' => $guardian->email,
            'full_name' => $guardian->name,
            'relationship' => 'Parent',
            'can_manage_consents' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        $access = app(PatientAccessService::class);
        $this->assertFalse($access->canViewScope($guardian, $patient, 'allergies'));

        $link->update([
            'approved_at' => now(),
            'approved_by_user_id' => $this->user(User::TYPE_ADMIN)->id,
        ]);

        $this->assertTrue($access->canViewScope($guardian, $patient->fresh(), 'allergies'));
    }

    public function test_postsecondary_mode_transfers_consent_control_to_student_regardless_of_age(): void
    {
        config(['health_privacy.student_rights_transfer_mode' => 'postsecondary']);

        $patient = $this->studentPatient(now()->subYears(17));
        $access = app(PatientAccessService::class);

        $this->assertFalse($patient->requiresGuardianConsent());
        $this->assertTrue($access->canManageConsent($patient->user, $patient));
    }

    public function test_hash_chain_detects_a_tampered_audit_entry(): void
    {
        $actor = $this->user(User::TYPE_STAFF);
        $patient = $this->studentPatient(now()->subYears(20));
        $request = Request::create('/privacy-test', 'GET');

        app(AccessAuditService::class)->record(
            $actor,
            $patient,
            'authorized_record_viewed',
            'consent',
            ['allergies'],
            'Authorized test access',
            [],
            $request,
        );

        $integrity = app(AuditIntegrityService::class);
        $this->assertTrue($integrity->verify()['access_audit_logs']['valid']);

        if (DB::getDriverName() === 'mysql') {
            $this->expectException(QueryException::class);
            DB::table('access_audit_logs')->where('id', AccessAuditLog::query()->value('id'))
                ->update(['purpose' => 'tampered']);

            return;
        }

        DB::table('access_audit_logs')->where('id', AccessAuditLog::query()->value('id'))
            ->update(['purpose' => 'tampered']);

        $this->assertFalse($integrity->verify()['access_audit_logs']['valid']);
    }

    private function user(int $type, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'user_type' => $type,
            'privacy_terms_version' => (string) config('privacy.terms_version'),
            'privacy_terms_accepted_at' => now(),
        ], $attributes));
    }

    private function studentPatient($dateOfBirth): Patients
    {
        $user = $this->user(User::TYPE_STUDENT, ['dob' => $dateOfBirth->toDateString()]);

        return Patients::create([
            'patient_type' => 1,
            'edulvl_id' => null,
            'user_id' => $user->id,
            'school_id' => 'STU-'.$user->id,
            'bloodtype' => 'O+',
            'address' => 'Test address',
            'medical_condition' => 'None recorded',
            'medical_illness' => 'None recorded',
            'operations' => 'None recorded',
            'allergies' => 'Peanuts',
            'emergency_contact_name' => 'Test Guardian',
            'emergency_contact_number' => '09170000000',
            'emergency_relationship' => 'Parent',
        ]);
    }
}
