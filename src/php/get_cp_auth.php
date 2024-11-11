<?php
include_once 'cors.php';
include_once 'conexion.php';

// Obtener el RFC de la empresa seleccionada desde los parámetros de la solicitud
$selectedCompanyRFC = $_GET['rfc']; // Cambiar 'rfc' por el nombre del parámetro que estás utilizando en tu solicitud HTTP

if (!empty($selectedCompanyRFC)) {
    // Consulta para obtener el association_id correspondiente al RFC proporcionado
    $query_association_id = "SELECT id FROM companies WHERE rfc = ?";
    if ($stmt = $mysqli->prepare($query_association_id)) {
        $stmt->bind_param("s", $selectedCompanyRFC);
        $stmt->execute();
        $stmt->bind_result($association_id);
        $stmt->fetch();
        $stmt->close();

        if ($association_id) {
            // Consulta principal usando el association_id
            $query = "SELECT u.id, u.username, u.name AS name, u.email, c.nameCompany, r.roleName, p.fecha_inicio, p.fecha_fin, c.rfc AS companyRFC
                      FROM users u
                      INNER JOIN user_company_roles uc ON u.id = uc.user_id
                      INNER JOIN companies c ON uc.company_id = c.id
                      INNER JOIN periodos p ON u.id = p.usuario_id
                      INNER JOIN roles r ON uc.role_id = r.id
                      WHERE uc.association_id = ? AND u.verified = 0";
            if ($stmt = $mysqli->prepare($query)) {
                $stmt->bind_param("i", $association_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result) {
                    // Array para almacenar los datos de los empleados no confirmados
                    $empleadosNoConfirmados = array();

                    // Almacenar los datos de los empleados no confirmados en el array
                    while ($row = $result->fetch_assoc()) {
                        $empleadosNoConfirmados[] = $row;
                    }

                    // Devolver los datos de los empleados no confirmados en formato JSON
                    header('Content-Type: application/json');
                    if (count($empleadosNoConfirmados) > 0) {
                        echo json_encode($empleadosNoConfirmados);
                    } else {
                        echo json_encode(array("mensaje" => "No se encontraron empleados no confirmados para la empresa con RFC: $selectedCompanyRFC"));
                    }
                } else {
                    echo json_encode(array("error" => "Error al ejecutar la consulta: " . $mysqli->error));
                }
                $stmt->close();
            } else {
                echo json_encode(array("error" => "Error al preparar la consulta principal: " . $mysqli->error));
            }
        } else {
            // Enviar un mensaje si no se encuentra la empresa correspondiente al RFC proporcionado
            echo json_encode(array("mensaje" => "No se encontró ninguna empresa con el RFC: $selectedCompanyRFC"));
        }
    } else {
        echo json_encode(array("error" => "Error al preparar la consulta de association_id: " . $mysqli->error));
    }
} else {
    echo json_encode(array("error" => "RFC no proporcionado"));
}

$mysqli->close();
?>
