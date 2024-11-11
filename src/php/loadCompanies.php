<?php

require_once 'cors.php';
require_once 'conexion.php';

// Obtener los datos enviados en formato JSON
$data = json_decode(file_get_contents("php://input"), true);

// Verificar si se recibió la ID de la empresa como parte de los datos
if (isset($data['company_id'])) {
    // Sanitizar y convertir la ID de la empresa a un entero
    $companyId = intval($data['company_id']);

    // Consulta para obtener las empresas no principales del usuario para una empresa específica
    $nonPrincipalCompanyQuery = "SELECT uc.status, c.nameCompany, c.rfc, r.roleName, l.levelUserName, uc.association_id, uc.company_id
        FROM user_company_roles uc
        LEFT JOIN companies c ON uc.company_id = c.id
        LEFT JOIN roles r ON uc.role_id = r.id
        LEFT JOIN levelUser l ON uc.levelUser_id = l.id
        WHERE uc.association_id = ?";
    
    // Preparar y ejecutar la consulta SQL
    if ($stmt = $mysqli->prepare($nonPrincipalCompanyQuery)) {
        $stmt->bind_param("i", $companyId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Crear array para almacenar las empresas no principales del usuario
        $nonPrincipalCompanies = array();

        // Iterar sobre los resultados y almacenar las empresas no principales en el array correspondiente
        while ($row = $result->fetch_assoc()) {
            $nonPrincipalCompanies[] = array(
                "idCompany" => $row['company_id'], // Utiliza company_id para obtener el id de la compañía
                "nameCompany" => $row['nameCompany'],
                "roleInCompany" => $row['roleName'],
                "rfcIncompany" => $row['rfc'],
                "statusCompany" => $row['status'], // Cambia el status a la columna de user_company_roles
                "levelUser" => $row['levelUserName'],
                "idAssociation" => $row['association_id'], // Agrega el association_id
            );
        }

        // Cerrar la declaración
        $stmt->close();

        // Responder con éxito junto con las empresas no principales
        echo json_encode(
            array(
                "success" => true,
                "message" => "Empresas no principales obtenidas exitosamente.",
                "nonPrincipalCompanies" => $nonPrincipalCompanies,
            )
        );
    } else {
        // Error en la preparación de la consulta SQL
        echo json_encode(array("success" => false, "message" => "Error en la preparación de la consulta: " . $mysqli->error));
    }
} else {
    // No se recibió la ID de la empresa, responder con un mensaje de error
    echo json_encode(array("success" => false, "message" => "ID de empresa no recibida."));
}

// Cerrar la conexión a la base de datos
$mysqli->close();

?>
