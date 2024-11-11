<?php
include_once 'cors.php';
include_once 'conexion.php';
include_once 'enviar_correo.php'; // Incluir el archivo enviar_correo.php

// Verificar si se recibieron datos mediante POST
$data = json_decode(file_get_contents("php://input"), true);

// Verificar si se recibieron todos los campos requeridos
if (isset($data['userId']) && isset($data['companyId']) && isset($data['motivo'])) {
    // Obtener el ID de usuario seleccionado, el ID de la empresa y el motivo de rechazo desde los datos enviados por la solicitud HTTP
    $userId = $data['userId'];
    $companyId2 = $data['companyId'];
    $motivo = $data['motivo']; // Obtener el motivo de rechazo

    // Consultar el ID de la empresa asociada al usuario
    $companyQuery = "SELECT company_id FROM user_company_roles WHERE user_id = ?";
    if ($stmt = $mysqli->prepare($companyQuery)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($companyId);
        if ($stmt->fetch()) {
            $stmt->close();

            // Eliminar la entrada en user_company_roles correspondiente a la asociación entre el usuario y la empresa
            $deleteUserCompanyRolesQuery = "DELETE FROM user_company_roles WHERE user_id = ? AND company_id = ?";
            if ($stmt = $mysqli->prepare($deleteUserCompanyRolesQuery)) {
                $stmt->bind_param("ii", $userId, $companyId2);
                if ($stmt->execute()) {
                    $stmt->close();

                    // Eliminar las entradas en user_company_roles donde association_id sea igual a $companyId y company_id sea igual a $companyId2
                    $deleteAssociationQuery = "DELETE FROM user_company_roles WHERE association_id = ? AND company_id = ?";
                    if ($stmt = $mysqli->prepare($deleteAssociationQuery)) {
                        $stmt->bind_param("ii", $companyId, $companyId2);
                        if ($stmt->execute()) {
                            $stmt->close();

                            // Consultar el ID del usuario que registró al usuario eliminado
                            $createdByQuery = "SELECT created_by FROM users WHERE id = ?";
                            if ($stmt = $mysqli->prepare($createdByQuery)) {
                                $stmt->bind_param("i", $userId);
                                $stmt->execute();
                                $stmt->bind_result($createdByUserId);
                                if ($stmt->fetch()) {
                                    $stmt->close();

                                    // Consultar el correo electrónico del usuario que registró al usuario eliminado
                                    $createdByEmailQuery = "SELECT email FROM users WHERE id = ?";
                                    if ($stmt = $mysqli->prepare($createdByEmailQuery)) {
                                        $stmt->bind_param("i", $createdByUserId);
                                        $stmt->execute();
                                        $stmt->bind_result($createdByEmail);
                                        if ($stmt->fetch()) {
                                            $stmt->close();

                                            // Llamar a la función enviarCorreo() para enviar el correo al usuario que lo registró
                                            enviarCorreo($createdByEmail, $motivo, 'eliminacion_peticion');
                                            echo json_encode(array("success" => true));
                                        } else {
                                            echo json_encode(array("error" => "No se encontró el correo electrónico del usuario que registró al usuario eliminado."));
                                        }
                                    } else {
                                        echo json_encode(array("error" => "Error al preparar la consulta del correo electrónico del usuario creador: " . $mysqli->error));
                                    }
                                } else {
                                    echo json_encode(array("error" => "No se encontró el ID del usuario que registró al usuario eliminado."));
                                }
                            } else {
                                echo json_encode(array("error" => "Error al preparar la consulta del usuario creador: " . $mysqli->error));
                            }
                        } else {
                            echo json_encode(array("error" => "Error al eliminar la asociación en user_company_roles: " . $mysqli->error));
                        }
                    } else {
                        echo json_encode(array("error" => "Error al preparar la consulta de eliminación de asociaciones: " . $mysqli->error));
                    }
                } else {
                    echo json_encode(array("error" => "Error al eliminar la entrada en user_company_roles: " . $mysqli->error));
                }
            } else {
                echo json_encode(array("error" => "Error al preparar la consulta de eliminación: " . $mysqli->error));
            }
        } else {
            echo json_encode(array("error" => "No se encontró la empresa asociada al usuario."));
        }
    } else {
        echo json_encode(array("error" => "Error al preparar la consulta de empresa asociada: " . $mysqli->error));
    }
} else {
    echo json_encode(array("error" => "Los campos 'userId', 'companyId' y 'motivo' son requeridos en la solicitud."));
}

$mysqli->close();
?>
