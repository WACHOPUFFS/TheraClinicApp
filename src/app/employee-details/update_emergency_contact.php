<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y manejo de CORS
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los datos enviados desde el frontend (Angular)
$data = json_decode(file_get_contents('php://input'), true);

// Verificar que los campos necesarios estén presentes
if (!isset($data['employee_id']) || !isset($data['emergency_contact_name']) || !isset($data['emergency_contact_number'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
    exit;
}

// Asignar los datos recibidos a variables
$employee_id = intval($data['employee_id']);
$emergency_contact_name = $mysqli->real_escape_string($data['emergency_contact_name']);
$emergency_contact_number = $mysqli->real_escape_string($data['emergency_contact_number']);

// Construir la consulta SQL para actualizar el contacto de emergencia
$sql = "
    UPDATE employees SET 
        emergency_contact_name = '$emergency_contact_name', 
        emergency_contact_number = '$emergency_contact_number',
        updated_at = NOW() -- Actualiza la fecha de modificación
    WHERE employee_id = $employee_id
";

// Ejecutar la consulta
if ($mysqli->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Contacto de emergencia actualizado correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el contacto de emergencia', 'error' => $mysqli->error]);
}

// Cerrar la conexión
$mysqli->close();
?>
