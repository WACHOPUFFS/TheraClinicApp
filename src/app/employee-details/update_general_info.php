<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y manejo de CORS
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los datos enviados desde el frontend (Angular)
$data = json_decode(file_get_contents('php://input'), true);

// Verificar que los campos requeridos estén presentes
if (!isset($data['employee_id']) || !isset($data['employee_code']) || !isset($data['first_name']) || !isset($data['last_name']) || !isset($data['middle_name']) || !isset($data['birth_date']) || !isset($data['curp']) || !isset($data['rfc']) || !isset($data['phone_number']) || !isset($data['email']) || !isset($data['company_id'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
    exit;
}

// Asignar los datos recibidos a variables
$employee_id = intval($data['employee_id']);
$employee_code = $mysqli->real_escape_string($data['employee_code']);
$first_name = $mysqli->real_escape_string($data['first_name']);
$middle_name = $mysqli->real_escape_string($data['middle_name']);
$last_name = $mysqli->real_escape_string($data['last_name']);
$birth_date = $mysqli->real_escape_string($data['birth_date']);
$birth_place = isset($data['birth_place']) ? $mysqli->real_escape_string($data['birth_place']) : '';
$curp = $mysqli->real_escape_string($data['curp']);
$rfc = $mysqli->real_escape_string($data['rfc']);
$phone_number = $mysqli->real_escape_string($data['phone_number']);
$email = $mysqli->real_escape_string($data['email']);
$gender_id = intval($data['gender_id']);
$marital_status_id = intval($data['marital_status_id']);
$company_id = intval($data['company_id']);

// Concatenar nombre completo
$full_name = $first_name . ' ' . $middle_name . ' ' . $last_name;

// Verificar si el código de empleado ya existe para otro empleado dentro de la misma empresa
$check_code_query = "
    SELECT employee_id FROM employees 
    WHERE employee_code = '$employee_code' AND employee_id != $employee_id AND company_id = $company_id
";

$result_code = $mysqli->query($check_code_query);

// Si ya existe un empleado con el mismo código en la misma empresa, devolver un error
if ($result_code && $result_code->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El código de empleado ya está en uso por otro empleado en la misma empresa.']);
    exit;
}

// Verificar si la CURP ya existe para otro empleado dentro de la misma empresa
$check_curp_query = "
    SELECT employee_id FROM employees 
    WHERE curp = '$curp' AND employee_id != $employee_id AND company_id = $company_id
";

$result_curp = $mysqli->query($check_curp_query);

// Si ya existe un empleado con la misma CURP en la misma empresa, devolver un error
if ($result_curp && $result_curp->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'La CURP ya está en uso por otro empleado en la misma empresa.']);
    exit;
}

// Si no existen duplicados, proceder a actualizar los datos del empleado
$sql = "
    UPDATE employees SET 
        employee_code = '$employee_code', 
        first_name = '$first_name',
        middle_name = '$middle_name',
        last_name = '$last_name',
        full_name = '$full_name', 
        gender_id = $gender_id,  
        marital_status_id = $marital_status_id, 
        birth_date = '$birth_date',
        birth_place = '$birth_place',
        curp = '$curp',
        rfc = '$rfc',
        phone_number = '$phone_number',
        email = '$email',
        updated_at = NOW()
    WHERE employee_id = $employee_id AND company_id = $company_id
";

// Ejecutar la consulta
if ($mysqli->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Información General actualizada correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la información general', 'error' => $mysqli->error]);
}

// Cerrar la conexión
$mysqli->close();
?>
