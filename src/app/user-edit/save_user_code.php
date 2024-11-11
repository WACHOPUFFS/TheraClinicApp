<?php
include_once 'cors.php';
include_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"));

// Verificar si se recibieron los datos esperados
if (isset($data->user_id) && isset($data->code)) {
    // Escapar los datos para prevenir inyección SQL
    $user_id = $mysqli->real_escape_string($data->user_id);
    $code = $mysqli->real_escape_string($data->code);

    // Verificar si el código ya existe para el usuario (opcional)
    $sqlCheckCode = "SELECT code FROM user_codes WHERE user_id = '$user_id' AND code = '$code'";
    $resultCheckCode = $mysqli->query($sqlCheckCode);
    if ($resultCheckCode->num_rows > 0) {
        // Enviar una respuesta si el código ya existe
        echo json_encode(array("success" => false, "message" => "Code already exists for this user."));
        exit;
    }

    // Insertar el código en la tabla user_codes
    $sqlInsertCode = "INSERT INTO user_codes (code, user_id, created_at) VALUES ('$code', '$user_id', NOW())";

    // Ejecutar la consulta de inserción
    if ($mysqli->query($sqlInsertCode)) {
        echo json_encode(array("success" => true, "message" => "Code saved successfully."));
    } else {
        echo json_encode(array("success" => false, "message" => "Error saving code: " . $mysqli->error));
    }
} else {
    // No se recibieron los datos esperados
    echo json_encode(array("success" => false, "message" => "Provide all necessary data for saving the code."));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
