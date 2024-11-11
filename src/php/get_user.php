<?php
include_once 'cors.php';
include_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $idUser = $_GET['idUser'];

    // Validar que se haya proporcionado el idUser
    if (isset($idUser)) {
        // Escapar el idUser para prevenir inyección SQL
        $idUserEscapado = $mysqli->real_escape_string($idUser);

        // Consulta para obtener la información del usuario
        $sql = "SELECT username, name, email, avatar FROM users WHERE id = '$idUserEscapado'";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            // Obtener los datos del usuario
            $user = $result->fetch_assoc();
            $response = array(
                "success" => true,
                "data" => $user
            );
        } else {
            // No se encontró el usuario
            $response = array(
                "success" => false,
                "message" => "User not found."
            );
        }
    } else {
        // No se proporcionó el idUser
        $response = array(
            "success" => false,
            "message" => "idUser not provided."
        );
    }

    // Enviar respuesta en formato JSON
    echo json_encode($response);
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
