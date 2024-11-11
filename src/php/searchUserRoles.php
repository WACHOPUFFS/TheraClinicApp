<?php
include_once 'cors.php';
include_once 'conexion.php';

// Configurar cabeceras para la respuesta JSON
header('Content-Type: application/json');

// Obtener los datos del cuerpo de la solicitud POST en formato JSON
$data = json_decode(file_get_contents("php://input"), true);

// Verificar si se recibió el ID de la empresa en la solicitud
if (isset($data['companyId'])) {
    $selectedCompanyId = trim($data['companyId']);

    // Consulta principal usando el ID de la empresa
    $query = "SELECT u.id, u.username, u.name AS name, u.email, c.nameCompany, r.roleName AS role, p.fecha_inicio, p.fecha_fin, c.rfc AS companyRFC, uc.company_id
              FROM users u
              INNER JOIN user_company_roles uc ON u.id = uc.user_id
              INNER JOIN companies c ON uc.company_id = c.id
              INNER JOIN periodos p ON u.id = p.usuario_id
              LEFT JOIN roles r ON uc.role_id = r.id
              WHERE uc.association_id = ?
              ORDER BY u.creation_date ASC";

    // Preparar la consulta
    if ($stmt = $mysqli->prepare($query)) {
        // Enlazar el parámetro
        $stmt->bind_param('i', $selectedCompanyId);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->get_result();

        if ($result) {
            // Array para almacenar los datos de los socios comerciales
            $businessPartners = array();

            // Almacenar los datos de los socios comerciales en el array
            while ($row = $result->fetch_assoc()) {
                $businessPartners[] = $row;
            }

            // Devolver los datos de los socios comerciales en formato JSON
            echo json_encode(array("success" => true, "businessPartners" => $businessPartners));
        } else {
            // Manejar el error en caso de que la consulta falle
            echo json_encode(array("success" => false, "error" => "Error al ejecutar la consulta: " . $stmt->error));
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        // Manejar el error en caso de que la preparación de la consulta falle
        echo json_encode(array("success" => false, "error" => "Error al preparar la consulta: " . $mysqli->error));
    }
} else {
    // Manejar el caso en que no se proporcione el companyId en la solicitud
    echo json_encode(array("success" => false, "error" => "No se proporcionó el ID de la empresa en la solicitud."));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
