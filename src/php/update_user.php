<?php
include_once 'cors.php';
include_once 'conexion.php';

    $data = json_decode(file_get_contents("php://input"));

    // Verificar si se recibieron los datos esperados
    if (
        isset($data->idUser) &&
        isset($data->fullName) &&
        isset($data->userName) &&
        isset($data->userEmail)
    ) {
        // Escapar los datos para prevenir inyección SQL
        $idUser = $mysqli->real_escape_string($data->idUser);
        $fullName = $mysqli->real_escape_string($data->fullName);
        $userName = $mysqli->real_escape_string($data->userName);
        $userEmail = $mysqli->real_escape_string($data->userEmail);
        
        // Si se proporciona una nueva contraseña, encriptarla
        $userPassword = isset($data->userPassword) ? md5($mysqli->real_escape_string($data->userPassword)) : null;

        // Verificar si el nombre de usuario ya existe
        $sqlCheckUsername = "SELECT id FROM users WHERE username = '$userName' AND id != '$idUser'";
        $resultCheckUsername = $mysqli->query($sqlCheckUsername);
        if ($resultCheckUsername->num_rows > 0) {
            // Enviar una respuesta de error si el nombre de usuario ya existe
            echo json_encode(array("success" => false, "message" => "Username already in use."));
            exit;
        }

        // Verificar si el correo electrónico ya existe
        $sqlCheckEmail = "SELECT id FROM users WHERE email = '$userEmail' AND id != '$idUser'";
        $resultCheckEmail = $mysqli->query($sqlCheckEmail);
        if ($resultCheckEmail->num_rows > 0) {
            // Enviar una respuesta de error si el correo electrónico ya está en uso
            echo json_encode(array("success" => false, "message" => "Email already in use."));
            exit;
        }

        // Construir la consulta de actualización
        $sqlUpdateUser = "UPDATE users SET name = '$fullName', username = '$userName', email = '$userEmail'";
        if ($userPassword) {
            $sqlUpdateUser .= ", password = '$userPassword'";
        }
        $sqlUpdateUser .= " WHERE id = '$idUser'";

        // Ejecutar la consulta de actualización
        if ($mysqli->query($sqlUpdateUser)) {
            echo json_encode(array("success" => true, "message" => "User updated successfully."));
        } else {
            echo json_encode(array("success" => false, "message" => "Error updating user: " . $mysqli->error));
        }
    } else {
        // No se recibieron los datos esperados
        echo json_encode(array("success" => false, "message" => "Provide all necessary data for the update."));
    }


// Cerrar la conexión a la base de datos
$mysqli->close();
?>
