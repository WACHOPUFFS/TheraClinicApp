<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se ha pasado el ID del puesto
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['position_id'])) {
    echo json_encode(['error' => 'position_id parameter is required']);
    exit;
}

$position_id = intval($data['position_id']);

// Consulta SQL para eliminar el puesto
$sql = "DELETE FROM positions WHERE position_id = ?";
$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare the SQL statement']);
    exit;
}

$stmt->bind_param("i", $position_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Puesto eliminado exitosamente']);
    } else {
        echo json_encode(['error' => 'No se encontró ningún puesto con ese ID']);
    }
} else {
    echo json_encode(['error' => 'Failed to delete position: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
