<?php
// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los datos del cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

$file_id = $data['file_id'];

// Verificar que el ID del archivo se haya recibido
if (!isset($file_id)) {
    echo json_encode(['success' => false, 'message' => 'ID del archivo no recibido']);
    exit;
}

// Preparar la consulta para eliminar el archivo
$query = "DELETE FROM employee_files WHERE file_id = ?";

if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param("i", $file_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Archivo eliminado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el archivo']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
}

// Cerrar la conexión
$mysqli->close();
?>
