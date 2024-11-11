<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y manejo de CORS
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se han pasado los parámetros necesarios (company_id y shift_id) en la URL
if (!isset($_GET['company_id']) || !isset($_GET['shift_id'])) {
    echo json_encode(['success' => false, 'error' => 'company_id and shift_id are required']);
    exit;
}

// Obtener los parámetros desde la URL y asegurarse de que son enteros
$company_id = intval($_GET['company_id']);
$shift_id = intval($_GET['shift_id']);

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
        employee_requests er
    JOIN 
        employees e ON er.employee_id = e.employee_id
    JOIN 
        departments d ON e.department_id = d.department_id
    JOIN 
        positions p ON e.position_id = p.position_id
    JOIN 
        shifts s ON e.shift_id = s.shift_id
    WHERE 
        er.status = 'Finish'
        AND e.company_id = $company_id
        AND e.shift_id = $shift_id
";

// Ejecutar la consulta
$result = $mysqli->query($sql);

if ($result) {
    $empleados = [];

    // Obtener los resultados
    while ($row = $result->fetch_assoc()) {
        $empleados[] = $row;
    }

    // Comprobar si se encontraron empleados
    if (!empty($empleados)) {
        // Devolver los empleados en formato JSON
        echo json_encode(['success' => true, 'employees' => $empleados]);
    } else {
        // No se encontraron empleados
        echo json_encode(['success' => false, 'error' => 'No employees found for the given shift and company']);
    }
} else {
    // Si hay un error al ejecutar la consulta
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve employees: ' . $mysqli->error]);
}

// Cerrar la conexión
$mysqli->close();
?>
