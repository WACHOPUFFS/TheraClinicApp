<?php

// Incluir la configuración de la base de datos
include_once 'cors.php';
include_once 'conexion.php';

// Obtener los datos del cuerpo de la solicitud POST
$data = json_decode(file_get_contents("php://input"));

// Inicializar la respuesta
$response = array();

// Verificar si se recibieron los datos esperados
if (
    isset($data->userId) &&
    isset($data->companyId) &&
    isset($data->tareaId) &&
    isset($data->domicilioSocial) &&
    isset($data->nombreRepresentante) &&
    isset($data->numeroEscritura) &&
    isset($data->folioMercantil) &&
    isset($data->fechaConstitucion) &&
    isset($data->nombreNotario) &&
    isset($data->nombreNotaria) &&
    isset($data->lugarFacultad) &&
    isset($data->seguroSocial)
) {
    // Escapar los datos para prevenir inyección SQL
    $userId = $mysqli->real_escape_string($data->userId);
    $companyId = $mysqli->real_escape_string($data->companyId);
    $tareaId = $mysqli->real_escape_string($data->tareaId);
    $domicilioSocial = $mysqli->real_escape_string($data->domicilioSocial);
    $nombreRepresentante = $mysqli->real_escape_string($data->nombreRepresentante);
    $numeroEscritura = $mysqli->real_escape_string($data->numeroEscritura);
    $folioMercantil = $mysqli->real_escape_string($data->folioMercantil);
    $fechaConstitucion = $mysqli->real_escape_string($data->fechaConstitucion);
    $nombreNotario = $mysqli->real_escape_string($data->nombreNotario);
    $nombreNotaria = $mysqli->real_escape_string($data->nombreNotaria);
    $lugarFacultad = $mysqli->real_escape_string($data->lugarFacultad);
    $seguroSocial = $mysqli->real_escape_string($data->seguroSocial);

    // Verificar si ya existe un registro con userId, companyId y tareaId
    $sqlCheck = "SELECT * FROM actas_constitutivas WHERE userId = '$userId' AND companyId = '$companyId' AND tareaId = '$tareaId'";
    $result = $mysqli->query($sqlCheck);

    if ($result->num_rows > 0) {
        // Actualizar el registro existente
        $sqlUpdate = "UPDATE actas_constitutivas 
                      SET domicilioSocial = '$domicilioSocial', nombreRepresentante = '$nombreRepresentante', numeroEscritura = '$numeroEscritura', folioMercantil = '$folioMercantil', fechaConstitucion = '$fechaConstitucion', nombreNotario = '$nombreNotario', nombreNotaria = '$nombreNotaria', lugarFacultad = '$lugarFacultad', seguroSocial = '$seguroSocial' 
                      WHERE userId = '$userId' AND companyId = '$companyId' AND tareaId = '$tareaId'";

        if ($mysqli->query($sqlUpdate)) {
            $response["success"] = true;
            $response["message"] = "Datos actualizados exitosamente";
        } else {
            $response["success"] = false;
            $response["message"] = "Error al actualizar los datos: " . $mysqli->error;
        }
    } else {
        // Insertar un nuevo registro
        $sqlInsert = "INSERT INTO actas_constitutivas (userId, companyId, tareaId, domicilioSocial, nombreRepresentante, numeroEscritura, folioMercantil, fechaConstitucion, nombreNotario, nombreNotaria, lugarFacultad, seguroSocial) 
                      VALUES ('$userId', '$companyId', '$tareaId', '$domicilioSocial', '$nombreRepresentante', '$numeroEscritura', '$folioMercantil', '$fechaConstitucion', '$nombreNotario', '$nombreNotaria', '$lugarFacultad', '$seguroSocial')";

        if ($mysqli->query($sqlInsert)) {
            $response["success"] = true;
            $response["message"] = "Datos guardados exitosamente";
        } else {
            $response["success"] = false;
            $response["message"] = "Error al guardar los datos: " . $mysqli->error;
        }
    }
} else {
    // No se recibieron todos los datos esperados
    $response["success"] = false;
    $response["message"] = "Faltan datos en la solicitud";
}

// Devolver la respuesta en formato JSON
echo json_encode($response);

$mysqli->close();
?>
