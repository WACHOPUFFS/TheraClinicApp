<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los datos del formulario
$data = json_decode(file_get_contents("php://input"), true);

$employeeId = $data['id'] ?? null;
$departamento = $data['departamento'] ?? null;
$puesto = $data['puesto'] ?? null;
$turno = $data['turno'] ?? null;
$nombre = $data['nombre'] ?? null;
$apellidoPaterno = $data['apellidoPaterno'] ?? null;
$apellidoMaterno = $data['apellidoMaterno'] ?? null;
$fechaNacimiento = $data['fechaNacimiento'] ?? null;
$estadoCivil = $data['estadoCivil'] ?? null;
$sexo = $data['sexo'] ?? null;
$curp = $data['curp'] ?? null;
$numeroSeguroSocial = $data['numeroSeguroSocial'] ?? '';
$rfc = $data['rfc'] ?? '';
$correoElectronico = $data['correoElectronico'] ?? '';
$telefono = $data['telefono'] ?? '';
$contactoEmergencia = $data['contactoEmergencia'] ?? '';
$numEmergencia = $data['numEmergencia'] ?? '';
$fechaInicio = $data['fechaInicio'] ?? null;
$companyId = $data['companyId'] ?? null;
$netBalance = $data['net_balance'] ?? null;
$dailySalary = $data['daily_salary'] ?? null;
$employeeCode = $data['employee_code'] ?? null;

// Preparar los fragmentos de la declaración SQL
$sqlFragments = [];
if ($nombre !== null) $sqlFragments[] = "first_name = '$nombre'";
if ($apellidoPaterno !== null) $sqlFragments[] = "last_name = '$apellidoPaterno'";
if ($apellidoMaterno !== null) $sqlFragments[] = "middle_name = '$apellidoMaterno'";
if ($fechaNacimiento !== null) $sqlFragments[] = "birth_date = '$fechaNacimiento'";
if ($estadoCivil !== null) $sqlFragments[] = "marital_status_id = '$estadoCivil'";
if ($sexo !== null) $sqlFragments[] = "gender_id = '$sexo'";
if ($curp !== null) $sqlFragments[] = "curp = '$curp'";
if ($numeroSeguroSocial !== '') $sqlFragments[] = "social_security_number = '$numeroSeguroSocial'";
if ($rfc !== '') $sqlFragments[] = "rfc = '$rfc'";
if ($correoElectronico !== '') $sqlFragments[] = "email = '$correoElectronico'";
if ($telefono !== '') $sqlFragments[] = "phone_number = '$telefono'";
if ($contactoEmergencia !== '') $sqlFragments[] = "emergency_contact_name = '$contactoEmergencia'";
if ($numEmergencia !== '') $sqlFragments[] = "emergency_contact_number = '$numEmergencia'";
if ($fechaInicio !== null) $sqlFragments[] = "start_date = '$fechaInicio'";
if ($departamento !== null) $sqlFragments[] = "department_id = '$departamento'";
if ($puesto !== null) $sqlFragments[] = "position_id = '$puesto'";
if ($turno !== null) $sqlFragments[] = "shift_id = '$turno'";
if ($netBalance !== null) $sqlFragments[] = "net_balance = '$netBalance'";
if ($dailySalary !== null) $sqlFragments[] = "daily_salary = '$dailySalary'";
if ($employeeCode !== null) $sqlFragments[] = "employee_code = '$employeeCode'";

if ($employeeId !== null && count($sqlFragments) > 0) {
    $sql = "UPDATE employees SET " . implode(", ", $sqlFragments) . " WHERE employee_id = $employeeId";

    if ($mysqli->query($sql) === TRUE) {
        echo json_encode(['success' => 'Empleado actualizado exitosamente']);
    } else {
        error_log("Error en la consulta: " . $mysqli->error);
        echo json_encode(['error' => 'Error en la consulta SQL']);
    }
} else {
    echo json_encode(['error' => 'ID de empleado faltante o no hay campos para actualizar']);
}

$mysqli->close();
?>
