<?php
include_once 'cors.php';
include_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
// Obtener el ID de la empresa seleccionada desde los parámetros de la solicitud
if (isset($data['companyId'])) {
    $selectedCompanyId = $mysqli->real_escape_string($data['companyId']);
    // Consulta principal usando el ID de la empresa
    $query = "SELECT u.id, u.username, u.name AS name, u.email, c.nameCompany, 
          IFNULL(r.roleName, lu.levelUserName) AS role, 
          MIN(p.fecha_inicio) AS fecha_inicio, 
          MAX(p.fecha_fin) AS fecha_fin, 
          c.rfc AS companyRFC
    FROM users u
    INNER JOIN user_company_roles uc ON u.id = uc.user_id
    INNER JOIN companies c ON uc.company_id = c.id
    LEFT JOIN periodos p ON u.id = p.usuario_id
    LEFT JOIN roles r ON uc.role_id = r.id
    LEFT JOIN levelUser lu ON lu.id = uc.levelUser_id
    WHERE uc.company_id = '$selectedCompanyId'
    GROUP BY u.id, u.username, u.name, u.email, c.nameCompany, c.rfc, role
    ORDER BY u.creation_date ASC";

    $result = $mysqli->query($query);

    if (!$result) {
        // Manejar el error en caso de que la consulta falle
        echo json_encode(array("success" => false, "error" => "Error al ejecutar la consulta: " . $mysqli->error));
    } else {
        // Array para almacenar los datos de los empleados
        $employees = array();

        // Almacenar los datos de los empleados en el array
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }

        // Devolver los datos de los empleados en formato JSON
        echo json_encode(array("success" => true, "employees" => $employees));
    }
} else {
    // Manejar el caso en que no se proporcione el companyId en la solicitud
    echo json_encode(array("success" => false, "error" => "No se proporcionó el ID de la empresa en la solicitud."));
}
?>