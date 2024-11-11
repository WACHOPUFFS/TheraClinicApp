<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y manejo de CORS
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si los datos han sido enviados en el cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['employee_id'])) {
    echo json_encode(['success' => false, 'error' => 'employee_id is required']);
    exit;
}

// Obtener los datos del empleado
$employee_id = intval($data['employee_id']);
$employee_code = isset($data['employee_code']) ? $mysqli->real_escape_string($data['employee_code']) : null;
$first_name = isset($data['first_name']) ? $mysqli->real_escape_string($data['first_name']) : null;
$middle_name = isset($data['middle_name']) ? $mysqli->real_escape_string($data['middle_name']) : null;
$last_name = isset($data['last_name']) ? $mysqli->real_escape_string($data['last_name']) : null;
$birth_date = isset($data['birth_date']) ? $mysqli->real_escape_string($data['birth_date']) : null;
$birth_place = isset($data['birth_place']) ? $mysqli->real_escape_string($data['birth_place']) : null;
$curp = isset($data['curp']) ? $mysqli->real_escape_string($data['curp']) : null;
$social_security_number = isset($data['social_security_number']) ? $mysqli->real_escape_string($data['social_security_number']) : null;
$rfc = isset($data['rfc']) ? $mysqli->real_escape_string($data['rfc']) : null;
$homoclave = isset($data['homoclave']) ? $mysqli->real_escape_string($data['homoclave']) : null;
$phone_number = isset($data['phone_number']) ? $mysqli->real_escape_string($data['phone_number']) : null;
$email = isset($data['email']) ? $mysqli->real_escape_string($data['email']) : null;
$address = isset($data['address']) ? $mysqli->real_escape_string($data['address']) : null;
$city = isset($data['city']) ? $mysqli->real_escape_string($data['city']) : null;
$state = isset($data['state']) ? $mysqli->real_escape_string($data['state']) : null;
$postal_code = isset($data['postal_code']) ? $mysqli->real_escape_string($data['postal_code']) : null;
$department_id = isset($data['department_id']) ? intval($data['department_id']) : null;
$position_id = isset($data['position_id']) ? intval($data['position_id']) : null;
$shift_id = isset($data['shift_id']) ? intval($data['shift_id']) : null;
$employee_status = isset($data['employee_status']) ? $mysqli->real_escape_string($data['employee_status']) : null;

// Construir la consulta de actualización
$sql = "
    UPDATE employees
    SET 
        employee_code = '$employee_code',
        first_name = '$first_name',
        middle_name = '$middle_name',
        last_name = '$last_name',
        birth_date = '$birth_date',
        birth_place = '$birth_place',
        curp = '$curp',
        social_security_number = '$social_security_number',
        rfc = '$rfc',
        homoclave = '$homoclave',
        phone_number = '$phone_number',
        email = '$email',
        address = '$address',
        city = '$city',
        state = '$state',
        postal_code = '$postal_code',
        department_id = $department_id,
        position_id = $position_id,
        shift_id = $shift_id,
        employee_status = '$employee_status',
        updated_at = NOW()
    WHERE 
        employee_id = $employee_id
";

// Ejecutar la consulta
if ($mysqli->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Employee details updated successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update employee details: ' . $mysqli->error]);
}

// Cerrar la conexión
$mysqli->close();
?>
