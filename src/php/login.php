<?php
// Incluir archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se recibieron datos del formulario de inicio de sesión
if (isset($_POST['username']) && isset($_POST['password'])) {
    // Sanitizar y escapar los datos del formulario
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $mysqli->real_escape_string($_POST['password']);

    // Calcular el hash MD5 de la contraseña
    $passwordHash = md5($password);

    // Consulta para verificar las credenciales del usuario en la base de datos y su estado de activación
    $query = "SELECT u.*, uc.role_id 
          FROM users u 
          LEFT JOIN user_company_roles uc ON u.id = uc.user_id 
          WHERE u.username = '$username' AND u.password = '$passwordHash' AND principal = '1'";
    $result = $mysqli->query($query);

    // Verificar si la consulta fue exitosa
    if ($result) {
        // Verificar si se encontró un usuario con las credenciales proporcionadas
        if ($result->num_rows == 1) {
            // Obtener la información del usuario
            $user = $result->fetch_assoc();
            $userId = $user['id'];
            $state = $user['state'];

            $companyQuery = "SELECT company_id FROM user_company_roles WHERE user_id = $userId AND principal IN ('0', '1')";
            $companyResult = $mysqli->query($companyQuery);
            if ($companyResult && $companyResult->num_rows > 0) {
                // Obtener el ID de la empresa
                $companyRow = $companyResult->fetch_assoc();
                $companyId = $companyRow['company_id'];

                // Verificar si el usuario tiene roles asignados
                if (!empty($user['role_id'])) {
                    // Verificar si la cuenta está activa
                    if ($state == 'active') {
                        // Consulta para obtener las empresas principales del usuario
                        $principalCompanyQuery = "SELECT c.nameCompany, c.rfc, r.roleName, l.levelUserName, uc.company_id
                        FROM companies c 
                        LEFT JOIN user_company_roles uc ON c.id = uc.company_id
                        LEFT JOIN roles r ON uc.role_id = r.id
                        LEFT JOIN levelUser l ON uc.levelUser_id = l.id
                        WHERE uc.user_id = $userId AND uc.principal IN ('0', '1')";
                        $principalCompanyResult = $mysqli->query($principalCompanyQuery);

                        // Crear arrays para almacenar las empresas principales y no principales del usuario
                        $principalCompanies = array();

                        // Iterar sobre los resultados y almacenar las empresas principales en el array correspondiente
                        while ($row = $principalCompanyResult->fetch_assoc()) {
                            $principalCompanies[] = array(
                                "idCompany" => $row['company_id'], // Utiliza company_id para obtener el id de la compañía
                                "nameCompany" => $row['nameCompany'],
                                "roleInCompany" => $row['roleName'],
                                "rfcIncompany" => $row['rfc'],      // Obtener el nivel de usuario
                                "levelUser" => $row['levelUserName']
                            );
                        }
                       
                        // Establecer una sesión y responder con éxito junto con las empresas, roles y nivel de usuario
                        $_SESSION['username'] = $username;
                        echo json_encode(
                            array(
                                "success" => true,
                                "message" => "Inicio de sesión exitoso.",
                                "userId" => $userId, // ID de usuario incluido en la respuesta
                                "principalCompanies" => $principalCompanies,
                            )
                        );
                    } else {
                        // La cuenta no está activa, responder con un mensaje de error
                        echo json_encode(array("success" => false, "message" => "La cuenta no está activa. Por favor, contacta al administrador."));
                    }
                } else {
                    if ($state == 'active') {
                        // Consulta para obtener las empresas principales del usuario
                        $principalCompanyQuery = "SELECT c.nameCompany, c.rfc, r.roleName, l.levelUserName, uc.company_id
                        FROM companies c 
                        LEFT JOIN user_company_roles uc ON c.id = uc.company_id
                        LEFT JOIN roles r ON uc.role_id = r.id
                        LEFT JOIN levelUser l ON uc.levelUser_id = l.id
                        WHERE uc.user_id = $userId AND uc.principal IN ('0', '1')";
                        $principalCompanyResult = $mysqli->query($principalCompanyQuery);

                        // Crear arrays para almacenar las empresas principales y no principales del usuario
                        $principalCompanies = array();

                        // Iterar sobre los resultados y almacenar las empresas principales en el array correspondiente
                        while ($row = $principalCompanyResult->fetch_assoc()) {
                            $principalCompanies[] = array(
                                "idCompany" => $row['company_id'], // Utiliza company_id para obtener el id de la compañía
                                "nameCompany" => $row['nameCompany'],
                                "roleInCompany" => $row['roleName'],
                                "rfcIncompany" => $row['rfc'],      // Obtener el nivel de usuario
                                "levelUser" => $row['levelUserName']
                            );
                        }
           
                        // Establecer una sesión y responder con éxito junto con las empresas, roles y nivel de usuario
                        $_SESSION['username'] = $username;
                        echo json_encode(
                            array(
                                "success" => true,
                                "message" => "Inicio de sesión exitoso.",
                                "userId" => $userId, // ID de usuario incluido en la respuesta
                                "principalCompanies" => $principalCompanies,
                            )
                        );
                    } else {
                        // La cuenta no está activa, responder con un mensaje de error
                        echo json_encode(array("success" => false, "message" => "La cuenta no está activa. Por favor, contacta al administrador."));
                    }
                }
            }
        } else {
            // Las credenciales son inválidas, responder con un mensaje de error
            echo json_encode(array("success" => false, "message" => "Nombre de usuario o contraseña incorrectos."));
        }
    } else {
        // Ocurrió un error al ejecutar la consulta, responder con un mensaje de error
        echo json_encode(array("success" => false, "message" => "Error al ejecutar la consulta."));
    }
} else {
    // No se recibieron datos del formulario, responder con un mensaje de error
    echo json_encode(array("success" => false, "message" => "Datos de inicio de sesión no recibidos."));
}

$mysqli->close();
?>