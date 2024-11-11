<?php
include_once 'cors.php';
// Incluir el archivo de conexión
include 'conexion.php';

// Leer datos de la solicitud POST
$data = json_decode(file_get_contents('php://input'), true);

// Verificar si se recibió el código de usuario
if (isset($data['userCode'])) {
    $userCode = $mysqli->real_escape_string($data['userCode']);

    // Preparar y ejecutar consulta SQL para obtener el user_id desde el código
    $sqlGetUserId = "SELECT user_id FROM user_codes WHERE code = ?";
    $stmtGetUserId = $mysqli->prepare($sqlGetUserId);

    if ($stmtGetUserId === false) {
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error en la preparación de la consulta: " . $mysqli->error));
        exit();
    }

    $stmtGetUserId->bind_param("s", $userCode);
    $stmtGetUserId->execute();
    $resultGetUserId = $stmtGetUserId->get_result();

    $response = [];

    if ($resultGetUserId->num_rows > 0) {
        $userIdRow = $resultGetUserId->fetch_assoc();
        $userId = $userIdRow['user_id'];

        // Preparar y ejecutar consulta SQL para obtener los datos del usuario por user_id
        $sqlGetUser = "SELECT * FROM users WHERE id = ?";
        $stmtGetUser = $mysqli->prepare($sqlGetUser);

        if ($stmtGetUser === false) {
            header('Content-Type: application/json');
            echo json_encode(array("error" => "Error en la preparación de la consulta: " . $mysqli->error));
            exit();
        }

        $stmtGetUser->bind_param("i", $userId); // Tipo "i" para enteros
        $stmtGetUser->execute();
        $resultGetUser = $stmtGetUser->get_result();

        if ($resultGetUser->num_rows > 0) {
            $user = $resultGetUser->fetch_assoc();
            $response['success'] = true;
            $response['user'] = $user;
        } else {
            $response['success'] = false;
            $response['error'] = "Usuario no encontrado.";
        }

        $stmtGetUser->close();
    } else {
        $response['success'] = false;
        $response['error'] = "Código de usuario no encontrado.";
    }

    $stmtGetUserId->close();
} else {
    $response['success'] = false;
    $response['error'] = "Código de usuario no proporcionado.";
}

// Cerrar la conexión a la base de datos
$mysqli->close();

// Devolver respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
