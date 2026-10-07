<?php

namespace Tests\Feature;

use App\Models\Patients;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedMedicalAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_administrator_cannot_open_patient_medical_routes(): void
    {
        $administrator = $this->user(User::TYPE_ADMIN);

        $this->actingAs($administrator)
            ->get(route('admin.patients.index'))
            ->assertForbidden();

        $this->actingAs($administrator)
            ->get(route('health-records.index'))
            ->assertForbidden();
    }

    public function test_teacher_receives_only_published_life_safety_projection(): void
    {
        $teacher = $this->user(User::TYPE_TEACHER);
        $patient = $this->studentPatient([
            'life_safety_visible_to_teachers' => true,
            'life_safety_alert' => 'Severe peanut allergy; carries an EpiPen.',
            'emergency_medication' => 'Epinephrine auto-injector in school bag.',
            'life_safety_instructions' => 'Use the auto-injector and call emergency services.',
            'medical_condition' => 'CONFIDENTIAL-DIAGNOSIS-TEXT',
            'medical_illness' => 'CONFIDENTIAL-TREATMENT-TEXT',
            'operations' => 'CONFIDENTIAL-CLINICAL-NOTE',
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.safety.show', $patient))
            ->assertOk()
            ->assertSee('Severe peanut allergy')
            ->assertSee('Epinephrine auto-injector')
            ->assertDontSee('CONFIDENTIAL-DIAGNOSIS-TEXT')
            ->assertDontSee('CONFIDENTIAL-TREATMENT-TEXT')
            ->assertDontSee('CONFIDENTIAL-CLINICAL-NOTE');

        $events = app('medical-audit.test-events');
        $this->assertTrue(collect($events)->contains(fn (array $event): bool =>
            $event['actor_user_id'] === $teacher->id
            && $event['patient_id'] === $patient->id
            && $event['action_type'] === 'READ'
            && $event['event_name'] === 'teacher.safety.show'
        ));
    }

    public function test_teacher_cannot_update_the_life_safety_snapshot_or_open_clinical_records(): void
    {
        $teacher = $this->user(User::TYPE_TEACHER);
        $patient = $this->studentPatient();

        $this->actingAs($teacher)
            ->put(route('clinical.life-safety.update', $patient), [
                'life_safety_visible_to_teachers' => true,
                'life_safety_alert' => 'Attempted change',
            ])
            ->assertForbidden();

        $this->actingAs($teacher)
            ->get(route('admin.patients.index'))
            ->assertForbidden();
    }

    public function test_school_nurse_and_doctor_are_authorized_clinical_operators(): void
    {
        $nurse = $this->user(User::TYPE_STAFF);
        $doctor = $this->user(User::TYPE_DOCTOR);

        $this->actingAs($nurse)
            ->get(route('clinical.life-safety.index'))
            ->assertOk();

        $this->actingAs($doctor)
            ->get(route('clinical.life-safety.index'))
            ->assertOk();
    }

    public function test_inactive_authenticated_session_is_locked_after_three_minutes(): void
    {
        $nurse = $this->user(User::TYPE_STAFF);

        $this->actingAs($nurse)
            ->withSession([
                'security_last_activity_at' => now()->subSeconds(181)->timestamp,
                'security_session_locked' => false,
            ])
            ->get(route('clinical.life-safety.index'))
            ->assertRedirect(route('security.lock.show'));
    }

    private function user(int $type): User
    {
        return User::factory()->create([
            'user_type' => $type,
            'privacy_terms_version' => (string) config('privacy.terms_version'),
            'privacy_terms_accepted_at' => now(),
        ]);
    }

    private function studentPatient(array $attributes = []): Patients
    {
        $student = $this->user(User::TYPE_STUDENT);

        return Patients::create(array_merge([
            'patient_type' => 1,
            'registry_type' => 'student',
            'privacy_regime' => 'ferpa_student',
            'edulvl_id' => null,
            'user_id' => $student->id,
            'school_id' => 'STU-'.$student->id,
            'bloodtype' => 'O+',
            'address' => 'Test address',
            'medical_condition' => 'None recorded',
            'medical_illness' => 'None recorded',
            'operations' => 'None recorded',
            'allergies' => 'Peanuts',
            'emergency_contact_name' => 'Test Guardian',
            'emergency_contact_number' => '09170000000',
            'emergency_relationship' => 'Parent',
        ], $attributes));
    }
}
