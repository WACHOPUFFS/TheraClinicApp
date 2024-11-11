<?php
// Incluir el archivo de conexión a la base de datos
include_once 'cors.php';
include_once 'conexion.php';

// Verificar si se recibió el ID del usuario a eliminar
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Obtener el ID del usuario a eliminar y asegurar que es un entero
    $id = intval($_GET['id']);

    // Consulta SQL para eliminar el usuario con el ID especificado
    $query = "DELETE FROM users WHERE id = ?";
    
    // Preparar la consulta
    if ($stmt = $mysqli->prepare($query)) {
        // Vincular el parámetro y ejecutar la consulta
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Si la eliminación fue exitosa, devolver una respuesta exitosa en formato JSON
            echo json_encode(array("success" => true));
        } else {
            // Si hubo un error en la eliminación, devolver un mensaje de error en formato JSON
            echo json_encode(array("success" => false, "message" => "Error al eliminar el usuario: " . $stmt->error));
        }
        // Cerrar la declaración preparada
        $stmt->close();
    } else {
        // Si hubo un error al preparar la consulta, devolver un mensaje de error en formato JSON
        echo json_encode(array("success" => false, "message" => "Error al preparar la consulta: " . $mysqli->error));
    }
} else {
    // Si no se recibió un ID válido del usuario a eliminar, devolver un mensaje de error en formato JSON
    echo json_encode(array("success" => false, "message" => "ID de usuario no proporcionado o no válido"));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
