<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PhysicalExamination;
use Carbon\Carbon;

class PhysicalExaminationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Physical Examination Records for 5 patients
        PhysicalExamination::create([
            'patient_id' => 1,
            'doctor_id' => 1,
            'height' => '165 cm',
            'weight' => '58 kg',
            'heart' => 'Regular rate and rhythm, no murmurs',
            'lungs' => 'Clear to auscultation bilaterally',
            'eyes' => 'PERRLA, EOM intact',
            'ears' => 'Bilateral TMs intact, no effusion',
            'nose' => 'Nasal septum midline, no obstruction',
            'throat' => 'Pharynx clear, no exudate',
            'skin' => 'Warm, dry, no rashes or lesions',
            'bp' => '120/80 mmHg',
            'remarks' => 'Normal physical examination. Patient appears healthy with good nutritional status.',
        ]);

        PhysicalExamination::create([
            'patient_id' => 1,
            'doctor_id' => 1,
            'height' => '165 cm',
            'weight' => '58.5 kg',
            'heart' => 'Regular rate and rhythm, 72 bpm',
            'lungs' => 'Clear bilaterally, no wheezing',
            'eyes' => 'Clear, lenses clear',
            'ears' => 'Normal canal, intact TMs',
            'nose' => 'Patent, septum midline',
            'throat' => 'Pharynx clear, normal tonsils',
            'skin' => 'Normal color and texture',
            'bp' => '118/78 mmHg',
            'remarks' => 'Follow-up examination. No abnormalities noted. Advised to maintain exercise routine.',
        ]);

        PhysicalExamination::create([
            'patient_id' => 2,
            'doctor_id' => 2,
            'height' => '175 cm',
            'weight' => '72 kg',
            'heart' => 'Regular S1, S2, no murmurs',
            'lungs' => 'Bilateral air entry good',
            'eyes' => 'Visual acuity normal',
            'ears' => 'Hearing intact bilaterally',
            'nose' => 'Nasal passages clear',
            'throat' => 'Pharynx normal, no swelling',
            'skin' => 'Healthy appearance',
            'bp' => '122/82 mmHg',
            'remarks' => 'Good health status. BMI within normal range. Lungs clear on auscultation.',
        ]);

        PhysicalExamination::create([
            'patient_id' => 3,
            'doctor_id' => 1,
            'height' => '158 cm',
            'weight' => '55 kg',
            'heart' => 'Regular rhythm, 76 bpm',
            'lungs' => 'Clear throughout',
            'eyes' => 'Alert, responsive, PERRLA',
            'ears' => 'Canals patent, TMs intact',
            'nose' => 'Septum midline, no deviation',
            'throat' => 'No erythema, no exudate',
            'skin' => 'Good turgor, uniform color',
            'bp' => '116/76 mmHg',
            'remarks' => 'Physical examination within normal limits. Patient is alert and responsive.',
        ]);

        PhysicalExamination::create([
            'patient_id' => 3,
            'doctor_id' => 1,
            'height' => '158 cm',
            'weight' => '55.2 kg',
            'heart' => 'Strong, regular, no irregularities',
            'lungs' => 'Clear to percussion',
            'eyes' => 'Bright, reactive pupils',
            'ears' => 'TMs pearly, translucent',
            'nose' => 'Nasal airway patent',
            'throat' => 'Pink mucosa, no swollen lymph nodes',
            'skin' => 'Uniform color, no lesions',
            'bp' => '118/76 mmHg',
            'remarks' => 'Routine check-up. No complaints. Heart rate regular and strong.',
        ]);

        PhysicalExamination::create([
            'patient_id' => 4,
            'doctor_id' => 2,
            'height' => '172 cm',
            'weight' => '68 kg',
            'heart' => 'Regular, 78 bpm, systolic murmur noted',
            'lungs' => 'Clear bilaterally, no crackles',
            'eyes' => 'Normal',
            'ears' => 'Normal hearing',
            'nose' => 'Normal',
            'throat' => 'Normal pharynx',
            'skin' => 'Dry, slightly pale',
            'bp' => '124/84 mmHg',
            'remarks' => 'Slightly elevated blood pressure. Advised to monitor salt intake and stress levels.',
        ]);

        PhysicalExamination::create([
            'patient_id' => 5,
            'doctor_id' => 1,
            'height' => '168 cm',
            'weight' => '62 kg',
            'heart' => 'Regular rate and rhythm',
            'lungs' => 'Bilateral breath sounds normal',
            'eyes' => 'Normal development for age',
            'ears' => 'Normal, hearing intact',
            'nose' => 'Nasal patency normal',
            'throat' => 'Pharynx clear',
            'skin' => 'Clear, normal for adolescent',
            'bp' => '119/79 mmHg',
            'remarks' => 'Healthy adolescent. Normal growth and development. No medical concerns.',
        ]);

        PhysicalExamination::create([
            'patient_id' => 5,
            'doctor_id' => 2,
            'height' => '168 cm',
            'weight' => '62.3 kg',
            'heart' => 'Regular, no abnormalities',
            'lungs' => 'Clear, no adverse reactions noted',
            'eyes' => 'Clear, normal',
            'ears' => 'Normal',
            'nose' => 'Normal',
            'throat' => 'Normal',
            'skin' => 'No reactions to vaccination',
            'bp' => '120/80 mmHg',
            'remarks' => 'Follow-up examination post-vaccination. No adverse reactions. Feeling well.',
        ]);
    }
}
