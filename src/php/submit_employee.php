<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los datos del formulario
$data = json_decode(file_get_contents("php://input"), true);

// Depuración: Imprimir los datos recibidos
error_log(print_r($data, true));

if (
    isset($data['nombre']) &&
    isset($data['apellidoPaterno']) &&
    isset($data['apellidoMaterno'])
) {
    $nombre = $mysqli->real_escape_string($data['nombre']);
    $apellidoPaterno = $mysqli->real_escape_string($data['apellidoPaterno']);
    $apellidoMaterno = $mysqli->real_escape_string($data['apellidoMaterno']);
    $fechaNacimiento = isset($data['fechaNacimiento']) ? $mysqli->real_escape_string($data['fechaNacimiento']) : NULL;
    $lugarNacimiento = isset($data['lugarNacimiento']) ? $mysqli->real_escape_string($data['lugarNacimiento']) : '';
    $estadoCivil = isset($data['estadoCivil']) ? (int)$data['estadoCivil'] : NULL;
    $sexo = isset($data['sexo']) ? (int)$data['sexo'] : NULL;
    $curp = isset($data['curp']) ? $mysqli->real_escape_string($data['curp']) : '';
    $numeroSeguroSocial = isset($data['numeroSeguroSocial']) ? $mysqli->real_escape_string($data['numeroSeguroSocial']) : '';
    $rfc = isset($data['rfc']) ? $mysqli->real_escape_string($data['rfc']) : '';
    $correoElectronico = isset($data['correoElectronico']) ? $mysqli->real_escape_string($data['correoElectronico']) : '';
    $telefono = isset($data['telefono']) ? $mysqli->real_escape_string($data['telefono']) : '';
    $contactoEmergencia = isset($data['contactoEmergencia']) ? $mysqli->real_escape_string($data['contactoEmergencia']) : '';
    $numEmergencia = isset($data['numEmergencia']) ? $mysqli->real_escape_string($data['numEmergencia']) : '';
    $fechaInicio = isset($data['fechaInicio']) ? $mysqli->real_escape_string($data['fechaInicio']) : NULL;
    $departamento = isset($data['departamento']) ? (int)$data['departamento'] : NULL;
    $puesto = isset($data['puesto']) ? (int)$data['puesto'] : NULL;
    $turno = isset($data['turno']) ? (int)$data['turno'] : NULL;
    $companyId = (int)$data['companyId'];
    $userId = (int)$data['userId'];
    $status = $data['status'];

    // Campos bancarios adicionales
    $numeroCuenta = isset($data['numeroCuenta']) ? $mysqli->real_escape_string($data['numeroCuenta']) : '';
    $nombreBanco = isset($data['nombreBanco']) ? $mysqli->real_escape_string($data['nombreBanco']) : '';
    $sucursalBanco = isset($data['sucursalBanco']) ? $mysqli->real_escape_string($data['sucursalBanco']) : '';
    $clabeInterbancaria = isset($data['clabeInterbancaria']) ? $mysqli->real_escape_string($data['clabeInterbancaria']) : '';

    // Preparar la declaración SQL
    $sql = "INSERT INTO employees (
        first_name, last_name, middle_name, birth_date, birth_place, marital_status_id, gender_id, curp, social_security_number, rfc, 
        email, phone_number, emergency_contact_name, emergency_contact_number, start_date, department_id, position_id, 
        shift_id, company_id, bank_account_number, bank_name, bank_branch, clabe
    ) VALUES (
        '$nombre', '$apellidoPaterno', '$apellidoMaterno', '$fechaNacimiento', '$lugarNacimiento', $estadoCivil, $sexo, '$curp', '$numeroSeguroSocial', 
        '$rfc', '$correoElectronico', '$telefono', '$contactoEmergencia', '$numEmergencia', '$fechaInicio', $departamento, 
        $puesto, $turno, $companyId, '$numeroCuenta', '$nombreBanco', '$sucursalBanco', '$clabeInterbancaria'
    )";
    
    if ($mysqli->query($sql) === TRUE) {
        $employee_id = $mysqli->insert_id;

        $sql = "INSERT INTO employee_requests (employee_id, status, created_by) VALUES ($employee_id, '$status', $userId)";

        if ($mysqli->query($sql) === TRUE) {
            $request_id = $mysqli->insert_id; // Obtener el ID de la solicitud recién insertada
            echo json_encode(['employee_id' => $employee_id, 'request_id' => $request_id, 'success' => 'Employee request submitted successfully']);
        } else {
            error_log("Error en la consulta: " . $mysqli->error);
            echo json_encode(['error' => 'Error en la consulta SQL al insertar en employee_requests']);
        }
    } else {
        error_log("Error en la consulta: " . $mysqli->error);
        echo json_encode(['error' => 'Error en la consulta SQL al insertar en employees']);
    }
} else {
    echo json_encode(['error' => 'Required fields are missing']);
}

$mysqli->close();
?>
