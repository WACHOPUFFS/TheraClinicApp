<?php

// Incluir la configuración de la base de datos
include_once 'cors.php';
include_once 'conexion.php';
include_once 'enviar_correo.php'; // Incluir la función para enviar correos

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



// Función para buscar el correo del administrador de la empresa por RFC
function buscarCorreoAdminPorRFC($mysqli, $rfc)
{
    // Escapar el RFC para prevenir inyección SQL
    $rfcEscapado = $mysqli->real_escape_string($rfc);

    // Consulta SQL para obtener el correo del administrador
    $sql = "SELECT u.email 
            FROM users u
            JOIN user_company_roles ucr ON u.id = ucr.user_id
            JOIN companies c ON ucr.company_id = c.id
            WHERE c.rfc = '$rfcEscapado' 
            AND ucr.levelUser_id IN (
                SELECT id 
                FROM levelUser 
                WHERE levelUserName IN ('adminS', 'adminE', 'adminEE', 'adminPE')
            )";

    // Ejecutar la consulta SQL
    $result = $mysqli->query($sql);

    // Verificar si se encontró un resultado
    if ($result->num_rows > 0) {
        // Obtener el correo del resultado
        $row = $result->fetch_assoc();
        return $row['email'];
    } else {
        // No se encontró un administrador para el RFC proporcionado
        return null;
    }
}


// Función para enviar correo al administrador sobre el nuevo registro
function enviarCorreoAdmin($correoAdmin, $nombreEmpresa, $nombreUsuario, $nombreCompleto, $correoUsuario)
{
    $mensaje = "Hola,\n\n" .
               "¡Tenemos buenas noticias! Un nuevo usuario se ha registrado en tu empresa '$nombreEmpresa'.\n\n" .
               "Aquí tienes los detalles del nuevo registro:\n" .
               "Nombre de usuario: $nombreUsuario\n" .
               "Nombre completo: $nombreCompleto\n" .
               "Correo electrónico: $correoUsuario\n\n" .
               "Si no reconoces este usuario, por favor elimínalo en el panel de registros de usuarios.\n\n" .
               "¡Gracias por ser parte de nuestra comunidad!\n\n" .
               "Saludos cordiales,\n" .
               "El equipo de $nombreEmpresa.";

    // Usar la función enviarCorreo para enviar el correo
    enviarCorreo($correoAdmin, $mensaje, 'AvisoRegistro');
}

// Obtener los datos del cuerpo de la solicitud POST
$data = json_decode(file_get_contents("php://input"));

