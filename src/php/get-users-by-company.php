<?php
include_once 'cors.php';
include_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['companyId'])) {
    $selectedCompanyId = $mysqli->real_escape_string($data['companyId']);
    
    // Consulta para obtener los usuarios por ID de la empresa
    $query = "SELECT u.id, u.username, u.name AS name, u.email, c.nameCompany, 
              IFNULL(r.roleName, lu.levelUserName) AS role, p.fecha_inicio, p.fecha_fin, 
              c.rfc AS companyRFC
              FROM users u
              INNER JOIN user_company_roles uc ON u.id = uc.user_id
              INNER JOIN companies c ON uc.company_id = c.id
              INNER JOIN periodos p ON u.id = p.usuario_id
              LEFT JOIN roles r ON uc.role_id = r.id
              LEFT JOIN levelUser lu ON lu.id = uc.levelUser_id
              WHERE uc.company_id = '$selectedCompanyId'
              ORDER BY u.creation_date ASC";
    
    $result = $mysqli->query($query);

    if (!$result) {
        echo json_encode(array("success" => false, "error" => "Error al ejecutar la consulta: " . $mysqli->error));
    } else {
        $employees = array();

        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }

        echo json_encode(array("success" => true, "users" => $employees));
    }
} else {
    echo json_encode(array("success" => false, "error" => "No se proporcionó el ID de la empresa en la solicitud."));
}
?>
