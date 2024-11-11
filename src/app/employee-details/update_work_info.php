<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y manejo de CORS
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los datos enviados desde el frontend (Angular)
$data = json_decode(file_get_contents('php://input'), true);

// Verificar que los campos necesarios estén presentes
if (!isset($data['employee_id']) || !isset($data['department_id']) || !isset($data['position_id']) || 
    !isset($data['shift_id']) || !isset($data['start_date']) || !isset($data['employee_status']) || 
    !isset($data['net_balance']) || !isset($data['daily_salary'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
    exit;
}

// Asignar los datos recibidos a variables
$employee_id = intval($data['employee_id']);
$department_id = intval($data['department_id']);
$position_id = intval($data['position_id']);
$shift_id = intval($data['shift_id']);
$start_date = $mysqli->real_escape_string($data['start_date']);
$employee_status = $mysqli->real_escape_string($data['employee_status']);
$net_balance = floatval($data['net_balance']);
$daily_salary = floatval($data['daily_salary']);

// Construir la consulta SQL para actualizar la información laboral
$sql = "
    UPDATE employees SET 
        department_id = $department_id, 
        position_id = $position_id,
        shift_id = $shift_id,
        start_date = '$start_date',
        employee_status = '$employee_status',
        net_balance = $net_balance,
        daily_salary = $daily_salary,
        updated_at = NOW() -- Actualiza la fecha de modificación
    WHERE employee_id = $employee_id
";

// Ejecutar la consulta
if ($mysqli->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Información del Trabajo actualizada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la información laboral', 'error' => $mysqli->error]);
}

// Cerrar la conexión
$mysqli->close();
?>
