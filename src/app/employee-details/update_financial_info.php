<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y manejo de CORS
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los datos enviados desde el frontend (Angular)
$data = json_decode(file_get_contents('php://input'), true);

// Verificar que los campos necesarios estén presentes
if (!isset($data['employee_id']) || !isset($data['social_security_number']) || !isset($data['bank_account_number']) || !isset($data['bank_name']) || !isset($data['clabe'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
    exit;
}

// Asignar los datos recibidos a variables
$employee_id = intval($data['employee_id']);
$social_security_number = $mysqli->real_escape_string($data['social_security_number']);
$bank_account_number = $mysqli->real_escape_string($data['bank_account_number']);
$bank_name = $mysqli->real_escape_string($data['bank_name']);
$bank_branch = isset($data['bank_branch']) ? $mysqli->real_escape_string($data['bank_branch']) : '';
$clabe = $mysqli->real_escape_string($data['clabe']);

// Construir la consulta SQL para actualizar la información financiera
$sql = "
    UPDATE employees SET 
        social_security_number = '$social_security_number', 
        bank_account_number = '$bank_account_number',
        bank_name = '$bank_name',
        bank_branch = '$bank_branch',
        clabe = '$clabe',
        updated_at = NOW() -- Actualiza la fecha de modificación
    WHERE employee_id = $employee_id
";

// Ejecutar la consulta
if ($mysqli->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Información Financiera y Bancaria actualizada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la información financiera', 'error' => $mysqli->error]);
}

// Cerrar la conexión
$mysqli->close();
?>
