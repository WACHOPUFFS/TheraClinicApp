<?php
include_once 'cors.php';
// Incluir el archivo de conexión
include 'conexion.php';

// Leer datos de la solicitud POST
$data = json_decode(file_get_contents('php://input'), true);

$employeeId = $data['employeeId'];
$code = $data['code'];

// Verificar si el usuario ya tiene un código
$sqlCheck = "SELECT * FROM employee_codes WHERE employee_id = ?";
$stmtCheck = $mysqli->prepare($sqlCheck);

if ($stmtCheck === false) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Error en la preparación de la consulta: " . $mysqli->error));
    exit();
}

$stmtCheck->bind_param("s", $employeeId);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

$response = [];

if ($resultCheck->num_rows > 0) {
    // Si el código ya existe, actualizarlo
    $sqlUpdate = "UPDATE employee_codes SET code = ? WHERE employee_id = ?";
    $stmtUpdate = $mysqli->prepare($sqlUpdate);

    if ($stmtUpdate === false) {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error en la preparación de la consulta: " . $mysqli->error));
        exit();
    }

    $stmtUpdate->bind_param("ss", $code, $employeeId);

    if ($stmtUpdate->execute()) {
        $response['success'] = true;
        $response['message'] = "Código actualizado correctamente.";
    } else {
        $response['success'] = false;
        $response['error'] = $stmtUpdate->error;
    }

    $stmtUpdate->close();
} else {
    // Si el código no existe, insertarlo
    $sqlInsert = "INSERT INTO employee_codes (employee_id, code) VALUES (?, ?)";
    $stmtInsert = $mysqli->prepare($sqlInsert);

    if ($stmtInsert === false) {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error en la preparación de la consulta: " . $mysqli->error));
        exit();
    }

    $stmtInsert->bind_param("ss", $employeeId, $code);

    if ($stmtInsert->execute()) {
        $response['success'] = true;
        $response['message'] = "Código insertado correctamente.";
    } else {
        $response['success'] = false;
        $response['error'] = $stmtInsert->error;
    }

    $stmtInsert->close();
}

// Cerrar conexiones
$stmtCheck->close();
$mysqli->close();

// Devolver respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
