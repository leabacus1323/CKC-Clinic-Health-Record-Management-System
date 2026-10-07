<?php

// User Contollers
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ImmunizationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\HealthRecordsController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DoctorsController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\ReportsController;
// Patient Controllers (Separated)
use App\Http\Controllers\PatientsAppointmentScheduler;
use App\Http\Controllers\PatientsViewPersonalHealthRecord;
use App\Http\Controllers\PatientsUpdatePersonalInformation;use App\Http\Controllers\PatientCertificateController;// Doctor Controllers
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\DoctorProfileController;
use Illuminate\Support\Facades\Auth;
// Staff Controllers
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\ImportController;
// Admin Controllers
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminAppointments;
use App\Http\Controllers\AdminUsers;
use App\Http\Controllers\AdminDoctor;
use App\Http\Controllers\AdminPatients;
use App\Http\Controllers\AdminMedicines;
use App\Http\Controllers\AdminImmunization;
use App\Http\Controllers\AdminReports;
use App\Http\Controllers\AdminDentalExamination;
use App\Http\Controllers\AdminPhysicalExamination;
use App\Http\Controllers\AdminPrescription;
use App\Http\Controllers\AdminPatientStatistics;
use App\Http\Controllers\EducationalLevelController;
use App\Http\Controllers\HealthPrivacyCenterController;
use App\Http\Controllers\GuardianAccessController;
use App\Http\Controllers\GuardianApprovalController;
use App\Http\Controllers\DataAuthorizationController;
use App\Http\Controllers\BreakGlassController;
use App\Http\Controllers\ImmunizationPortalController;
use App\Http\Controllers\ImmunizationRequirementController;
use App\Http\Controllers\AdminSystemController;
use App\Http\Controllers\AdminHealthAnalyticsController;
use App\Http\Controllers\TeacherSafetyController;
use App\Http\Controllers\ClinicalLifeSafetyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\InfrastructureLogController;
use App\Http\Controllers\NurseLogController;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'privacy.accepted'])->group(function () {
    // Generic dashboard - redirects to appropriate user dashboard
    Route::get('/dashboard', function () {
        $user = Auth::user();
        switch ($user->user_type) {
            case 0: // Admin
                return redirect()->route('admin.dashboard');
            case 1: // Student/Patient
            case 4: // Faculty/Patient
                return redirect()->route('patients.dashboard');
            case 2: // Staff
                return redirect()->route('staff.dashboard');
            case 5: // Guardian
                return redirect()->route('privacy-center.index');
            case 6: // Emergency Administrator / Principal
                return redirect()->route('break-glass.index');
            case 7: // Teacher
                return redirect()->route('teacher.safety.index');
            case 3: // Doctor
                $doctor = \App\Models\Doctors::where('user_id', $user->id)->first();
                return $doctor
                    ? redirect()->route('doctor.dashboard')
                    : redirect()->route('doctor.profile.show');
            default:
                return redirect()->route('patients.dashboard');
        }
    })->middleware(['auth', 'verified'])->name('dashboard');

    // Notification endpoints
    Route::get('/notifications', [NotificationsController::class, 'getNotifications'])->name('notifications.get');
    
    // Legacy clinical resources are restricted to clinic operators. Student,
    // guardian, faculty-patient, and principal accounts use their scoped portals.
    Route::middleware(['clinical.operator', 'sensitive.no-store', 'medical.audit'])->group(function () {
        // Keep only complete legacy resources. Health records, provider,
        // patient and report management use the role-specific portals below.
        Route::resource('/appointments', AppointmentController::class);
        Route::resource('/medicine', MedicineController::class);
        Route::resource('/immunization', ImmunizationController::class);
        Route::resource('/course', CourseController::class);
    });


    // Privacy, guardian consent, and immunization compliance center
    Route::prefix('privacy-center')->name('privacy-center.')->middleware(['sensitive.no-store', 'medical.audit'])->group(function () {
        Route::get('/', [HealthPrivacyCenterController::class, 'index'])->name('index');
        Route::get('/patients/{patient}', [HealthPrivacyCenterController::class, 'show'])->name('show');
        Route::post('/patients/{patient}/guardians', [GuardianAccessController::class, 'store'])->name('guardians.store');
        Route::post('/guardian-links/{guardianLink}/accept', [GuardianAccessController::class, 'accept'])->name('guardians.accept');
        Route::delete('/patients/{patient}/guardians/{guardianLink}', [GuardianAccessController::class, 'destroy'])->name('guardians.destroy');
        Route::post('/patients/{patient}/authorizations', [DataAuthorizationController::class, 'store'])->name('authorizations.store');
        Route::get('/patients/{patient}/authorizations/{authorization}', [DataAuthorizationController::class, 'show'])->name('authorizations.show');
        Route::patch('/patients/{patient}/authorizations/{authorization}/revoke', [DataAuthorizationController::class, 'revoke'])->name('authorizations.revoke');
        Route::get('/patients/{patient}/immunizations', [ImmunizationPortalController::class, 'index'])->name('immunizations.index');
        Route::post('/patients/{patient}/immunizations', [ImmunizationPortalController::class, 'store'])->name('immunizations.store');
        Route::get('/patients/{patient}/immunizations/{record}/download', [ImmunizationPortalController::class, 'download'])->name('immunizations.download');
    });

    Route::get('/immunization-review', [ImmunizationPortalController::class, 'reviewIndex'])->middleware(['clinical.operator', 'sensitive.no-store', 'medical.audit'])->name('immunizations.review.index');
    Route::patch('/immunization-review/{record}', [ImmunizationPortalController::class, 'review'])->middleware(['clinical.operator', 'sensitive.no-store', 'medical.audit'])->name('immunizations.review.update');

    // Patient-specific routes
    Route::prefix('patient')->name('patients.')->middleware(['auth', 'patient.self', 'ensure.patient.profile', 'sensitive.no-store', 'medical.audit'])->group(function () {
        // Preserve compatibility with old bookmarks and the previous patient
        // shell, which used /patient/ as the dashboard URL.
        Route::get('/', fn () => redirect()->route('patients.dashboard'))->name('index');

        // Dashboard is server-rendered so it appears immediately on desktop and without JavaScript.
        Route::get('/dashboard', [PatientsController::class, 'index'])->name('dashboard');

        // Tab routes - all return the shell view, content loaded via AJAX
        Route::get('/appointments', function() {
            return view('patients.shells.patient-shell');
        })->name('appointments');

        Route::get('/health-records', function() {
            return view('patients.shells.patient-shell');
        })->name('health.records');

        Route::get('/profile', function() {
            return view('patients.shells.patient-shell');
        })->name('profile');

        Route::get('/certificates', function() {
            return view('patients.shells.patient-shell');
        })->name('certificates.index');

        // Appointment Scheduling Routes (separate pages, not in shell)
        Route::get('/schedule-appointment', [PatientsAppointmentScheduler::class, 'scheduleAppointment'])->name('schedule.appointment');
        Route::post('/schedule-appointment', [PatientsAppointmentScheduler::class, 'storeAppointment'])->name('store.appointment');
        Route::patch('/appointments/{appointmentId}/cancel', [PatientsAppointmentScheduler::class, 'cancelAppointment'])->name('appointments.cancel');

        // Health Records Viewing Routes (separate pages, not in shell)
        Route::get('/health-records/detailed', [PatientsViewPersonalHealthRecord::class, 'detailedHealthRecords'])->name('health.records.detailed');
        Route::get('/immunization-records', [PatientsViewPersonalHealthRecord::class, 'immunizationRecords'])->name('immunization.records');
        Route::get('/prescription-history', [PatientsViewPersonalHealthRecord::class, 'prescriptionHistory'])->name('prescription.history');
        Route::get('/examination-history', [PatientsViewPersonalHealthRecord::class, 'examinationHistory'])->name('examination.history');
        Route::get('/health-summary', [PatientsViewPersonalHealthRecord::class, 'healthSummary'])->name('health.summary');

        // Certificate Request Routes (separate pages, not in shell)
        Route::get('/certificates/create', [PatientCertificateController::class, 'create'])->name('certificates.create');
        Route::post('/certificates', [PatientCertificateController::class, 'store'])->name('certificates.store');
        Route::get('/certificates/{id}', [PatientCertificateController::class, 'show'])->name('certificates.show');

        // AJAX Tab Content Routes (inside patient prefix, used by shell)
        Route::get('/ajax/dashboard', [PatientsController::class, 'ajaxDashboard'])->name('ajax.dashboard');
        Route::get('/ajax/appointments', [PatientsController::class, 'ajaxAppointments'])->name('ajax.appointments');
        Route::get('/ajax/health-records', [PatientsController::class, 'ajaxHealthRecords'])->name('ajax.health-records');
        Route::get('/ajax/profile', [PatientsController::class, 'ajaxProfile'])->name('ajax.profile');
        Route::get('/ajax/certificates', [PatientsController::class, 'ajaxCertificates'])->name('ajax.certificates');
    });

    // Profile creation/editing routes — no patient profile check (allows new users to create profile)
    Route::prefix('patient')->name('patients.')->middleware(['patient.self', 'sensitive.no-store', 'medical.audit'])->group(function () {
        Route::get('/profile/create', [PatientsUpdatePersonalInformation::class, 'create'])->name('profile.create');
        Route::post('/profile', [PatientsUpdatePersonalInformation::class, 'store'])->name('profile.store');
        Route::get('/profile/edit', [PatientsUpdatePersonalInformation::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [PatientsUpdatePersonalInformation::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [PatientsUpdatePersonalInformation::class, 'updatePassword'])->name('profile.password.update');
        Route::patch('/profile/basic-info', [PatientsUpdatePersonalInformation::class, 'updateBasicInfo'])->name('profile.basic.update');
        Route::patch('/profile/medical-info', [PatientsUpdatePersonalInformation::class, 'updateMedicalInfo'])->name('profile.medical.info');
        Route::patch('/profile/emergency-contact', [PatientsUpdatePersonalInformation::class, 'updateEmergencyContact'])->name('profile.emergency.contact');
    });
    Route::prefix('doctor')->name('doctor.')->middleware(['auth', 'check.user.type:' . User::TYPE_DOCTOR, 'clinical.operator', 'sensitive.no-store', 'medical.audit', 'infrastructure.audit'])->group(function () {
        // Server-render the dashboard to prevent an empty desktop shell while AJAX initializes.
        Route::get('/', [DoctorDashboardController::class, 'index'])->name('index');
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/appointments', [DoctorDashboardController::class, 'appointments'])->name('appointments');
        Route::patch('/appointments/{appointmentId}/status', [DoctorDashboardController::class, 'updateAppointmentStatus'])->name('appointments.update-status');
        Route::get('/patients', [DoctorDashboardController::class, 'patients'])->name('patients');
        Route::get('/patients/{patientId}', [DoctorDashboardController::class, 'viewPatient'])->name('patient-details');
        Route::put('/patients/{patientId}', [DoctorDashboardController::class, 'updatePatient'])->name('patients.update');
        Route::get('/health-records', [DoctorDashboardController::class, 'healthRecords'])->name('health-records');
        Route::get('/health-records/create', [DoctorDashboardController::class, 'createHealthRecord'])->name('health-records.create');
        Route::post('/health-records', [DoctorDashboardController::class, 'storeHealthRecord'])->name('health-records.store');
        Route::get('/health-records/{id}/edit', [DoctorDashboardController::class, 'editHealthRecord'])->name('health-records.edit');
        Route::put('/health-records/{id}', [DoctorDashboardController::class, 'updateHealthRecord'])->name('health-records.update');

        // Print Health Records
        Route::get('/print/health-record/{patientId}', [DoctorDashboardController::class, 'printHealthRecord'])->name('print.health-record');

        // Physical Examinations
        Route::get('/physical-exams/{id}/edit', [DoctorDashboardController::class, 'editPhysicalExam'])->name('physical-exams.edit');
        Route::put('/physical-exams/{id}', [DoctorDashboardController::class, 'updatePhysicalExam'])->name('physical-exams.update');

        // Dental Examinations
        Route::get('/dental-exams/{id}/edit', [DoctorDashboardController::class, 'editDentalExam'])->name('dental-exams.edit');
        Route::put('/dental-exams/{id}', [DoctorDashboardController::class, 'updateDentalExam'])->name('dental-exams.update');

        // Immunizations
        Route::get('/immunizations/{id}/edit', [DoctorDashboardController::class, 'editImmunization'])->name('immunizations.edit');
        Route::put('/immunizations/{id}', [DoctorDashboardController::class, 'updateImmunization'])->name('immunizations.update');

        // Prescriptions (old routes)
        Route::get('/prescriptions/{id}/edit', [DoctorDashboardController::class, 'editPrescription'])->name('prescriptions.edit');
        Route::put('/prescriptions/{id}', [DoctorDashboardController::class, 'updatePrescription'])->name('prescriptions.update');

        // Prescriptions Management (new routes)
        Route::get('/prescriptions', [DoctorDashboardController::class, 'prescriptions'])->name('prescriptions');
        Route::get('/prescriptions/create', [DoctorDashboardController::class, 'createPrescription'])->name('prescriptions.create');
        Route::post('/prescription', [DoctorDashboardController::class, 'storePrescription'])->name('prescription.store');
        Route::put('/prescription/{id}', [DoctorDashboardController::class, 'updatePrescriptionRecord'])->name('prescription.update');
        Route::put('/prescription/{id}/discontinue', [DoctorDashboardController::class, 'discontinuePrescription'])->name('prescription.discontinue');
        Route::get('/prescription/history/{patientId}', [DoctorDashboardController::class, 'prescriptionHistory'])->name('prescription.history');

        // Medicine
        Route::post('/medicine', [DoctorDashboardController::class, 'storeMedicine'])->name('medicines.store');
        Route::put('/medicine/{id}', [DoctorDashboardController::class, 'updateMedicine'])->name('medicine.update');

        Route::get('/medications', [DoctorDashboardController::class, 'medications'])->name('medications');
        Route::get('/reports', [DoctorDashboardController::class, 'reports'])->name('reports');

        // Custom Report Generation Routes
        Route::get('/reports/generate', [DoctorDashboardController::class, 'showReportGeneration'])->name('reports.generate');
        Route::post('/reports/generate', [DoctorDashboardController::class, 'generateReport'])->name('reports.store');
        Route::get('/reports/view/{id}', [DoctorDashboardController::class, 'viewReport'])->name('reports.view');
        Route::delete('/reports/{id}', [DoctorDashboardController::class, 'deleteReport'])->name('reports.delete');
        Route::get('/reports/export/{id}/{format}', [DoctorDashboardController::class, 'exportReport'])->name('reports.export');

        // Certificate Requests
        Route::get('/certificate-requests', [DoctorDashboardController::class, 'certificateRequests'])->name('certificate-requests');
        Route::get('/certificate-requests/{id}', [DoctorDashboardController::class, 'showCertificateRequest'])->name('certificate-requests.show');
        Route::post('/certificate-requests/{id}/approve', [DoctorDashboardController::class, 'approveCertificateRequest'])->name('certificate-requests.approve');
        Route::post('/certificate-requests/{id}/reject', [DoctorDashboardController::class, 'rejectCertificateRequest'])->name('certificate-requests.reject');
        Route::post('/certificate-requests/{id}/issue', [DoctorDashboardController::class, 'issueCertificateRequest'])->name('certificate-requests.issue');
        Route::get('/certificate-requests/{id}/print', [DoctorDashboardController::class, 'printCertificate'])->name('certificate-requests.print');
        
        // AJAX Tab Content Routes
        Route::get('/ajax/dashboard', [DoctorDashboardController::class, 'ajaxDashboard'])->name('ajax.dashboard');
        Route::get('/ajax/appointments', [DoctorDashboardController::class, 'ajaxAppointments'])->name('ajax.appointments');
        Route::get('/ajax/patients', [DoctorDashboardController::class, 'ajaxPatients'])->name('ajax.patients');
        Route::get('/ajax/health-records', [DoctorDashboardController::class, 'ajaxHealthRecords'])->name('ajax.health-records');
        Route::get('/ajax/medications', [DoctorDashboardController::class, 'ajaxMedications'])->name('ajax.medications');
        Route::get('/ajax/prescriptions', [DoctorDashboardController::class, 'ajaxPrescriptions'])->name('ajax.prescriptions');
        Route::get('/ajax/reports', [DoctorDashboardController::class, 'ajaxReports'])->name('ajax.reports');

        // Doctor Profile Routes
        Route::get('/profile', [DoctorProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [DoctorProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [DoctorProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [DoctorProfileController::class, 'updatePassword'])->name('profile.password.update');

        // Create Routes for full-page forms
        Route::get('/medicines/create', [DoctorDashboardController::class, 'createMedicine'])->name('medicines.create');
        Route::get('/prescriptions/create', [DoctorDashboardController::class, 'createPrescriptionRecord'])->name('prescriptions.create-page');
    });
    // End Doctor Routes

    // Staff Routes - for user_type = 2 (Faculty & Staff)
    // Staff have access to doctor-like functionalities for managing patients
    Route::prefix('staff')->name('staff.')->middleware(['auth', 'check.user.type:' . User::TYPE_STAFF, 'clinical.operator', 'sensitive.no-store', 'medical.audit', 'infrastructure.audit'])->group(function () {
        Route::get('/', [StaffDashboardController::class, 'index'])->name('index');
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
        Route::get('/appointments', [StaffDashboardController::class, 'appointments'])->name('appointments');
        Route::patch('/appointments/{appointmentId}/status', [StaffDashboardController::class, 'updateAppointmentStatus'])->name('appointments.update-status');
        Route::get('/nurse-logs', [NurseLogController::class, 'index'])->name('nurse-logs.index');
        Route::get('/nurse-logs/create', [NurseLogController::class, 'create'])->name('nurse-logs.create');
        Route::post('/nurse-logs', [NurseLogController::class, 'store'])->name('nurse-logs.store');
        Route::patch('/nurse-logs/{nurseLog}/checkout', [NurseLogController::class, 'checkout'])->name('nurse-logs.checkout');
        Route::get('/patients', [StaffDashboardController::class, 'patients'])->name('patients');
        Route::get('/patients/create', [StaffDashboardController::class, 'createPatient'])->name('patients.create');
        Route::post('/patients', [StaffDashboardController::class, 'storePatient'])->name('patients.store');
        Route::get('/patients/{patientId}', [StaffDashboardController::class, 'viewPatient'])->name('patient-details');
        Route::put('/patients/{patientId}', [StaffDashboardController::class, 'updatePatient'])->name('patients.update');
        Route::get('/health-records', [StaffDashboardController::class, 'healthRecords'])->name('health-records');
        Route::get('/health-records/create', [StaffDashboardController::class, 'createHealthRecord'])->name('health-records.create');
        Route::post('/health-records', [StaffDashboardController::class, 'storeHealthRecord'])->name('health-records.store');
        Route::get('/health-records/{id}/edit', [StaffDashboardController::class, 'editHealthRecord'])->name('health-records.edit');
        Route::put('/health-records/{id}', [StaffDashboardController::class, 'updateHealthRecord'])->name('health-records.update');

        // Print Health Records
        Route::get('/print/health-record/{patientId}', [StaffDashboardController::class, 'printHealthRecord'])->name('print.health-record');

        // Physical Examinations
        Route::get('/physical-exams/{id}/edit', [StaffDashboardController::class, 'editPhysicalExam'])->name('physical-exams.edit');
        Route::put('/physical-exams/{id}', [StaffDashboardController::class, 'updatePhysicalExam'])->name('physical-exams.update');

        // Dental Examinations
        Route::get('/dental-exams/{id}/edit', [StaffDashboardController::class, 'editDentalExam'])->name('dental-exams.edit');
        Route::put('/dental-exams/{id}', [StaffDashboardController::class, 'updateDentalExam'])->name('dental-exams.update');

        // Immunization Records
        Route::get('/immunizations/{id}/edit', [StaffDashboardController::class, 'editImmunization'])->name('immunizations.edit');
        Route::put('/immunizations/{id}', [StaffDashboardController::class, 'updateImmunization'])->name('immunizations.update');

        // Medicines
        Route::get('/medications', [StaffDashboardController::class, 'medications'])->name('medications');
        Route::get('/medicines/{id}/edit', [StaffDashboardController::class, 'editMedicine'])->name('medicines.edit');
        Route::post('/medicine', [StaffDashboardController::class, 'storeMedicine'])->name('medicines.store');
        Route::put('/medicine/{id}', [StaffDashboardController::class, 'updateMedicine'])->name('medicines.update');
        Route::delete('/medicine/{id}', [StaffDashboardController::class, 'deleteMedicine'])->name('medicines.delete');

        // Staff Prescriptions Management
        Route::get('/prescriptions', [StaffDashboardController::class, 'prescriptions'])->name('prescriptions');
        Route::post('/prescription', [StaffDashboardController::class, 'storePrescription'])->name('prescription.store');
        Route::put('/prescription/{id}', [StaffDashboardController::class, 'updatePrescriptionRecord'])->name('prescription.update');
        Route::put('/prescription/{id}/discontinue', [StaffDashboardController::class, 'discontinuePrescription'])->name('prescription.discontinue');
        Route::get('/prescription/history/{patientId}', [StaffDashboardController::class, 'prescriptionHistory'])->name('prescription.history');

        // Reports & Statistics
        Route::get('/reports', [StaffDashboardController::class, 'reports'])->name('reports');
        Route::get('/reports/generate', [StaffDashboardController::class, 'showReportGeneration'])->name('reports.generate');
        Route::post('/reports/generate', [StaffDashboardController::class, 'generateReport'])->name('reports.store');
        Route::get('/reports/view/{id}', [StaffDashboardController::class, 'viewReport'])->name('reports.view');
        Route::delete('/reports/{id}', [StaffDashboardController::class, 'deleteReport'])->name('reports.delete');
        Route::get('/reports/export/{id}/{format}', [StaffDashboardController::class, 'exportReport'])->name('reports.export');

        // AJAX Tab Content Routes
        Route::get('/ajax/dashboard', [StaffDashboardController::class, 'ajaxDashboard'])->name('ajax.dashboard');
        Route::get('/ajax/appointments', [StaffDashboardController::class, 'ajaxAppointments'])->name('ajax.appointments');
        Route::get('/ajax/nurse-logs', [StaffDashboardController::class, 'ajaxNurseLogs'])->name('ajax.nurse-logs');
        Route::get('/ajax/patients', [StaffDashboardController::class, 'ajaxPatients'])->name('ajax.patients');
        Route::get('/ajax/health-records', [StaffDashboardController::class, 'ajaxHealthRecords'])->name('ajax.health-records');
        Route::get('/ajax/medicines', [StaffDashboardController::class, 'ajaxMedicines'])->name('ajax.medicines');
        Route::get('/ajax/prescriptions', [StaffDashboardController::class, 'ajaxPrescriptions'])->name('ajax.prescriptions');
        Route::get('/ajax/reports', [StaffDashboardController::class, 'ajaxReports'])->name('ajax.reports');
        Route::get('/ajax/statistics', [StaffDashboardController::class, 'ajaxStatistics'])->name('ajax.statistics');
        Route::get('/ajax/import', [StaffDashboardController::class, 'ajaxImport'])->name('ajax.import');

        // Statistics Routes (detail pages)
        Route::get('/statistics/course/{id}', [StaffDashboardController::class, 'patientsByCourse'])->name('statistics.course');
        Route::get('/statistics/level/{id}', [StaffDashboardController::class, 'patientsByLevel'])->name('statistics.level');

        // Import routes (direct processing, not tab-based)
        Route::post('/import/process/users', [ImportController::class, 'processUsers'])->name('import.process.users');
        Route::post('/import/process/medicines', [ImportController::class, 'processMedicines'])->name('import.process.medicines');
        Route::get('/import/sample/users', [ImportController::class, 'downloadUsersSample'])->name('import.sample.users');
        Route::get('/import/sample/medicines', [ImportController::class, 'downloadMedicinesSample'])->name('import.sample.medicines');

        // Create Routes for full-page forms
        Route::get('/medicines/create', [StaffDashboardController::class, 'createMedicine'])->name('medicines.create');
        Route::get('/prescriptions/create', [StaffDashboardController::class, 'createPrescription'])->name('prescriptions.create-page');

        // Certificate Requests
        Route::get('/certificate-requests', [StaffDashboardController::class, 'certificateRequests'])->name('certificate-requests');
        Route::get('/certificate-requests/{id}', [StaffDashboardController::class, 'showCertificateRequest'])->name('certificate-requests.show');
        Route::post('/certificate-requests/{id}/approve', [StaffDashboardController::class, 'approveCertificateRequest'])->name('certificate-requests.approve');
        Route::post('/certificate-requests/{id}/reject', [StaffDashboardController::class, 'rejectCertificateRequest'])->name('certificate-requests.reject');
        Route::post('/certificate-requests/{id}/issue', [StaffDashboardController::class, 'issueCertificateRequest'])->name('certificate-requests.issue');
        Route::get('/certificate-requests/{id}/print', [StaffDashboardController::class, 'printCertificate'])->name('certificate-requests.print');

        // Staff operational activity log. Staff can see only their own sanitized events.
        Route::get('/infrastructure-logs', [InfrastructureLogController::class, 'staffIndex'])->name('infrastructure-logs.index');

        // Staff Announcements / Reminders
        Route::get('/announcements', [StaffDashboardController::class, 'announcements'])->name('announcements');
        Route::get('/announcements/create', [StaffDashboardController::class, 'createAnnouncement'])->name('announcements.create');
        Route::post('/announcements', [StaffDashboardController::class, 'storeAnnouncement'])->name('announcements.store');
    });

    Route::prefix('teacher')->name('teacher.')->middleware(['teacher.safety', 'sensitive.no-store', 'medical.audit'])->group(function () {
        Route::get('/safety', [TeacherSafetyController::class, 'index'])->name('safety.index');
        Route::get('/safety/{patient}', [TeacherSafetyController::class, 'show'])->name('safety.show');
    });

    Route::prefix('clinical/life-safety')->name('clinical.life-safety.')->middleware(['clinical.operator', 'sensitive.no-store', 'medical.audit'])->group(function () {
        Route::get('/', [ClinicalLifeSafetyController::class, 'index'])->name('index');
        Route::get('/{patient}/edit', [ClinicalLifeSafetyController::class, 'edit'])->name('edit');
        Route::put('/{patient}', [ClinicalLifeSafetyController::class, 'update'])->name('update');
    });

});


Route::middleware(['auth', 'privacy.accepted', 'emergency.admin', 'sensitive.no-store', 'medical.audit'])->group(function () {
    Route::get('/admin/emergency-access', [BreakGlassController::class, 'index'])->name('break-glass.index');
    Route::get('/admin/emergency-access/{patient}/activate', [BreakGlassController::class, 'create'])->name('break-glass.create');
    Route::post('/admin/emergency-access/{patient}', [BreakGlassController::class, 'store'])->middleware('throttle:5,1')->name('break-glass.store');
    Route::get('/admin/emergency-access/{patient}/view', [BreakGlassController::class, 'show'])->name('break-glass.show');
    Route::get('/admin/access-audit', [BreakGlassController::class, 'audit'])->name('break-glass.audit');
});

Route::middleware(['auth', 'privacy.accepted', 'system.admin', 'infrastructure.audit'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/system', [AdminSystemController::class, 'index'])->name('admin.system.index');
    Route::get('/admin/analytics', [AdminHealthAnalyticsController::class, 'index'])->middleware('sensitive.no-store')->name('admin.analytics.index');
    Route::post('/admin/analytics/generate', [AdminHealthAnalyticsController::class, 'generate'])->middleware('sensitive.no-store')->name('admin.analytics.generate');
    Route::get('/admin/analytics/export', [AdminHealthAnalyticsController::class, 'export'])->middleware('sensitive.no-store')->name('admin.analytics.export');

    Route::get('/admin/users/edit/{user}/{type}', [AdminUsers::class, 'edit'])->name('admin.users.editWithType');
    Route::put('/admin/users/edit/{user}/{type}', [AdminUsers::class, 'update'])->name('admin.users.updateWithType');
    Route::delete('/admin/users/destroy/{user}/{type}', [AdminUsers::class, 'deleteWithType'])->name('admin.users.deleteWithType');
    Route::resource('/admin/users', AdminUsers::class)->names([
        'index' => 'admin.users.index', 'create' => 'admin.users.create',
        'store' => 'admin.users.store', 'show' => 'admin.users.show',
        'edit' => 'admin.users.edit', 'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);

    Route::resource('/admin/doctors', AdminDoctor::class)
        ->except(['show'])
        ->names([
            'index' => 'admin.doctors.index', 'create' => 'admin.doctors.create',
            'store' => 'admin.doctors.store', 'edit' => 'admin.doctors.edit',
            'update' => 'admin.doctors.update', 'destroy' => 'admin.doctors.destroy',
        ]);

    Route::get('/admin/import/users', [ImportController::class, 'users'])->name('admin.import.users');
    Route::post('/admin/import/process/users', [ImportController::class, 'processUsers'])->name('admin.import.process.users');
    Route::get('/admin/import/sample/users', [ImportController::class, 'downloadUsersSample'])->name('admin.import.sample.users');
    Route::get('/admin/import', [ImportController::class, 'users'])->name('admin.import.index');

    Route::post('/admin/educational-levels', [EducationalLevelController::class, 'store'])->name('admin.educational-levels.store');
    Route::get('/admin/guardian-approvals', [GuardianApprovalController::class, 'index'])->middleware('sensitive.no-store')->name('guardian-approvals.index');
    Route::patch('/admin/guardian-approvals/{guardianLink}', [GuardianApprovalController::class, 'update'])->middleware('sensitive.no-store')->name('guardian-approvals.update');
    Route::get('/admin/immunization-requirements', [ImmunizationRequirementController::class, 'index'])->name('immunization-requirements.index');
    Route::post('/admin/immunization-requirements', [ImmunizationRequirementController::class, 'store'])->name('immunization-requirements.store');
    Route::put('/admin/immunization-requirements/{requirement}', [ImmunizationRequirementController::class, 'update'])->name('immunization-requirements.update');
});

// Legacy clinical URLs remain available only to school nurses and doctors.
// System administrators receive no route to patient medical text fields.
Route::middleware(['auth', 'privacy.accepted', 'clinical.operator', 'sensitive.no-store', 'medical.audit'])->group(function () {
    Route::resource('/admin/appointments', AdminAppointments::class)
        ->except(['show'])
        ->names([
            'index' => 'admin.appointments.index', 'create' => 'admin.appointments.create',
            'store' => 'admin.appointments.store', 'edit' => 'admin.appointments.edit',
            'update' => 'admin.appointments.update', 'destroy' => 'admin.appointments.destroy',
        ]);
    Route::get('/admin/medicines/export', [AdminMedicines::class, 'export'])->name('admin.medicines.export');
    Route::resource('/admin/medicines', AdminMedicines::class)
        ->except(['show'])
        ->names([
            'index' => 'admin.medicines.index', 'create' => 'admin.medicines.create',
            'store' => 'admin.medicines.store', 'edit' => 'admin.medicines.edit',
            'update' => 'admin.medicines.update', 'destroy' => 'admin.medicines.destroy',
        ]);
    Route::get('/admin/import/medicines', [ImportController::class, 'medicines'])->name('admin.import.medicines');
    Route::post('/admin/import/process/medicines', [ImportController::class, 'processMedicines'])->name('admin.import.process.medicines');
    Route::get('/admin/import/sample/medicines', [ImportController::class, 'downloadMedicinesSample'])->name('admin.import.sample.medicines');
    Route::get('/admin/patients/export', [AdminPatients::class, 'export'])->name('admin.patients.export');
    Route::resource('/admin/patients', AdminPatients::class)
        ->except(['show'])
        ->names([
            'index' => 'admin.patients.index', 'create' => 'admin.patients.create',
            'store' => 'admin.patients.store', 'edit' => 'admin.patients.edit',
            'update' => 'admin.patients.update', 'destroy' => 'admin.patients.destroy',
        ]);
    Route::resource('/admin/immunization', AdminImmunization::class)
        ->only(['index', 'edit', 'update'])
        ->names([
            'index' => 'admin.immunization.index', 'edit' => 'admin.immunization.edit',
            'update' => 'admin.immunization.update',
        ]);
    Route::resource('/admin/dental', AdminDentalExamination::class)
        ->only(['index', 'edit', 'update'])
        ->names([
            'index' => 'admin.dental.index', 'edit' => 'admin.dental.edit',
            'update' => 'admin.dental.update',
        ]);
    Route::resource('/admin/physical', AdminPhysicalExamination::class)
        ->only(['index', 'edit', 'update'])
        ->names([
            'index' => 'admin.physical.index', 'edit' => 'admin.physical.edit',
            'update' => 'admin.physical.update',
        ]);
    Route::get('/admin/prescription/export', [AdminPrescription::class, 'export'])->name('admin.prescription.export');
    Route::resource('/admin/prescription', AdminPrescription::class)
        ->except(['show'])
        ->names([
            'index' => 'admin.prescription.index', 'create' => 'admin.prescription.create',
            'store' => 'admin.prescription.store', 'edit' => 'admin.prescription.edit',
            'update' => 'admin.prescription.update', 'destroy' => 'admin.prescription.destroy',
        ]);
    Route::get('/admin/reports/generate', [AdminReports::class, 'showReportGeneration'])->name('admin.reports.generate');
    Route::post('/admin/reports/generate', [AdminReports::class, 'generateReport'])->name('admin.reports.store');
    Route::get('/admin/reports/view/{id}', [AdminReports::class, 'viewReport'])->name('admin.reports.view');
    Route::delete('/admin/reports/{id}', [AdminReports::class, 'deleteReport'])->name('admin.reports.delete');
    Route::get('/admin/reports/export/{id}/{format}', [AdminReports::class, 'exportReport'])->name('admin.reports.export');
    Route::get('/admin/statistics/course/{id}', [AdminPatientStatistics::class, 'byCourse'])->name('admin.statistics.course');
    Route::get('/admin/statistics/level/{id}', [AdminPatientStatistics::class, 'byEducationalLevel'])->name('admin.statistics.level');
});

require __DIR__.'/auth.php';


