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
    isset($data->noPatronales)
) {
    // Escapar los datos para prevenir inyección SQL
    $userId = $mysqli->real_escape_string($data->userId);
    $companyId = $mysqli->real_escape_string($data->companyId);
    $tareaId = $mysqli->real_escape_string($data->tareaId);

    // Verificar si ya existe un registro con userId, companyId y tareaId
    $sqlCheck = "SELECT * FROM afil01 WHERE userId = '$userId' AND companyId = '$companyId' AND tareaId = '$tareaId'";
    $result = $mysqli->query($sqlCheck);

    if ($result->num_rows > 0) {
        // Borrar registros existentes para reinsertar nuevos datos
        $sqlDelete = "DELETE FROM afil01 WHERE userId = '$userId' AND companyId = '$companyId' AND tareaId = '$tareaId'";
        $mysqli->query($sqlDelete);
    }

    // Insertar nuevos datos
    foreach ($data->noPatronales as $patronal) {
        $noPatronal = $mysqli->real_escape_string($patronal->noPatronal);
        $noRegPatronal = $mysqli->real_escape_string($patronal->noRegPatronal);
        $division = $mysqli->real_escape_string($patronal->division);
        $grupo = $mysqli->real_escape_string($patronal->grupo);
        $fraccion = $mysqli->real_escape_string($patronal->fraccion);
        $clase = $mysqli->real_escape_string($patronal->clase);
        $actividad = $mysqli->real_escape_string($patronal->actividad);
        $descripcion = $mysqli->real_escape_string($patronal->descripcion);

        $sqlInsert = "INSERT INTO afil01 (userId, companyId, tareaId, noPatronal, noRegPatronal, division, grupo, fraccion, clase, actividad, descripcion) 
                      VALUES ('$userId', '$companyId', '$tareaId', '$noPatronal', '$noRegPatronal', '$division', '$grupo', '$fraccion', '$clase', '$actividad', '$descripcion')";

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
