<?php
header('Content-Type: application/json');
include_once 'cors.php';
include_once 'conexion.php';
// Verificar que se haya pasado el parámetro necesario (id)
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'Appointment ID is required']);
    exit;
}

// Obtener el id de la cita desde la URL
$appointment_id = intval($_GET['id']);

// Consulta para eliminar la cita
$sql = "DELETE FROM appointments WHERE id = $appointment_id";

// Ejecutar la consulta
if ($mysqli->query($sql)) {
    if ($mysqli->affected_rows > 0) {
        // Si se eliminó alguna fila, la cita fue eliminada con éxito
        echo json_encode(['success' => true, 'message' => 'Appointment deleted successfully']);
    } else {
        // Si no se afectaron filas, el ID no existe en la base de datos
        echo json_encode(['success' => false, 'error' => 'No appointment found with the provided ID']);
    }
} else {
    // Manejo de errores en la ejecución de la consulta
    echo json_encode(['success' => false, 'error' => 'Failed to delete appointment: ' . $mysqli->error]);
}

// Cerrar la conexión
$mysqli->close();
