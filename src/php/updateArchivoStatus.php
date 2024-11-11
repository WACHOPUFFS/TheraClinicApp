<?php
include_once 'cors.php';
include_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del cuerpo de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);

    // Sanitizar y asignar los datos
    $id = $mysqli->real_escape_string($data['id']);
    $estado = $mysqli->real_escape_string($data['estado']);
    $comentario = isset($data['comentario']) ? $mysqli->real_escape_string($data['comentario']) : '';

    // Construir la consulta SQL
    $query = "UPDATE documentUploads SET estado = '$estado', comentario = '$comentario' WHERE id = '$id'";

    // Ejecutar la consulta
    if ($mysqli->query($query)) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("error" => "Error al actualizar el estado: " . $mysqli->error));
    }
} else {
    echo json_encode(array("error" => "Método no permitido"));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
