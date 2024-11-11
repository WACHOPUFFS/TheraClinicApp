<?php

// Incluir la configuración de la base de datos
include_once 'cors.php';
include_once 'conexion.php';

// Obtener los datos del cuerpo de la solicitud POST
$data = json_decode(file_get_contents("php://input"));

// Verificar si se recibieron los datos esperados
if (
    isset($data->userRequester) &&
    isset($data->message) &&
    isset($data->senderCompany)
) {
    // Escapar los datos para prevenir inyección SQL
    $userRequester = $mysqli->real_escape_string($data->userRequester);
    $message = $mysqli->real_escape_string($data->message);
    $senderCompany = $mysqli->real_escape_string($data->senderCompany);

    // Consulta SQL para insertar el mensaje en la base de datos
    $sql = "INSERT INTO companyRequest (userRequester, message, senderCompany, status) VALUES ('$userRequester', '$message', '$senderCompany', 'pendiente')";

    if ($mysqli->query($sql)) {
        // Enviar una respuesta de éxito
        $response = array("success" => true, "message" => "¡Mensaje guardado exitosamente!");
        echo json_encode($response);
    } else {
        // Enviar una respuesta de error
        $response = array("success" => false, "message" => "Error al guardar el mensaje: " . $mysqli->error);
        echo json_encode($response);
    }
} else {
    // Enviar una respuesta de error si no se recibieron los datos esperados
    $response = array("success" => false, "message" => "Por favor, proporcione todos los datos necesarios para guardar el mensaje.");
    echo json_encode($response);
}

// Cerrar la conexión a la base de datos
$mysqli->close();

?>
