<?php

// Incluir la configuración de la base de datos
include_once 'cors.php';
include_once 'conexion.php';

// Función para generar un token único (puedes implementarla de acuerdo a tus necesidades)
function generarToken()
{
    $token = md5(uniqid(rand(), true));
    return $token;
}

// Función para guardar el token en la base de datos
function guardarToken($mysqli, $correo, $token)
{
    // Escapar el correo para prevenir inyección SQL
    $correoEscapado = $mysqli->real_escape_string($correo);

    // Consulta SQL para actualizar el token en la tabla de usuarios
    $sql = "UPDATE users SET token = '$token' WHERE email = '$correoEscapado'";

    // Ejecutar la consulta SQL
    if ($mysqli->query($sql)) {
        // El token se ha guardado correctamente en la base de datos
        return true;
    } else {
        // Error al guardar el token en la base de datos
        return false;
    }
}

// Obtener los datos del cuerpo de la solicitud POST
$data = json_decode(file_get_contents("php://input"));

// Inicializar la respuesta
$response = array();

// Verificar si se recibieron los datos esperados
if (
    isset($data->idUser) &&
    isset($data->nombreUsuario) &&
    isset($data->nombreCompleto) &&
    isset($data->correo) &&
    isset($data->numTelefonico) &&
    isset($data->rfc) &&
    isset($data->nombreEmpresa) &&
    isset($data-> fechaInicioRequest) && 
    isset($data->folioSolicitud)
) {
    // Escapar los datos para prevenir inyección SQL
    $idUser = $mysqli->real_escape_string($data->idUser);
    $nombreUsuario = $mysqli->real_escape_string($data->nombreUsuario);
    $nombreCompleto = $mysqli->real_escape_string($data->nombreCompleto);
    $correo = $mysqli->real_escape_string($data->correo);
    $numTelefonico = $mysqli->real_escape_string($data->numTelefonico);
    $rfc = $mysqli->real_escape_string($data->rfc);
    $nombreEmpresa = $mysqli->real_escape_string($data->nombreEmpresa);
    $fechaInicioRequest = $mysqli->real_escape_string($data->fechaInicioRequest);
    $folioSolicitud = $mysqli->real_escape_string($data->folioSolicitud);

    // Verificar si el idUser ya ha realizado una solicitud
    $sqlCheck = "SELECT * FROM premium_user_requests WHERE id = '$idUser'";
    $resultCheck = $mysqli->query($sqlCheck);

    if ($resultCheck->num_rows > 0) {
        // El idUser ya ha realizado una solicitud
        $response["success"] = false;
        $response["message"] = "Ya has hecho una solicitud. No puedes realizar otra hasta que revisen la anterior.";
    } else {
        // El idUser no ha realizado una solicitud, proceder a insertar la nueva solicitud
        $sqlInsert = "INSERT INTO premium_user_requests (id, username, name, email, phone, nameCompany, rfc, fecha_inicio_request, requestFolio) 
            VALUES ('$idUser', '$nombreUsuario', '$nombreCompleto', '$correo', '$numTelefonico', '$nombreEmpresa', '$rfc', '$fechaInicioRequest', '$folioSolicitud')";

        // Ejecutar la consulta SQL
        if ($mysqli->query($sqlInsert)) {
            include_once 'enviar_correo.php';
            enviarCorreo($correo, null, 'avisoSolicitud');
            // Los datos se han guardado correctamente en la base de datos
            $response["success"] = true;
            $response["message"] = "¡Solicitud de membresía Premium enviada, espera información por correo electrónico para más detalles!";
        } else {
            // Error al guardar los datos en la base de datos
            $response["success"] = false;
            $response["message"] = "Error al procesar la solicitud";
        }
    }
} else {
    // No se recibieron todos los datos esperados
    $response["success"] = false;
    $response["message"] = "Faltan datos en la solicitud";
}

// Devolver la respuesta en formato JSON
echo json_encode($response);

?>
