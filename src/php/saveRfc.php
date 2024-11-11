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
    isset($data->rfcInformacion)
) {
    // Escapar los datos para prevenir inyección SQL
    $userId = $mysqli->real_escape_string($data->userId);
    $companyId = $mysqli->real_escape_string($data->companyId);
    $tareaId = $mysqli->real_escape_string($data->tareaId);

    // Verificar si ya existe un registro con userId, companyId y tareaId
    $sqlCheck = "SELECT * FROM rfc WHERE userId = '$userId' AND companyId = '$companyId' AND tareaId = '$tareaId'";
    $result = $mysqli->query($sqlCheck);

    if ($result->num_rows > 0) {
        // Borrar registros existentes para reinsertar nuevos datos
        $sqlDelete = "DELETE FROM rfc WHERE userId = '$userId' AND companyId = '$companyId' AND tareaId = '$tareaId'";
        $mysqli->query($sqlDelete);
    }

    // Insertar nuevos datos
    foreach ($data->rfcInformacion as $rfcInfo) {
        $rfc = $mysqli->real_escape_string($rfcInfo->rfc);
        $curp = $mysqli->real_escape_string($rfcInfo->curp);
        $domicilioFiscal = $mysqli->real_escape_string($rfcInfo->domicilioFiscal);
        $tipoRegimen = $mysqli->real_escape_string($rfcInfo->tipoRegimen);
        $regimen = $mysqli->real_escape_string($rfcInfo->regimen);

        $sqlInsert = "INSERT INTO rfc (userId, companyId, tareaId, rfc, curp, domicilioFiscal, tipoRegimen, regimen) 
                      VALUES ('$userId', '$companyId', '$tareaId', '$rfc', '$curp', '$domicilioFiscal', '$tipoRegimen', '$regimen')";

        if (!$mysqli->query($sqlInsert)) {
            $response["success"] = false;
            $response["message"] = "Error al guardar los datos: " . $mysqli->error;
            echo json_encode($response);
            exit;
        }
    }

    $response["success"] = true;
    $response["message"] = "Datos guardados exitosamente";
} else {
    // No se recibieron todos los datos esperados
    $response["success"] = false;
    $response["message"] = "Faltan datos en la solicitud";
}

// Devolver la respuesta en formato JSON
echo json_encode($response);

$mysqli->close();
?>
