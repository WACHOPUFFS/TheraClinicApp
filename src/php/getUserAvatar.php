<?php
include_once 'cors.php';
include_once 'conexion.php';

// Verificar la conexión a la base de datos
if ($mysqli->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Conexión fallida: " . $mysqli->connect_error));
    exit();
}

// Obtener el ID de usuario desde los parámetros de la solicitud
$userId = isset($_GET['userId']) ? $mysqli->real_escape_string($_GET['userId']) : null;

if ($userId) {
    // Consulta para obtener el avatar del usuario
    $query = "SELECT avatar FROM users WHERE id = '$userId'";

    $result = $mysqli->query($query);

    if ($result) {
        if ($row = $result->fetch_assoc()) {
            // Comprobar si la URL del avatar está vacía y asignar la imagen predeterminada si es necesario
            $avatarUrl = $row['avatar'] ? 'https://www.siinad.mx/php/' . $row['avatar'] : 'https://www.siinad.mx/php/uploads/logos/no-image.png';

            // Devolver la URL del avatar en formato JSON
            header('Content-Type: application/json');
            echo json_encode(array("avatarUrl" => $avatarUrl));
        } else {
            // Manejar el caso en que no se encuentre el usuario
            header('Content-Type: application/json');
            echo json_encode(array("error" => "Usuario no encontrado."));
        }
    } else {
        // Manejar el error en caso de que la consulta falle
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al ejecutar la consulta: " . $mysqli->error));
    }
} else {
    // Manejar el caso en que no se proporcione un ID de usuario válido
    header('Content-Type: application/json');
    echo json_encode(array("error" => "ID de usuario no proporcionado."));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
