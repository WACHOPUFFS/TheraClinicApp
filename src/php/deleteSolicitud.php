<?php
include_once 'cors.php';
include_once 'conexion.php';
include_once 'enviar_correo.php'; // Incluir el archivo enviar_correo.php

// Verificar si se recibieron datos mediante POST
$data = json_decode(file_get_contents("php://input"), true);

// Verificar si se recibieron todos los campos requeridos
if (isset($data['userId']) && isset($data['motivo'])) {
    // Obtener el ID de la solicitud y el motivo de rechazo desde los datos enviados por la solicitud HTTP
    $userId = $data['userId'];
    $motivo = $data['motivo'];

    // Consultar el correo electrónico del usuario asociado a la solicitud premium
    $userEmailQuery = "SELECT email FROM premium_user_requests WHERE id = ?";
    $stmtUserEmail = $mysqli->prepare($userEmailQuery);
    $stmtUserEmail->bind_param('i', $userId);
    $stmtUserEmail->execute();
    $userEmailResult = $stmtUserEmail->get_result();

    if ($userEmailResult && $userEmailResult->num_rows > 0) {
        $userEmailRow = $userEmailResult->fetch_assoc();
        $userEmail = $userEmailRow['email'];

        // Llamar a la función enviarCorreo() para enviar el correo al usuario
        enviarCorreo($userEmail, $motivo, 'eliminacion_peticion');

        // Después de enviar el correo, eliminar la solicitud de usuario premium
        $sql_delete_premium_request = "DELETE FROM premium_user_requests WHERE id = ?";
        $stmtDelete = $mysqli->prepare($sql_delete_premium_request);
        $stmtDelete->bind_param('i', $userId);

        if ($stmtDelete->execute()) {
            echo json_encode(array("success" => true));
        } else {
            // Si hay algún error al eliminar la solicitud, devolver un mensaje de error
            echo json_encode(array("error" => "Error al eliminar la solicitud de usuario premium: " . $stmtDelete->error));
        }

        $stmtDelete->close();
    } else {
        echo json_encode(array("error" => "No se encontró el correo electrónico del usuario asociado a la solicitud premium."));
    }

    $stmtUserEmail->close();
} else {
    // Si alguno de los campos requeridos no está presente en la solicitud, devolver un mensaje de error
    echo json_encode(array("error" => "Los campos 'userId' y 'motivo' son requeridos en la solicitud."));
}

$mysqli->close();
?>
