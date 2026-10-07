<?php

echo "=== CREATING MISSING DOCTOR AND PATIENT RECORDS ===\n\n";

try {
    $pdo = new PDO('sqlite:database/database.sqlite');

    echo "1. CREATING DOCTOR RECORDS:\n";

    // Get users with user_type = 3 (doctors) who don't have doctor records
    $doctorUsers = $pdo->query("
        SELECT u.id, u.name, u.email
        FROM users u
        WHERE u.user_type = 3
        AND u.id NOT IN (SELECT user_id FROM doctors WHERE user_id IS NOT NULL)
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($doctorUsers as $user) {
        $stmt = $pdo->prepare("
            INSERT INTO doctors (user_id, specialization, address, created_at, updated_at)
            VALUES (?, ?, ?, datetime('now'), datetime('now'))
        ");
        $stmt->execute([
            $user['id'],
            'General Medicine', // Default specialization
            'CKC Medical Center' // Default address
        ]);
        echo "   ✓ Created doctor record for: {$user['name']} (User ID: {$user['id']})\n";
    }

    echo "\n2. CREATING PATIENT RECORDS:\n";

    // Get users with user_type = 1 or 2 (students/staff) who don't have patient records
    $patientUsers = $pdo->query("
        SELECT u.id, u.name, u.email, u.user_type
        FROM users u
        WHERE u.user_type IN (1, 2)
        AND u.id NOT IN (SELECT user_id FROM patients WHERE user_id IS NOT NULL)
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($patientUsers as $user) {
        $patientType = ($user['user_type'] == 1) ? 1 : 2; // 1 = Student, 2 = Staff

        $stmt = $pdo->prepare("
            INSERT INTO patients (
                patient_type, user_id, address, medical_condition, medical_illness,
                operations, allergies, emergency_contact_name, emergency_contact_number,
                emergency_relationship, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))
        ");
        $stmt->execute([
            $patientType,
            $user['id'],
            'N/A', // Default address
            'None', // Default medical condition
            'None', // Default medical illness
            'None', // Default operations
            'None', // Default allergies
            'Emergency Contact', // Default emergency contact name
            '000-000-0000', // Default emergency contact number
            'Family' // Default emergency relationship
        ]);

        $typeName = ($user['user_type'] == 1) ? 'Student' : 'Staff';
        echo "   ✓ Created patient record for: {$user['name']} (User ID: {$user['id']}, Type: {$typeName})\n";
    }

    echo "\n3. VERIFICATION:\n";

    $doctorsCount = $pdo->query("SELECT COUNT(*) as count FROM doctors")->fetch(PDO::FETCH_ASSOC);
    $patientsCount = $pdo->query("SELECT COUNT(*) as count FROM patients")->fetch(PDO::FETCH_ASSOC);

    echo "   Total doctors now: {$doctorsCount['count']}\n";
    echo "   Total patients now: {$patientsCount['count']}\n";

    echo "\n✅ Missing records created successfully!\n";
    echo "The appointment dropdowns should now show data.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
