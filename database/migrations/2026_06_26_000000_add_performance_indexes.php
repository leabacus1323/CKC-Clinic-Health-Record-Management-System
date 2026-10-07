<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->index(['user_type', 'id'], 'users_type_id_idx'));
        Schema::table('patients', function (Blueprint $table): void {
            $table->index(['patient_type', 'id'], 'patients_type_id_idx');
            $table->index(['course_id', 'id'], 'patients_course_id_idx');
            $table->index(['edulvl_id', 'id'], 'patients_level_id_idx');
            $table->index(['user_id', 'patient_type'], 'patients_user_type_idx');
        });
        Schema::table('appointments', function (Blueprint $table): void {
            $table->index(['doc_id', 'date', 'time'], 'appointments_doctor_date_time_idx');
            $table->index(['doc_id', 'status', 'date'], 'appointments_doctor_status_date_idx');
            $table->index(['patient_id', 'status', 'date'], 'appointments_patient_status_date_idx');
            $table->index(['status', 'date'], 'appointments_status_date_idx');
        });
        Schema::table('health_records', function (Blueprint $table): void {
            $table->index(['patient_id', 'created_at'], 'health_records_patient_created_idx');
            $table->index(['user_id', 'created_at'], 'health_records_user_created_idx');
            $table->index('date_recorded', 'health_records_date_recorded_idx');
        });
        Schema::table('physical_examinations', function (Blueprint $table): void {
            $table->index(['patient_id', 'created_at'], 'physical_exams_patient_created_idx');
            $table->index(['doctor_id', 'created_at'], 'physical_exams_doctor_created_idx');
        });
        Schema::table('dental_examinations', function (Blueprint $table): void {
            $table->index(['patient_id', 'created_at'], 'dental_exams_patient_created_idx');
            $table->index(['doctor_id', 'created_at'], 'dental_exams_doctor_created_idx');
            $table->index('diagnosis_search', 'dental_exams_diagnosis_search_idx');
        });
        Schema::table('immunization_records', function (Blueprint $table): void {
            $table->index(['patient_id', 'created_at'], 'immunizations_patient_created_idx');
            $table->index('expiration_date', 'immunizations_expiration_idx');
        });
        Schema::table('prescription_records', function (Blueprint $table): void {
            $table->index(['patient_id', 'status', 'created_at'], 'prescriptions_patient_status_created_idx');
            $table->index(['doctor_id', 'created_at'], 'prescriptions_doctor_created_idx');
            $table->index(['medicine_id', 'status'], 'prescriptions_medicine_status_idx');
            $table->index('date_prescribed', 'prescriptions_date_prescribed_idx');
        });
        Schema::table('medicine', function (Blueprint $table): void {
            $table->index('name', 'medicine_name_idx');
            $table->index(['status', 'expiration_date'], 'medicine_status_expiration_idx');
        });
        Schema::table('generated_reports', function (Blueprint $table): void {
            $table->index(['generated_by_type', 'generated_by_id', 'created_at'], 'reports_generator_created_idx');
            $table->index(['report_type', 'created_at'], 'reports_type_created_idx');
        });
        Schema::table('announcements', fn (Blueprint $table) => $table->index(['active', 'created_at'], 'announcements_active_created_idx'));
    }

    public function down(): void
    {
        Schema::table('announcements', fn (Blueprint $table) => $table->dropIndex('announcements_active_created_idx'));
        Schema::table('generated_reports', function (Blueprint $table): void {
            $table->dropIndex('reports_generator_created_idx');
            $table->dropIndex('reports_type_created_idx');
        });
        Schema::table('medicine', function (Blueprint $table): void {
            $table->dropIndex('medicine_name_idx');
            $table->dropIndex('medicine_status_expiration_idx');
        });
        Schema::table('prescription_records', function (Blueprint $table): void {
            $table->dropIndex('prescriptions_patient_status_created_idx');
            $table->dropIndex('prescriptions_doctor_created_idx');
            $table->dropIndex('prescriptions_medicine_status_idx');
            $table->dropIndex('prescriptions_date_prescribed_idx');
        });
        Schema::table('immunization_records', function (Blueprint $table): void {
            $table->dropIndex('immunizations_patient_created_idx');
            $table->dropIndex('immunizations_expiration_idx');
        });
        Schema::table('dental_examinations', function (Blueprint $table): void {
            $table->dropIndex('dental_exams_patient_created_idx');
            $table->dropIndex('dental_exams_doctor_created_idx');
            $table->dropIndex('dental_exams_diagnosis_search_idx');
        });
        Schema::table('physical_examinations', function (Blueprint $table): void {
            $table->dropIndex('physical_exams_patient_created_idx');
            $table->dropIndex('physical_exams_doctor_created_idx');
        });
        Schema::table('health_records', function (Blueprint $table): void {
            $table->dropIndex('health_records_patient_created_idx');
            $table->dropIndex('health_records_user_created_idx');
            $table->dropIndex('health_records_date_recorded_idx');
        });
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropIndex('appointments_doctor_date_time_idx');
            $table->dropIndex('appointments_doctor_status_date_idx');
            $table->dropIndex('appointments_patient_status_date_idx');
            $table->dropIndex('appointments_status_date_idx');
        });
        Schema::table('patients', function (Blueprint $table): void {
            $table->dropIndex('patients_type_id_idx');
            $table->dropIndex('patients_course_id_idx');
            $table->dropIndex('patients_level_id_idx');
            $table->dropIndex('patients_user_type_idx');
        });
        Schema::table('users', fn (Blueprint $table) => $table->dropIndex('users_type_id_idx'));
    }
};
