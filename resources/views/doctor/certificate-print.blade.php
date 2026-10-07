@php
    $patient = $certificate->patient;
    $user = $patient->user;
    
    // Get certificate type name for title
    $certTypeName = $certificate->certificateType->name ?? 'Medical Certificate';
    
    // Format dates
    $issuedDate = $certificate->issued_date ? \Carbon\Carbon::parse($certificate->issued_date)->format('F d, Y') : date('F d, Y');
    $appointmentDate = $certificate->appointment ? \Carbon\Carbon::parse($certificate->appointment->date)->format('F d, Y') : 'N/A';
    
    // Get clinic info (you can customize these)
    $clinicName = 'CKC Clinic HRMS';
    $clinicAddress = 'Christ the King College Clinic - Health Services';
    $clinicContact = 'Clinic Contact Information';
    
    // Determine certificate type for specific content
    $certSlug = $certificate->certificateType->slug ?? '';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $certTypeName }} - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Georgia, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            background: #f0f0f0;
        }
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }
        
        .modal-content {
            position: relative;
            background: white;
            max-height: 95vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border-radius: 8px;
        }
        
        .print-area {
            width: 210mm;
            min-height: 297mm;
            padding: 40px;
            background: white;
        }
        
        .certificate {
            width: 100%;
            border: 3px double #2c5282;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2c5282;
        }
        
        .header h1 {
            font-size: 24pt;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14pt;
            font-weight: normal;
            color: #4a5568;
        }
        
        .header p {
            font-size: 10pt;
            color: #718096;
            margin-top: 5px;
        }
        
        .title {
            text-align: center;
            margin: 30px 0;
        }
        
        .title h3 {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #2c5282;
            border-bottom: 1px solid #2c5282;
            padding-bottom: 10px;
            display: inline-block;
        }
        
        .content {
            text-align: justify;
            margin: 30px 0;
        }
        
        .content p {
            margin-bottom: 15px;
        }
        
        .diagnosis-box {
            background: #fffaf0;
            border: 1px solid #ed8936;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .diagnosis-box h4 {
            color: #c05621;
            margin-bottom: 10px;
        }
        
        .certification {
            text-align: justify;
            margin: 30px 0;
            padding: 20px;
            background: #f0fff4;
            border: 1px solid #68d391;
            border-radius: 5px;
        }
        
        .certification h4 {
            color: #276749;
            margin-bottom: 10px;
        }
        
        .doctor-info {
            margin-top: 40px;
            text-align: right;
        }
        
        .doctor-signature {
            margin-top: 60px;
            padding-top: 10px;
            border-top: 1px solid #333;
        }
        
        .doctor-signature .name {
            font-weight: bold;
            font-size: 12pt;
        }
        
        .doctor-signature .license {
            font-size: 10pt;
            color: #718096;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9pt;
            color: #718096;
        }
        
        .footer .stamp {
            margin-top: 15px;
            padding: 10px 30px;
            border: 2px dashed #a0aec0;
            display: inline-block;
            color: #a0aec0;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
            background: white;
            border-radius: 8px 8px 0 0;
        }
        
        .modal-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }
        
        .btn-close {
            background: #6b7280;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.2s;
        }
        
        .btn-close:hover {
            background: #4b5563;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            background: white;
            border-radius: 0 0 8px 8px;
        }
        
        .btn-print {
            background: #059669;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items-center;
            gap: 8px;
            transition: background 0.2s;
        }
        
        .btn-print:hover {
            background: #047857;
        }
        
        @media print {
            body {
                background: white;
            }
            
            .modal-overlay {
                position: static;
                background: none;
                padding: 0;
            }
            
            .modal-header,
            .modal-footer {
                display: none !important;
            }
            
            .modal-content {
                max-height: none;
                overflow: visible;
                box-shadow: none;
                border-radius: 0;
            }
            
            .print-area {
                width: 100%;
                min-height: auto;
                padding: 0;
            }
            
            .certificate {
                border: 3px double #2c5282;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Modal Overlay -->
    <div class="modal-overlay">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header">
                <h2>📄 {{ $certTypeName }} - Print Preview</h2>
                <a href="{{ url()->previous() }}" class="btn-close">✕ Close</a>
            </div>
            
            <!-- Print Area -->
            <div class="print-area">
                <div class="certificate">
                    <!-- Header -->
                    <div class="header">
                        <h1>{{ $clinicName }}</h1>
                        <h2>{{ $clinicAddress }}</h2>
                        <p>{{ $clinicContact }}</p>
                    </div>
                    
                    <!-- Title -->
                    <div class="title">
                        <h3>{{ $certTypeName }}</h3>
                    </div>
                    
                    <!-- Certificate Content -->
                    <div class="content">
                        <p>This is to certify that <strong>{{ $user->name }}</strong>, 
                           {{ $patient->age ? 'aged ' . $patient->age . ' years old,' : '' }}
                           {{ $patient->address ? 'residing at ' . $patient->address : '' }}
                           was examined at this clinic on <strong>{{ $appointmentDate }}</strong>.</p>
                        
                        @if($certificate->doctor_notes)
                        <div class="diagnosis-box">
                            <h4>Medical Assessment:</h4>
                            <p>{{ $certificate->doctor_notes }}</p>
                        </div>
                        @endif
                        
                        <!-- Certificate-specific content based on type -->
                        @switch($certSlug)
                            @case('sick_leave')
                            <div class="certification">
                                <h4>Recommendation:</h4>
                                <p>Based on the medical examination conducted, the patient is hereby advised to take a sick leave for <strong>___</strong> days, effective from <strong>{{ $issuedDate }}</strong>, to facilitate recovery.</p>
                            </div>
                            @break
                            
                            @case('fit_to_work')
                            <div class="certification">
                                <h4>Certification:</h4>
                                <p>I hereby certify that the above-named patient has been thoroughly examined and is <strong>physically and medically fit to resume work duties</strong> as of today's date.</p>
                            </div>
                            @break
                            
                            @case('fit_to_travel')
                            <div class="certification">
                                <h4>Certification:</h4>
                                <p>I hereby certify that the above-named patient has been medically evaluated and is <strong>fit to travel</strong> as of today's date.</p>
                            </div>
                            @break
                            
                            @case('school_pe')
                            <div class="certification">
                                <h4>Certification:</h4>
                                <p>I hereby certify that the above-named patient has been medically evaluated and is <strong>cleared for school activities and Physical Education</strong> as of today's date.</p>
                            </div>
                            @break
                            
                            @case('employment')
                            <div class="certification">
                                <h4>Certification:</h4>
                                <p>I hereby certify that the above-named patient has undergone medical examination and is <strong>medically fit for employment purposes</strong> as of today's date.</p>
                            </div>
                            @break
                            
                            @case('drivers_license')
                            <div class="certification">
                                <h4>Certification:</h4>
                                <p>I hereby certify that the above-named patient has been medically evaluated and is <strong>physically and mentally fit to operate a motor vehicle</strong> as of today's date.</p>
                                <p style="margin-top: 10px;"><strong>Visual Acuity:</strong> _______ &nbsp;&nbsp; <strong>Color Vision:</strong> _______</p>
                            </div>
                            @break
                            
                            @case('health_certificate')
                            <div class="certification">
                                <h4>Certification:</h4>
                                <p>I hereby certify that the above-named patient has been medically examined and is in <strong>good health</strong> as of today's date.</p>
                            </div>
                            @break
                            
                            @default
                            <div class="certification">
                                <h4>Certification:</h4>
                                <p>This is to certify that the above-named patient was examined at this clinic and found to be in {{ $certificate->doctor_notes ? 'satisfactory medical condition' : 'good health' }}.</p>
                            </div>
                        @endswitch
                    </div>
                    
                    <!-- Date and Signature -->
                    <div class="doctor-info">
                        <p>Issued on this <strong>{{ $issuedDate }}</strong></p>
                        
                        <div class="doctor-signature">
                            <p class="name">{{ $certificate->doctor->user->name ?? 'Dr. [Doctor Name]' }}</p>
                            <p class="license">License No.: {{ $certificate->doctor->license_number ?? '_____' }}</p>
                            <p class="license">PTR No.: _______</p>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="footer">
                        <p>This certificate is issued upon the request of the patient and is valid for the date indicated above.</p>
                        <p>For verification, please contact the clinic during office hours.</p>
                        <div class="stamp">CLINIC STAMP</div>
                    </div>
                </div>
            </div>
            
            <!-- Footer with Print Button -->
            <div class="modal-footer">
                <a href="{{ url()->previous() }}" class="btn-close">✕ Cancel</a>
                <button onclick="window.print()" class="btn-print">
                    🖨️ Print Certificate
                </button>
            </div>
        </div>
    </div>
</body>
</html>