<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se ha pasado el ID del turno
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['shift_id'])) {
    echo json_encode(['error' => 'shift_id parameter is required']);
    exit;
}

$shift_id = intval($data['shift_id']);

// Consulta SQL para eliminar el turno
$sql = "DELETE FROM shifts WHERE shift_id = ?";
$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare the SQL statement']);
    exit;
}

$stmt->bind_param("i", $shift_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Turno eliminado exitosamente']);
    } else {
        echo json_encode(['error' => 'No se encontró ningún turno con ese ID']);
    }
} else {
    echo json_encode(['error' => 'Failed to delete shift: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
