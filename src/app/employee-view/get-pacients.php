<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y manejo de CORS
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se ha pasado el parámetro necesario (company_id)
if (!isset($_GET['company_id'])) {
    echo json_encode(['success' => false, 'error' => 'company_id is required']);
    exit;
}

// Obtener el parámetro desde la URL
$company_id = intval($_GET['company_id']);

// Construcción segura de la consulta
$sql = "
    SELECT 
        e.employee_id, 
        e.employee_code,
        e.first_name, 
        e.middle_name, 
        e.last_name, 
        e.email, 
        e.phone_number, 
        e.start_date, 
        e.created_at, 
        e.updated_at,
        d.department_name, 
        p.position_name, 
        s.shift_name
    FROM 
        employees e
    LEFT JOIN 
        departments d ON e.department_id = d.department_id
    LEFT JOIN 
        positions p ON e.position_id = p.position_id
    LEFT JOIN 
        shifts s ON e.shift_id = s.shift_id
    WHERE 
        e.company_id = $company_id
";

// Ejecutar la consulta
$result = $mysqli->query($sql);

if ($result) {
    $empleados = [];

    // Obtener los resultados
    while ($row = $result->fetch_assoc()) {
        $empleados[] = $row;
    }

    // Devolver los empleados en formato JSON
    echo json_encode(['success' => true, 'employees' => $empleados]);
} else {
    // Si hay un error al ejecutar la consulta
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve employees: ' . $mysqli->error]);
}

// Cerrar la conexión
$mysqli->close();
?>
