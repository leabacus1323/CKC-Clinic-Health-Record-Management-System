<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in proper order based on foreign key dependencies
        $this->call(AdminSeeder::class);
        $this->call(EducationalLevelSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(ImmunizationSeeder::class);
        $this->call(MedicineSeeder::class);
        $this->call(NurseBedSeeder::class);
        
        // Users must be created before doctors and patients
        $this->call(UserSeeder::class);
        $this->call(DoctorSeeder::class);
        $this->call(PatientSeeder::class);
        $this->call(AnnouncementSeeder::class);
        
        // Medical records depend on doctors and patients
        $this->call(AppointmentSeeder::class);
        $this->call(HealthRecordSeeder::class);
        $this->call(PhysicalExaminationSeeder::class);
        $this->call(DentalExaminationSeeder::class);
        $this->call(ImmunizationRecordsSeeder::class);
        $this->call(PrescriptionRecordSeeder::class);
        
        // Certificate types
        $this->call(CertificateTypeSeeder::class);
    }
}
