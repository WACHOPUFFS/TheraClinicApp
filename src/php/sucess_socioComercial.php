<?php
include_once 'cors.php';
include_once 'conexion.php';
include_once 'enviar_correo.php'; // Incluir el archivo para enviar correos

// Función para enviar una respuesta JSON
function sendResponse($success, $message)
{
    echo json_encode(['success' => $success, 'message' => $message]);
    exit();
}

// Verificar si se recibieron datos mediante POST
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['userId'], $data['username'], $data['name'], $data['email'], $data['phone'], $data['nameCompany'], $data['rfc'], $data['fecha_inicio'], $data['fecha_fin'], $data['fecha_inicio_request'], $data['fecha_fin_request'], $data['folioSolicitud'])) {
    sendResponse(false, 'Datos incompletos.');
}

$userId = $data['userId'];
$username = $data['username'];
$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$nameCompany = $data['nameCompany'];
$rfc = $data['rfc'];
$levelUser = 'adminPE';
$fecha_inicio = $data['fecha_inicio'];
$fecha_fin = $data['fecha_fin'];
$fecha_inicio_request = $data['fecha_inicio_request'];
$fecha_fin_request = $data['fecha_fin_request'];
$folioSolicitud = $data['folioSolicitud'];

// Verificar si el usuario existe
$sql_check_user = "SELECT id FROM users WHERE id = ?";
$stmt_check_user = $mysqli->prepare($sql_check_user);
if ($stmt_check_user === false) {
    sendResponse(false, 'Error al preparar la consulta de verificación de usuario.');
}

$stmt_check_user->bind_param("i", $userId);
$stmt_check_user->execute();
$stmt_check_user->store_result();

if ($stmt_check_user->num_rows <= 0) {
    sendResponse(false, 'Usuario no encontrado.');
}

// El usuario existe, proceder con la actualización
$stmt_check_user->close();

// Actualizar usuario
$sql_update_user = "UPDATE users SET username = ?, name = ?, email = ?, phone = ? WHERE id = ?";
$stmt_update_user = $mysqli->prepare($sql_update_user);
if ($stmt_update_user === false) {
    sendResponse(false, 'Error al preparar la consulta de actualización de usuario.');
}

$stmt_update_user->bind_param("ssssi", $username, $name, $email, $phone, $userId);
if (!$stmt_update_user->execute()) {
    sendResponse(false, 'Error al ejecutar la consulta de actualización de usuario.');
}
$stmt_update_user->close();

// Actualizar nameCompany y rfc en la tabla companies
$sql_update_company = "UPDATE companies SET nameCompany = ?, rfc = ? WHERE id IN (SELECT company_id FROM user_company_roles WHERE user_id = ?)";
$stmt_update_company = $mysqli->prepare($sql_update_company);
if ($stmt_update_company === false) {
    sendResponse(false, 'Error al preparar la consulta de actualización de compañía: ' . $mysqli->error);
}

$stmt_update_company->bind_param("ssi", $nameCompany, $rfc, $userId);
if (!$stmt_update_company->execute()) {
    sendResponse(false, 'Error al ejecutar la consulta de actualización de compañía.');
}
$stmt_update_company->close();

// Verificar si el levelUser existe en la tabla levelUser
$sql_check_levelUser = "SELECT id FROM levelUser WHERE levelUserName = ?";
$stmt_check_levelUser = $mysqli->prepare($sql_check_levelUser);
if ($stmt_check_levelUser === false) {
    sendResponse(false, 'Error al preparar la consulta de verificación de levelUser.' . $mysqli->error);
}

$stmt_check_levelUser->bind_param("s", $levelUser);
$stmt_check_levelUser->execute();
$stmt_check_levelUser->bind_result($levelUser_id);
$stmt_check_levelUser->fetch();

