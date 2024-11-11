<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Obtener el ID del empleado desde el cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);
$employeeId = isset($data['employee_id']) ? intval($data['employee_id']) : 0;

if ($employeeId > 0) {
    // Eliminar la solicitud del empleado y los archivos asociados
    $stmt = $mysqli->prepare("DELETE FROM employee_files WHERE employee_id = ?");
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $stmt->close();

    $stmt = $mysqli->prepare("DELETE FROM employee_requests WHERE employee_id = ?");
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $stmt->close();

    $stmt = $mysqli->prepare("DELETE FROM employees WHERE employee_id = ?");
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => 'Solicitud de empleado eliminada exitosamente']);
    } else {
        echo json_encode(['error' => 'Error al eliminar solicitud de empleado']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid employee ID']);
}

$mysqli->close();
?>
