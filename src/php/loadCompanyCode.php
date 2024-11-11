<?php

// Incluir la configuración de la base de datos
include_once 'cors.php'; // Asegúrate de tener configurado correctamente CORS para permitir solicitudes desde tu frontend
include_once 'conexion.php'; // Incluye tu archivo de conexión a la base de datos

// Obtener los datos del cuerpo de la solicitud POST en formato JSON
$json = file_get_contents('php://input');

// Decodificar los datos JSON en un array asociativo
$data = json_decode($json, true);

// Verificar si se recibió el ID de la empresa en la solicitud
if (isset($data['companyId'])) {
    // Obtener y sanitizar el ID de la empresa de la solicitud
    $companyId = intval($data['companyId']);

    // Consulta SQL para verificar si la empresa ya tiene un código asignado
    $sql_check = "SELECT * FROM company_codes WHERE company_id = ?";
    $stmt_check = $mysqli->prepare($sql_check);
    $stmt_check->bind_param("i", $companyId);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Si la empresa ya tiene un código asignado, actualizar el código
        $codigoUnico = uniqid();
        $sql_update = "UPDATE company_codes SET code = ? WHERE company_id = ?";
        $stmt_update = $mysqli->prepare($sql_update);
        $stmt_update->bind_param("si", $codigoUnico, $companyId);

        // Ejecutar la consulta SQL de actualización
        if ($stmt_update->execute()) {
            // Enviar el nuevo código de la empresa como respuesta al cliente
            $response = array("success" => true, "codigoEmpresa" => $codigoUnico);
            echo json_encode($response);
        } else {
            // Error al actualizar el código, enviar respuesta de error al cliente
            $response = array("success" => false, "message" => "Error al actualizar el código: " . $stmt_update->error);
            echo json_encode($response);
        }
        $stmt_update->close();
    } else {
        // Si la empresa no tiene un código asignado, generar uno nuevo y guardarlo
        $codigoUnico = uniqid();
        $sql_insert = "INSERT INTO company_codes (company_id, code, created_at) VALUES (?, ?, NOW())";
        $stmt_insert = $mysqli->prepare($sql_insert);
        $stmt_insert->bind_param("is", $companyId, $codigoUnico);

        // Ejecutar la consulta SQL de inserción
        if ($stmt_insert->execute()) {
            // Enviar el código de la empresa como respuesta al cliente
            $response = array("success" => true, "codigoEmpresa" => $codigoUnico);
            echo json_encode($response);
        } else {
            // Error al insertar el código, enviar respuesta de error al cliente
            $response = array("success" => false, "message" => "Error al generar y guardar el código único: " . $stmt_insert->error);
            echo json_encode($response);
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
} else {
    // Enviar una respuesta de error si no se recibió el ID de la empresa en la solicitud
    $response = array("success" => false, "message" => "No se proporcionó el ID de la empresa en la solicitud.");
    echo json_encode($response);
}

// Cerrar la conexión a la base de datos
$mysqli->close();

?>
