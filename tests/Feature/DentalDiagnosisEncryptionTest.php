<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\DentalExamination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DentalDiagnosisEncryptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_diagnosis_is_encrypted_on_save()
    {
        // Create minimal related records if foreign keys are enforced in migrations
        // We'll insert directly since factories may not exist in this repo.
        $patientId = DB::table('patients')->insertGetId(['f_name' => 'Test', 'm_name' => '', 'l_name' => 'Patient']);
        $doctorId = DB::table('doctors')->insertGetId(['user_id' => 9999, 'specialization' => 'dentist']);

        $exam = DentalExamination::create([
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'teeth_status' => [],
            'diagnosis' => 'Extraction needed',
        ]);

        $dbVal = DB::table('dental_examinations')->where('id', $exam->id)->value('diagnosis');
        $this->assertNotEquals('Extraction needed', $dbVal);
        $this->assertEquals('Extraction needed', $exam->diagnosis);

        $expectedHash = hash('sha256', Str::lower(trim('Extraction needed')));
        $this->assertEquals($expectedHash, DB::table('dental_examinations')->where('id', $exam->id)->value('diagnosis_search'));
    }

    public function test_reports_group_counts_use_decrypted_values()
    {
        $patientId = DB::table('patients')->insertGetId(['f_name' => 'A', 'm_name' => '', 'l_name' => 'One']);
        $doctorId = DB::table('doctors')->insertGetId(['user_id' => 9998, 'specialization' => 'dentist']);

        DentalExamination::create(['patient_id' => $patientId, 'doctor_id' => $doctorId, 'teeth_status' => [], 'diagnosis' => 'Cavity']);
        DentalExamination::create(['patient_id' => $patientId, 'doctor_id' => $doctorId, 'teeth_status' => [], 'diagnosis' => 'Cavity']);
        DentalExamination::create(['patient_id' => $patientId, 'doctor_id' => $doctorId, 'teeth_status' => [], 'diagnosis' => 'Filling']);

        $counts = DentalExamination::all()->pluck('diagnosis')->map(function ($d) { return trim($d); })->filter()->countBy();

        $this->assertEquals(2, $counts['Cavity']);
        $this->assertEquals(1, $counts['Filling']);
    }
}
