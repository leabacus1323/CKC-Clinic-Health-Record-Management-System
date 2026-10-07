<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Record - {{ $patient->user->name ?? 'Patient' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            padding: 4px 8px;
            background-color: #f3f4f6;
            border: 1px solid #ddd;
        }

        .info-value {
            display: table-cell;
            width: 65%;
            padding: 4px 8px;
            border: 1px solid #ddd;
        }

        .two-column {
            display: table;
            width: 100%;
        }

        .two-column .col {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }

        .col:first-child {
            margin-right: 4%;
        }

        .record-card {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .record-header {
            font-weight: bold;
            color: #1e40af;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-health { background-color: #dbeafe; color: #1e40af; }
        .badge-physical { background-color: #d1fae5; color: #059669; }
        .badge-dental { background-color: #ede9fe; color: #7c3aed; }
        .badge-immunization { background-color: #fef3c7; color: #d97706; }

        .vitals-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .vital-item {
            text-align: center;
            padding: 8px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .vital-value {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
        }

        .vital-label {
            font-size: 10px;
            color: #666;
        }

        .teeth-chart {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 3px;
            margin-top: 10px;
        }

        .tooth {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #333;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .tooth-healthy { background-color: #22c55e; color: white; }
        .tooth-cavity { background-color: #ef4444; color: white; }
        .tooth-filled { background-color: #3b82f6; color: white; }
        .tooth-missing { background-color: #9ca3af; color: white; }
        .tooth-other { background-color: #f59e0b; color: white; }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }

        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 45%;
            text-align: center;
            vertical-align: bottom;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }

        .print-date {
            text-align: right;
            font-size: 10px;
            color: #666;
            margin-bottom: 10px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .section {
                page-break-inside: avoid;
            }
        }

        .back-button {
            background-color: #1e40af;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #1e3a8a;
        }
    </style>
</head>
<body>
    <!-- Print Header -->
    <div class="print-date">
        Generated: {{ now()->format('M j, Y g:i A') }}
    </div>

    <!-- Header -->
    <div class="header">
        <h1>COLLEGE CLINIC HEALTH SERVICES</h1>
        <h2>Medical Health Record</h2>
        <p>Christ the King College- Gingoog City</p>
    </div>

    <!-- Patient Information -->
    <div class="section">
        <div class="section-title">PATIENT INFORMATION</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Name</div>
                <div class="info-value">{{ $patient->user->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Patient ID</div>
                <div class="info-value">{{ $patient->id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Patient Type</div>
                <div class="info-value">
                    @if($patient->patient_type == 1) Student
                    @elseif($patient->patient_type == 3) Faculty
                    @elseif($patient->patient_type == 2) Staff
                    @else Other
                    @endif
                </div>
            </div>
            @if($patient->school_id)
            <div class="info-row">
                <div class="info-label">School ID</div>
                <div class="info-value">{{ $patient->school_id }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $patient->user->email ?? 'N/A' }}</div>
            </div>
            @if($patient->user->contact_number)
            <div class="info-row">
                <div class="info-label">Contact Number</div>
                <div class="info-value">{{ $patient->user->contact_number }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Blood Type</div>
                <div class="info-value">{{ $patient->blood_type ?? 'Not Specified' }}</div>
            </div>
            @if($patient->allergies)
            <div class="info-row">
                <div class="info-label">Allergies</div>
                <div class="info-value" style="color: #dc2626;">{{ $patient->allergies }}</div>
            </div>
            @endif
            @if($patient->medical_condition)
            <div class="info-row">
                <div class="info-label">Medical Conditions</div>
                <div class="info-value">{{ $patient->medical_condition }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Health Records -->
    @if(isset($healthRecords) && $healthRecords->count() > 0)
    <div class="section">
        <div class="section-title">GENERAL HEALTH RECORDS <span class="badge badge-health">General</span></div>
        @foreach($healthRecords as $record)
        <div class="record-card">
            <div class="record-header">
                Record #{{ $record->id }} - {{ $record->created_at->format('M j, Y') }}
            </div>
            <div class="info-grid">
                @if($record->chief_complaint)
                <div class="info-row">
                    <div class="info-label">Chief Complaint</div>
                    <div class="info-value">{{ $record->chief_complaint }}</div>
                </div>
                @endif
                @if($record->diagnosis)
                <div class="info-row">
                    <div class="info-label">Diagnosis</div>
                    <div class="info-value">{{ $record->diagnosis }}</div>
                </div>
                @endif
                @if($record->treatment)
                <div class="info-row">
                    <div class="info-label">Treatment</div>
                    <div class="info-value">{{ $record->treatment }}</div>
                </div>
                @endif
                @if($record->prescription)
                <div class="info-row">
                    <div class="info-label">Prescription</div>
                    <div class="info-value">{{ $record->prescription }}</div>
                </div>
                @endif
                @if($record->vital_signs)
                <div class="info-row">
                    <div class="info-label">Vital Signs</div>
                    <div class="info-value">{{ $record->vital_signs }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Attending Staff</div>
                    <div class="info-value">{{ $record->staff_name ?? 'Clinic Staff' }}</div>
                </div>
            </div>
            @if($record->notes)
            <div style="margin-top: 8px; padding: 8px; background-color: #f9fafb; border-left: 3px solid #1e40af;">
                <strong>Notes:</strong> {{ $record->notes }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Physical Examinations -->
    @if(isset($physicalExaminations) && $physicalExaminations->count() > 0)
    <div class="section">
        <div class="section-title">PHYSICAL EXAMINATIONS <span class="badge badge-physical">Physical</span></div>
        @foreach($physicalExaminations as $exam)
        <div class="record-card">
            <div class="record-header">
                Examination #{{ $exam->id }} - {{ $exam->created_at->format('M j, Y') }}
            </div>

            @if($exam->height || $exam->weight || $exam->blood_pressure || $exam->temperature)
            <div class="vitals-grid">
                @if($exam->height)
                <div class="vital-item">
                    <div class="vital-value">{{ $exam->height }}</div>
                    <div class="vital-label">Height (cm)</div>
                </div>
                @endif
                @if($exam->weight)
                <div class="vital-item">
                    <div class="vital-value">{{ $exam->weight }}</div>
                    <div class="vital-label">Weight (kg)</div>
                </div>
                @endif
                @if($exam->blood_pressure)
                <div class="vital-item">
                    <div class="vital-value">{{ $exam->blood_pressure }}</div>
                    <div class="vital-label">BP (mmHg)</div>
                </div>
                @endif
                @if($exam->temperature)
                <div class="vital-item">
                    <div class="vital-value">{{ $exam->temperature }}</div>
                    <div class="vital-label">Temp (°C)</div>
                </div>
                @endif
            </div>
            @endif

            <div class="info-grid" style="margin-top: 10px;">
                @if($exam->visual_acuity_left)
                <div class="info-row">
                    <div class="info-label">Visual Acuity (L)</div>
                    <div class="info-value">{{ $exam->visual_acuity_left }}</div>
                </div>
                @endif
                @if($exam->visual_acuity_right)
                <div class="info-row">
                    <div class="info-label">Visual Acuity (R)</div>
                    <div class="info-value">{{ $exam->visual_acuity_right }}</div>
                </div>
                @endif
                @if($exam->hearing)
                <div class="info-row">
                    <div class="info-label">Hearing</div>
                    <div class="info-value">{{ $exam->hearing }}</div>
                </div>
                @endif
                @if($exam->remarks)
                <div class="info-row">
                    <div class="info-label">Remarks</div>
                    <div class="info-value">{{ $exam->remarks }}</div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Dental Examinations -->
    @if(isset($dentalExaminations) && $dentalExaminations->count() > 0)
    <div class="section">
        <div class="section-title">DENTAL EXAMINATIONS <span class="badge badge-dental">Dental</span></div>
        @foreach($dentalExaminations as $dental)
        <div class="record-card">
            <div class="record-header">
                Dental Exam #{{ $dental->id }} - {{ $dental->created_at->format('M j, Y') }}
            </div>
            <div class="info-grid">
                @if($dental->teeth_condition)
                <div class="info-row">
                    <div class="info-label">Teeth Condition</div>
                    <div class="info-value">{{ $dental->teeth_condition }}</div>
                </div>
                @endif
                @if($dental->gums_condition)
                <div class="info-row">
                    <div class="info-label">Gums Condition</div>
                    <div class="info-value">{{ $dental->gums_condition }}</div>
                </div>
                @endif
                @if($dental->diagnosis)
                <div class="info-row">
                    <div class="info-label">Diagnosis</div>
                    <div class="info-value">{{ $dental->diagnosis }}</div>
                </div>
                @endif
                @if($dental->treatment)
                <div class="info-row">
                    <div class="info-label">Treatment</div>
                    <div class="info-value">{{ $dental->treatment }}</div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Immunization Records -->
    @if(isset($immunizationRecords) && $immunizationRecords->count() > 0)
    <div class="section">
        <div class="section-title">IMMUNIZATION RECORDS <span class="badge badge-immunization">Immunization</span></div>
        @foreach($immunizationRecords as $imm)
        <div class="record-card">
            <div class="record-header">
                Immunization #{{ $imm->id }} - {{ $imm->created_at->format('M j, Y') }}
            </div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Vaccine</div>
                    <div class="info-value">{{ $imm->vaccine_name ?? 'N/A' }}</div>
                </div>
                @if($imm->vaccine_type)
                <div class="info-row">
                    <div class="info-label">Vaccine Type</div>
                    <div class="info-value">{{ $imm->vaccine_type }}</div>
                </div>
                @endif
                @if($imm->dosage)
                <div class="info-row">
                    <div class="info-label">Dosage</div>
                    <div class="info-value">{{ $imm->dosage }}</div>
                </div>
                @endif
                @if($imm->site_of_administration)
                <div class="info-row">
                    <div class="info-label">Administration Site</div>
                    <div class="info-value">{{ $imm->site_of_administration }}</div>
                </div>
                @endif
                @if($imm->administered_by)
                <div class="info-row">
                    <div class="info-label">Administered By</div>
                    <div class="info-value">{{ $imm->administered_by }}</div>
                </div>
                @endif
                @if($imm->expiration_date)
                <div class="info-row">
                    <div class="info-label">Next Due Date</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($imm->expiration_date)->format('M j, Y') }}</div>
                </div>
                @endif
            </div>
            @if($imm->notes)
            <div style="margin-top: 8px; padding: 8px; background-color: #fef3c7; border-left: 3px solid #d97706;">
                <strong>Notes:</strong> {{ $imm->notes }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This is an official medical record from Christ the King College Clinic Health Services.</p>
        <p>For inquiries, please contact the College Clinic.</p>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Clinic Staff Signature<br>
                <small>Date: _______________</small>
            </div>
        </div>
        <div style="display: table-cell; width: 10%;"></div>
        <div class="signature-box">
            <div class="signature-line">
                Patient/Guardian Signature<br>
                <small>Date: _______________</small>
            </div>
        </div>
    </div>

    <!-- Print Button (hidden when printing) -->
    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" class="back-button">
            🖨️ Print This Record
        </button>
        <button onclick="window.close()" class="back-button" style="background-color: #6b7280;">
            ✕ Close
        </button>
    </div>
</body>
</html>