// Verificar si se recibieron los datos esperados
if (
    isset($data->association_user_id) &&
    isset($data->nombreUsuario) &&
    isset($data->nombreCompleto) &&
    isset($data->correo) &&
    isset($data->contrasena) &&
    isset($data->rfcEmpresa) &&
    isset($data->nombreEmpresa) &&
    isset($data->levelUser)
) {
    // Escapar los datos para prevenir inyección SQL
    $association_user_id = $mysqli->real_escape_string($data->association_user_id);
    $nombreUsuario = $mysqli->real_escape_string($data->nombreUsuario);
    $nombreCompleto = $mysqli->real_escape_string($data->nombreCompleto);
    $correo = $mysqli->real_escape_string($data->correo);
    $contrasena = $mysqli->real_escape_string($data->contrasena);
    $rfc = $mysqli->real_escape_string($data->rfcEmpresa);
    $nombreEmpresa = $mysqli->real_escape_string($data->nombreEmpresa);
    $levelUser = $mysqli->real_escape_string($data->levelUser);

    // Encriptar la contraseña (puedes utilizar un método más seguro en lugar de md5)
    $contrasenaHash = md5($contrasena);

    // Verificar si el correo electrónico ya existe y si está confirmado
    $sqlCheckEmail = "SELECT id, confirmed FROM users WHERE email = ?";
    $stmtCheckEmail = $mysqli->prepare($sqlCheckEmail);
    $stmtCheckEmail->bind_param("s", $correo);
    $stmtCheckEmail->execute();
    $stmtCheckEmail->store_result();

    if ($stmtCheckEmail->num_rows > 0) {
        // El correo electrónico ya está registrado
        $stmtCheckEmail->bind_result($userId, $confirmed);
        $stmtCheckEmail->fetch();

        if ($confirmed == 1) {
            // El correo electrónico está registrado y confirmado, permitir su uso
            // Proceder con el registro del usuario si es necesario...
        } else {
            // El correo electrónico está registrado pero no confirmado, enviar una respuesta de error
            $response = array("success" => false, "message" => "El correo electrónico está registrado pero no confirmado.");
            echo json_encode($response);
            exit;
        }
    } else {
        // El correo electrónico no está registrado, permitir su uso
        // Proceder con el registro del usuario si es necesario...
    }

    // Verificar si el nombre de usuario ya existe
    $sqlCheckUsername = "SELECT id FROM users WHERE username = '$nombreUsuario'";
    $resultCheckUsername = $mysqli->query($sqlCheckUsername);
    if ($resultCheckUsername->num_rows > 0) {
        // Enviar una respuesta de error si el nombre de usuario ya existe
        $response = array("success" => false, "message" => "El nombre de usuario ya está en uso.");
        echo json_encode($response);
        exit;
    }

    $sqlCheckLevelUser = "SELECT id FROM levelUser WHERE levelUserName = '$levelUser'";
    $resultCheckLevelUser = $mysqli->query($sqlCheckLevelUser);
    if ($resultCheckLevelUser->num_rows > 0) {
        // El levelUser ya existe, obtener su ID
        $rowLevelUser = $resultCheckLevelUser->fetch_assoc();
        $levelUserId = $rowLevelUser['id'];
    } else {
        // El levelUser no existe, insertarlo y obtener el ID recién insertado
        $sqlInsertLevelUser = "INSERT INTO levelUser (levelUserName) VALUES ('$levelUser')";
        if ($mysqli->query($sqlInsertLevelUser)) {
            $levelUserId = $mysqli->insert_id;
        } else {
            // Enviar una respuesta de error si no se pudo insertar el levelUser
            $response = array("success" => false, "message" => "Error al insertar el nivel de usuario: " . $mysqli->error);
            echo json_encode($response);
            exit;
        }
    }

    // Verificar si la empresa ya existe
    $sqlCheckEmpresa = "SELECT id FROM companies WHERE nameCompany = '$nombreEmpresa'";
    $resultCheckEmpresa = $mysqli->query($sqlCheckEmpresa);

    if ($resultCheckEmpresa->num_rows > 0) {
        // La empresa ya existe, obtener el ID de la empresa
        $rowEmpresa = $resultCheckEmpresa->fetch_assoc();
        $empresaId = $rowEmpresa['id'];
    } else {
        // La empresa no existe, insertar la empresa y obtener el ID de la empresa recién insertada
        $sqlInsertEmpresa = "INSERT INTO companies (nameCompany, rfc) VALUES ('$nombreEmpresa', '$rfc')";
        if ($mysqli->query($sqlInsertEmpresa)) {
            $empresaId = $mysqli->insert_id;
        } else {
            // Enviar una respuesta de error si no se pudo insertar la empresa
            $response = array("success" => false, "message" => "Error al insertar la empresa: " . $mysqli->error);
            echo json_encode($response);
            exit;
        }
    }

    // Insertar el usuario
    $sqlInsertUsuario = "INSERT INTO users (username, name, password, email, state) VALUES ('$nombreUsuario', '$nombreCompleto', '$contrasenaHash', '$correo', 'active')";
    if ($mysqli->query($sqlInsertUsuario)) {
        $usuarioId = $mysqli->insert_id;

        // Buscar el periodo del usuario al que se está asociando
        $sqlBuscarPeriodo = "SELECT fecha_inicio, fecha_fin FROM periodos WHERE usuario_id = '$association_user_id'";
        $resultadoBuscarPeriodo = $mysqli->query($sqlBuscarPeriodo);

        if ($resultadoBuscarPeriodo->num_rows > 0) {
            // Si se encuentra un registro, obtener el periodo
            $rowPeriodo = $resultadoBuscarPeriodo->fetch_assoc();
            $fecha_inicio = $rowPeriodo['fecha_inicio'];
            $fecha_fin = $rowPeriodo['fecha_fin'];

            // Insertar un nuevo registro en la tabla periodos
            $sqlInsertarPeriodo = "INSERT INTO periodos (fecha_inicio, fecha_fin, usuario_id, association_user_id) VALUES ('$fecha_inicio', '$fecha_fin', '$usuarioId', '$association_user_id')";
            if (!$mysqli->query($sqlInsertarPeriodo)) {
                // Error al insertar el nuevo periodo
                $response = array("success" => false, "message" => "Error al insertar el nuevo periodo: " . $mysqli->error);
                echo json_encode($response);
                exit;
            }
        } else {
            // No se encontró un periodo para el usuario al que se está asociando
            $response = array("success" => false, "message" => "No se encontró un periodo para el usuario al que se está asociando.");
            echo json_encode($response);
            exit;
        }

        // Insertar la relación entre el usuario y la empresa
        $sqlInsertRelacion = "INSERT INTO user_company_roles (user_id, company_id, principal, levelUser_id, status) VALUES ('$usuarioId', '$empresaId', '1', '$levelUserId', '2')";
        if ($mysqli->query($sqlInsertRelacion)) {
            // Enviar una respuesta de éxito
            $token = generarToken();
            if (guardarToken($mysqli, $correo, $token)) {
                // Enviar una respuesta de éxito con el token
                enviarCorreo($correo, $token, 'confirmacion');

                // Buscar el correo del administrador
                $correoAdmin = buscarCorreoAdminPorRFC($mysqli, $rfc);
                if ($correoAdmin) {
                    // Enviar un correo al administrador
                    enviarCorreoAdmin($correoAdmin, $nombreUsuario, $nombreCompleto, $correo);
                }

                $response = array("success" => true, "message" => "¡Usuario y empresa registrados exitosamente!", "token" => $token);
                echo json_encode($response);
            } else {
                // Enviar una respuesta de error si no se pudo guardar el token
                $response = array("success" => false, "message" => "Error al guardar el token en la base de datos.");
                echo json_encode($response);
            }
        } else {
            // Enviar una respuesta de error si no se pudo insertar la relación
            $response = array("success" => false, "message" => "Error al registrar relación usuario-empresa: " . $mysqli->error);
            echo json_encode($response);
        }
    } else {
        // Enviar una respuesta de error si no se pudo insertar el usuario
        $response = array("success" => false, "message" => "Error al registrar usuario: " . $mysqli->error);
        echo json_encode($response);
    }
} else {
    // Enviar una respuesta de error si no se recibieron los datos esperados
    $response = array("success" => false, "message" => "Proporciona todos los datos necesarios para el registro.");
    echo json_encode($response);
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>