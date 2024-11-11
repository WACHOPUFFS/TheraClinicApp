<?php
include_once 'cors.php';
// Incluir el archivo de conexión
include 'conexion.php';

// Leer datos de la solicitud POST
$data = json_decode(file_get_contents('php://input'), true);

$employeeId = $data['employeeId'];
$code = $data['code'];

// Preparar y ejecutar consulta SQL
$sql = "INSERT INTO employee_codes (employee_id, code) VALUES (?, ?)";
$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Error en la preparación de la consulta: " . $mysqli->error));
    exit();
}

$stmt->bind_param("ss", $employeeId, $code);

$response = [];

if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['error'] = $stmt->error;
}

// Cerrar conexiones
$stmt->close();
$mysqli->close();

// Devolver respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