if (!$levelUser_id) {
    // Si el levelUser no existe, insertarlo
    $sql_insert_levelUser = "INSERT INTO levelUser (levelUserName) VALUES (?)";
    $stmt_insert_levelUser = $mysqli->prepare($sql_insert_levelUser);
    if ($stmt_insert_levelUser === false) {
        sendResponse(false, 'Error al preparar la consulta de inserción de levelUser.' . $mysqli->error);
    }

    $stmt_insert_levelUser->bind_param("s", $levelUser);
    if ($stmt_insert_levelUser->execute()) {
        $levelUser_id = $stmt_insert_levelUser->insert_id;
    } else {
        sendResponse(false, 'Error al ejecutar la consulta de inserción de levelUser.');
    }

    $stmt_insert_levelUser->close();
}

$stmt_check_levelUser->close();

// Actualizar el levelUser_id en la tabla user_company_roles
$sql_update_levelUser = "UPDATE user_company_roles SET levelUser_id = ? WHERE user_id = ?";
$stmt_update_levelUser = $mysqli->prepare($sql_update_levelUser);
if ($stmt_update_levelUser === false) {
    sendResponse(false, 'Error al preparar la consulta de actualización de levelUser_id.');
}

$stmt_update_levelUser->bind_param("ii", $levelUser_id, $userId);
if (!$stmt_update_levelUser->execute()) {
    sendResponse(false, 'Error al ejecutar la consulta de actualización de levelUser_id.');
}
$stmt_update_levelUser->close();

// Actualizar periodos en la tabla periodosPremium
$sql_update_periodos = "INSERT INTO periodosPremium (fecha_inicio, fecha_fin, usuario_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE fecha_inicio = VALUES(fecha_inicio), fecha_fin = VALUES(fecha_fin)";
$stmt_update_periodos = $mysqli->prepare($sql_update_periodos);
if ($stmt_update_periodos === false) {
    sendResponse(false, 'Error al preparar la consulta de actualización de periodos.');
}

$stmt_update_periodos->bind_param("ssi", $fecha_inicio, $fecha_fin, $userId);
if (!$stmt_update_periodos->execute()) {
    sendResponse(false, 'Error al ejecutar la consulta de actualización de periodos.');
}
$stmt_update_periodos->close();

// Eliminar de la tabla premium_user_requests
$sql_delete_premium_request = "DELETE FROM premium_user_requests WHERE id = ?";
$stmt_delete_premium_request = $mysqli->prepare($sql_delete_premium_request);
if ($stmt_delete_premium_request === false) {
    sendResponse(false, 'Error al preparar la consulta de eliminación de premium_user_requests.');
}

$stmt_delete_premium_request->bind_param("i", $userId);
if (!$stmt_delete_premium_request->execute()) {
    sendResponse(false, 'Error al ejecutar la consulta de eliminación de premium_user_requests.');
}
$stmt_delete_premium_request->close();

// Insertar en la tabla user_requests_accept
$sql_insert_user_requests_accept = "INSERT INTO user_requests_accept (user_id, requested_at, accepted_at, requestFolio) VALUES (?, ?, ?, ?)";
$stmt_insert_user_requests_accept = $mysqli->prepare($sql_insert_user_requests_accept);
if ($stmt_insert_user_requests_accept === false) {
    sendResponse(false, 'Error al preparar la consulta de inserción en user_requests_accept.');
}

$stmt_insert_user_requests_accept->bind_param("isss", $userId, $fecha_inicio_request, $fecha_fin_request, $folioSolicitud);
if ($stmt_insert_user_requests_accept->execute()) {
    // Enviar correo
    enviarCorreo($email, null, 'SolicitudAceptada');
    sendResponse(true, 'Usuario, compañía y solicitudes premium actualizados correctamente, y solicitud aceptada registrada.');
} else {
    sendResponse(false, 'Error al ejecutar la consulta de inserción en user_requests_accept.');
}
$stmt_insert_user_requests_accept->close();

$mysqli->close();
?>