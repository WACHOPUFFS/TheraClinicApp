<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se han pasado los datos necesarios
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['company_id']) || !isset($data['department_name'])) {
    echo json_encode(['error' => 'company_id and department_name are required']);
    exit;
}

$company_id = intval($data['company_id']);
$department_name = $data['department_name'];
$description = isset($data['description']) ? $data['description'] : '';

// Consulta SQL para insertar un nuevo departamento
$sql = "INSERT INTO departments (company_id, department_name, description) VALUES (?, ?, ?)";
$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare the SQL statement']);
    exit;
}

$stmt->bind_param("iss", $company_id, $department_name, $description);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Departamento añadido exitosamente']);
} else {
    echo json_encode(['error' => 'Failed to add department: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
