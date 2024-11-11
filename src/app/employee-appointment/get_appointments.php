<?php
header('Content-Type: application/json');
require_once 'conexion.php';
require_once 'cors.php';

// Verificar que se haya pasado el parámetro necesario (company_id)
if (!isset($_GET['company_id'])) {
    echo json_encode(['success' => false, 'error' => 'company_id is required']);
    exit;
}

// Obtener el parámetro desde la URL
$company_id = intval($_GET['company_id']);

// Consulta para obtener las citas con los nombres de empleados y terapeutas
$sql = "
    SELECT 
        a.id,
        a.employee_id,
        e.first_name AS employee_first_name,
        e.last_name AS employee_last_name,
        a.therapist_id,
        t.name AS therapist_name,
        a.therapy_type,
        a.date,
        a.time,
        a.notes,
        a.created_at,
        a.updated_at
    FROM 
        appointments a
    JOIN 
        employees e ON a.employee_id = e.employee_id
    JOIN 
        users t ON a.therapist_id = t.id
    WHERE 
        e.company_id = $company_id
";

// Ejecutar la consulta
$result = $mysqli->query($sql);

if ($result) {
    $appointments = [];

    // Recorrer los resultados y construir el arreglo de citas
    while ($row = $result->fetch_assoc()) {
        $appointments[] = [
            'id' => $row['id'],
            'employee_id' => $row['employee_id'],
            'employee_name' => $row['employee_first_name'] . ' ' . $row['employee_last_name'],
            'therapist_id' => $row['therapist_id'],
            'therapist_name' => $row['therapist_name'],
            'therapy_type' => $row['therapy_type'],
            'date' => $row['date'],
            'time' => $row['time'],
            'notes' => $row['notes'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
    }

    // Devolver las citas en formato JSON
    echo json_encode(['success' => true, 'appointments' => $appointments]);
} else {
    // Manejo de errores
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve appointments: ' . $mysqli->error]);
}

// Cerrar l
