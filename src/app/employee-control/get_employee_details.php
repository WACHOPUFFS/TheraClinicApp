<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y manejo de CORS
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los datos enviados en el cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

// Verificar si se ha pasado el ID del empleado
if (!isset($data['employee_id'])) {
    echo json_encode(['success' => false, 'error' => 'employee_id is required']);
    exit;
}

$employee_id = intval($data['employee_id']);

// Construcción segura de la consulta
$sql = "
    SELECT 
        e.employee_id, 
        e.first_name, 
        e.last_name, 
        e.curp, 
        e.social_security_number, 
        e.rfc, 
        e.email, 
        e.phone_number, 
        e.start_date, 
        d.department_name, 
        p.position_name, 
        s.shift_name
    FROM 
        employees e
    JOIN 
        departments d ON e.department_id = d.department_id
    JOIN 
        positions p ON e.position_id = p.position_id
    JOIN 
        shifts s ON e.shift_id = s.shift_id
    WHERE 
        e.employee_id = $employee_id
";

// Ejecutar la consulta
$result = $mysqli->query($sql);

if ($result) {
    $empleado = $result->fetch_assoc();

    // Verificar si el empleado existe
    if ($empleado) {
        // Devolver los detalles del empleado en formato JSON
        echo json_encode(['success' => true, 'employee' => $empleado]);
    } else {
        // No se encontró el empleado
        echo json_encode(['success' => false, 'error' => 'Employee not found']);
    }
} else {
    // Si hay un error al ejecutar la consulta
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve employee details: ' . $mysqli->error]);
}

// Cerrar la conexión
$mysqli->close();
?>
