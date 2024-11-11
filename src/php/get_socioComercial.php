<?php
include_once 'cors.php';
include_once 'conexion.php';

// Obtener el ID del socio comercial desde los parámetros de la solicitud
$socioComercialUserId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($socioComercialUserId <= 0) {
    echo json_encode(array("error" => "ID de usuario inválido"));
    exit;
}

// Consulta para obtener el association_id correspondiente al ID proporcionado
$query_association_id = "SELECT id FROM users WHERE id = ?";
$stmt_association_id = $mysqli->prepare($query_association_id);
$stmt_association_id->bind_param("i", $socioComercialUserId);
$stmt_association_id->execute();
$result_association_id = $stmt_association_id->get_result();

if ($result_association_id->num_rows > 0) {
    $row_association_id = $result_association_id->fetch_assoc();
    $association_id = $row_association_id['id'];

    // Consulta principal usando el association_id
    $query = "SELECT u.id, u.username, u.name AS name, u.email, u.phone, c.nameCompany, r.roleName, p.fecha_inicio, p.fecha_fin, c.rfc AS companyRFC
              FROM users u
              INNER JOIN user_company_roles uc ON u.id = uc.user_id
              INNER JOIN companies c ON uc.company_id = c.id
              INNER JOIN periodos p ON u.id = p.usuario_id
              INNER JOIN roles r ON uc.role_id = r.id
              WHERE uc.user_id = ? AND u.verified = '1'";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $association_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        echo json_encode(array("error" => "Error al ejecutar la consulta: " . $mysqli->error));
    } else {
        $empleadosNoConfirmados = array();

        while ($row = $result->fetch_assoc()) {
            $empleadosNoConfirmados[] = $row;
        }

        header('Content-Type: application/json');
        if (count($empleadosNoConfirmados) > 0) {
            echo json_encode($empleadosNoConfirmados);
        } else {
            echo json_encode(array("mensaje" => "No se encontraron empleados no confirmados para la empresa con ID: $socioComercialUserId"));
        }
    }
    $stmt->close();
} else {
    echo json_encode(array("mensaje" => "No se encontró ninguna usuario con el ID: $socioComercialUserId"));
}

$stmt_association_id->close();
$mysqli->close();
?>
