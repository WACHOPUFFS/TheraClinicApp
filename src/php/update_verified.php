<?php
include_once 'cors.php';
include_once 'conexion.php';

// Verificar si se recibieron datos mediante POST
$data = json_decode(file_get_contents("php://input"), true);

// Verificar si se recibió el campo userId
if(isset($data['userId']) && isset($data['companyId'])) {
    // Obtener los datos enviados por la solicitud HTTP
    $userId = $data['userId'];
    $companyId2 = $data['companyId'];

    // Obtener el company_id del usuario seleccionado
    $companyQuery = "SELECT company_id FROM user_company_roles WHERE user_id = '$userId'";
    $companyResult = $mysqli->query($companyQuery);

    if ($companyResult && $companyResult->num_rows > 0) {
        $row = $companyResult->fetch_assoc();
        $companyId = $row['company_id'];

        // Actualizar el status del usuario en la tabla user_company_roles a 1
        $updateQuery = "UPDATE user_company_roles SET status = '1' WHERE user_id = '$userId'";
        if ($mysqli->query($updateQuery) === TRUE) {
            // Actualizar el campo 'verified' del usuario en la tabla 'users' a 1
            $userUpdateQuery = "UPDATE users SET verified = '1' WHERE id = '$userId'";
            if ($mysqli->query($userUpdateQuery) === TRUE) {
                // Actualizar el status de los empleados asociados al companyId y companyId2
                $associationUpdateQuery = "UPDATE user_company_roles SET status = '1' WHERE association_id = '$companyId' AND company_id = '$companyId2'";
                if ($mysqli->query($associationUpdateQuery) === TRUE) {
                    // Si todas las actualizaciones se realizan con éxito, devolver una respuesta exitosa
                    echo json_encode(array("success" => true));
                } else {
                    // Si hay algún error al actualizar el status de los empleados asociados, devolver un mensaje de error
                    echo json_encode(array("error" => "Error al actualizar el status de los empleados asociados: " . $mysqli->error));
                }
            } else {
                // Si hay algún error al actualizar el campo 'verified' del usuario, devolver un mensaje de error
                echo json_encode(array("error" => "Error al actualizar el campo 'verified' del usuario: " . $mysqli->error));
            }
        } else {
            // Si hay algún error al actualizar el status del usuario, devolver un mensaje de error
            echo json_encode(array("error" => "Error al actualizar el status del usuario: " . $mysqli->error));
        }
    } else {
        // Si no se encontró el company_id del usuario, devolver un mensaje de error
        echo json_encode(array("error" => "No se encontró el company_id del usuario."));
    }
} else {
    // Si algún campo requerido no está presente en la solicitud, devolver un mensaje de error
    echo json_encode(array("error" => "Los campos 'userId', 'companyId' y 'companyId2' son requeridos en la solicitud."));
}

$mysqli->close();
?>
