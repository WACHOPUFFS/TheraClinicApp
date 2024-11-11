<?php

// Incluir la configuración de la base de datos
include_once 'cors.php';
include_once 'conexion.php';

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

// Verificar si se recibieron los datos esperados
if (
    isset($data->nombreUsuario) &&
    isset($data->nombreCompleto) &&
    isset($data->correo) &&
    isset($data->numTelefonico) &&
    isset($data->contrasena) &&
    isset($data->rfc) &&
    isset($data->nombreEmpresa) &&
    isset($data->fechaInicio) &&
    isset($data->fechaFin)
) {
    // Escapar los datos para prevenir inyección SQL
    $nombreUsuario = $mysqli->real_escape_string($data->nombreUsuario);
    $nombreCompleto = $mysqli->real_escape_string($data->nombreCompleto);
    $correo = $mysqli->real_escape_string($data->correo);
    $numTelefonico = $mysqli->real_escape_string($data->numTelefonico);
    $contrasena = $mysqli->real_escape_string($data->contrasena);
    $rfc = $mysqli->real_escape_string($data->rfc);
    $nombreEmpresa = $mysqli->real_escape_string($data->nombreEmpresa);
    $fechaInicio = $mysqli->real_escape_string($data->fechaInicio);
    $fechaFin = $mysqli->real_escape_string($data->fechaFin);

    // Encriptar la contraseña (puedes utilizar un método más seguro en lugar de md5)
    $contrasenaHash = md5($contrasena);

    // Valor fijo para levelUser (siempre 'adminE = administrador unico de la empresa')
    $levelUser = 'adminE';
    $state = 'active';

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

    // Verificar si ya existe un usuario admin en la empresa
    $sqlCheckAdmin = "SELECT COUNT(*) as countAdmin FROM users u
    INNER JOIN user_company_roles ucr ON u.id = ucr.user_id
    INNER JOIN companies c ON ucr.company_id = c.id
    INNER JOIN levelUser lu ON ucr.levelUser_id = lu.id
    WHERE lu.levelUserName = 'adminE' AND c.nameCompany = '$nombreEmpresa'";

    $resultCheckAdmin = $mysqli->query($sqlCheckAdmin);
    $rowCheckAdmin = $resultCheckAdmin->fetch_assoc();

    if ($rowCheckAdmin['countAdmin'] > 0) {
        // Enviar una respuesta de error si ya existe un usuario admin en la empresa
        $response = array("success" => false, "message" => "Ya existe un usuario administrador en esta empresa.");
        echo json_encode($response);
        exit;
    }

    // Verificar si ya existe un registro para el levelUser
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
    $sqlCheckEmpresa = "SELECT id FROM companies WHERE rfc = ?";
    $stmtCheckEmpresa = $mysqli->prepare($sqlCheckEmpresa);
    $stmtCheckEmpresa->bind_param("s", $rfc);
    $stmtCheckEmpresa->execute();
    $stmtCheckEmpresa->store_result();

    if ($stmtCheckEmpresa->num_rows > 0) {
        // La empresa ya existe, obtener el ID de la empresa existente
        $stmtCheckEmpresa->bind_result($empresaId);
        $stmtCheckEmpresa->fetch();
    } else {
        // La empresa no existe, insertar la empresa y obtener el ID de la empresa recién insertada
        $sqlInsertEmpresa = "INSERT INTO companies (nameCompany, rfc) VALUES (?, ?)";
        $stmtInsertEmpresa = $mysqli->prepare($sqlInsertEmpresa);
        $stmtInsertEmpresa->bind_param("ss", $nombreEmpresa, $rfc);

        if ($stmtInsertEmpresa->execute()) {
            $empresaId = $stmtInsertEmpresa->insert_id;
        } else {
            // Enviar una respuesta de error si no se pudo insertar la empresa
            $response = array("success" => false, "message" => "Error al insertar la empresa: " . $mysqli->error);
            echo json_encode($response);
            exit;
        }
    }


    // Insertar el usuario
    $sqlInsertUsuario = "INSERT INTO users (username, name, phone, password, email, state) VALUES ('$nombreUsuario', '$nombreCompleto', '$numTelefonico', '$contrasenaHash', '$correo', 'active')";
    if ($mysqli->query($sqlInsertUsuario)) {
        $usuarioId = $mysqli->insert_id;

        // Insertar la relación entre el usuario y la empresa
        $sqlInsertRelacion = "INSERT INTO user_company_roles (user_id, company_id, principal, levelUser_id, status) VALUES ('$usuarioId', '$empresaId', '1', '$levelUserId', '2')";
        if ($mysqli->query($sqlInsertRelacion)) {
            // Insertar el periodo de actividad del usuario
            $sqlPeriodo = "INSERT INTO periodos (usuario_id, fecha_inicio, fecha_fin) VALUES ('$usuarioId', '$fechaInicio', '$fechaFin')";
            if ($mysqli->query($sqlPeriodo)) {
                // Generar y guardar el token
                $token = generarToken();
                if (guardarToken($mysqli, $correo, $token)) {
                    // Enviar una respuesta de éxito con el token
                    include_once 'enviar_correo.php';
                    enviarCorreo($correo, $token, 'confirmacion');

                    $response = array("success" => true, "message" => "¡Usuario y empresa registrados exitosamente!", "token" => $token);
                    echo json_encode($response);
                } else {
                    // Enviar una respuesta de error si no se pudo guardar el token
                    $response = array("success" => false, "message" => "Error al guardar el token en la base de datos.");
                    echo json_encode($response);
                }
            } else {
                // Enviar una respuesta de error si no se pudo insertar el periodo de actividad
                $response = array("success" => false, "message" => "Error al registrar el periodo de actividad: " . $mysqli->error);
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