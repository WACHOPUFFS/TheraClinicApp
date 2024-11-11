<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se ha pasado el ID del departamento
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['department_id'])) {
    echo json_encode(['error' => 'department_id parameter is required']);
    exit;
}

$department_id = intval($data['department_id']);

// Consulta SQL para eliminar el departamento
$sql = "DELETE FROM departments WHERE department_id = ?";
$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare the SQL statement']);
    exit;
}

$stmt->bind_param("i", $department_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Departamento eliminado exitosamente']);
    } else {
        echo json_encode(['error' => 'No se encontró ningún departamento con ese ID']);
    }
} else {
    echo json_encode(['error' => 'Failed to delete department: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
