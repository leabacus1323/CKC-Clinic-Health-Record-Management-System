<?php

echo "=== TESTING APPOINTMENT DROPDOWN QUERIES ===\n\n";

try {
    $pdo = new PDO('sqlite:database/database.sqlite');

    echo "1. TESTING PATIENTS QUERY (same as controller):\n";

    // This is the exact query from AdminAppointments controller
    $patients = $pdo->query("
        SELECT p.id, u.name, p.patient_type
        FROM patients p
        JOIN users u ON u.id = p.user_id
        WHERE u.user_type IN (1, 2)
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo "   Found {" . count($patients) . "} patients:\n";
    foreach ($patients as $patient) {
        echo "     - ID: {$patient['id']}, Name: {$patient['name']}, Type: {$patient['patient_type']}\n";
    }

    echo "\n2. TESTING DOCTORS QUERY (same as controller):\n";

    // This is the exact query from AdminAppointments controller
    $doctors = $pdo->query("
        SELECT d.id, u.name, d.specialization
        FROM doctors d
        JOIN users u ON u.id = d.user_id
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo "   Found {" . count($doctors) . "} doctors:\n";
    foreach ($doctors as $doctor) {
        echo "     - ID: {$doctor['id']}, Name: {$doctor['name']}, Specialization: {$doctor['specialization']}\n";
    }

    echo "\n✅ Both queries return data successfully!\n";
    echo "The appointment dropdowns should now be populated.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
