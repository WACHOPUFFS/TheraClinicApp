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
    isset($data->fechaAutorizacion) &&
    isset($data->registroAutorizacion) &&
    isset($data->actividades)
) {
    // Escapar los datos para prevenir inyección SQL
    $userId = $mysqli->real_escape_string($data->userId);
    $companyId = $mysqli->real_escape_string($data->companyId);
    $tareaId = $mysqli->real_escape_string($data->tareaId);
    $fechaAutorizacion = $mysqli->real_escape_string($data->fechaAutorizacion);
    $registroAutorizacion = $mysqli->real_escape_string($data->registroAutorizacion);

    // Verificar si ya existe un registro con userId, companyId y tareaId
    $sqlCheck = "SELECT * FROM autorizacion_stps WHERE userId = '$userId' AND companyId = '$companyId' AND tareaId = '$tareaId'";
    $result = $mysqli->query($sqlCheck);

    if ($result->num_rows > 0) {
        // Actualizar el registro existente
        $sqlUpdate = "UPDATE autorizacion_stps 
                      SET fechaAutorizacion = '$fechaAutorizacion', registroAutorizacion = '$registroAutorizacion' 
                      WHERE userId = '$userId' AND companyId = '$companyId' AND tareaId = '$tareaId'";

        if (!$mysqli->query($sqlUpdate)) {
            $response["success"] = false;
            $response["message"] = "Error al actualizar los datos: " . $mysqli->error;
            echo json_encode($response);
            exit;
        }

        // Borrar actividades existentes para reinsertar nuevas
        $sqlDelete = "DELETE FROM actividades_stps WHERE autorizacion_id IN (SELECT id FROM autorizacion_stps WHERE userId = '$userId' AND companyId = '$companyId' AND tareaId = '$tareaId')";
        $mysqli->query($sqlDelete);
    } else {
        // Insertar un nuevo registro
        $sqlInsert = "INSERT INTO autorizacion_stps (userId, companyId, tareaId, fechaAutorizacion, registroAutorizacion) 
                      VALUES ('$userId', '$companyId', '$tareaId', '$fechaAutorizacion', '$registroAutorizacion')";

        if (!$mysqli->query($sqlInsert)) {
            $response["success"] = false;
            $response["message"] = "Error al guardar los datos: " . $mysqli->error;
            echo json_encode($response);
            exit;
        }
    }

    // Obtener el ID del registro de autorizacion_stps (ya sea insertado o actualizado)
    $autorizacionId = $mysqli->insert_id ?: $result->fetch_assoc()['id'];

    // Insertar actividades
    foreach ($data->actividades as $actividad) {
        $actividadNombre = $mysqli->real_escape_string($actividad->actividad);
        $folio = $mysqli->real_escape_string($actividad->folio);

        $sqlInsert = "INSERT INTO actividades_stps (autorizacion_id, actividad, folio) 
                      VALUES ('$autorizacionId', '$actividadNombre', '$folio')";

        if (!$mysqli->query($sqlInsert)) {
            $response["success"] = false;
            $response["message"] = "Error al guardar las actividades: " . $mysqli->error;
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
