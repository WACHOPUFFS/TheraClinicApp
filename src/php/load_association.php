<?php
include_once 'cors.php';
include_once 'conexion.php';

if (isset($_GET['codigoEmpresa'])) {
    $codigoEmpresa = $mysqli->real_escape_string($_GET['codigoEmpresa']);

    // Consultar la información de la empresa y del representante a partir del código
    $sql = "SELECT u.full_name AS representante, 
                   c.company_name AS empresa,
                   ucr.principal,
                   ucr.levelUser_id
            FROM company_codes cc
            INNER JOIN companies c ON cc.company_id = c.company_id
            INNER JOIN user_company_roles ucr ON c.company_id = ucr.company_id
            INNER JOIN users u ON u.user_id = ucr.user_id
            WHERE cc.code = ?
              AND ucr.principal = '1'
              AND ucr.levelUser_id = 2";

    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $codigoEmpresa);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $representativeData = $result->fetch_assoc();
            echo json_encode($representativeData);
        } else {
            echo json_encode(["error" => "No se encontró un representante principal con el código de empresa proporcionado."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Error en la preparación de la consulta: " . $mysqli->error]);
    }
} else {
    echo json_encode(["error" => "Código de empresa no proporcionado."]);
}

$mysqli->close();
?>
