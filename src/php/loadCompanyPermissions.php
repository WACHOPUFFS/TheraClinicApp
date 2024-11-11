<?php
include_once 'cors.php';
include_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['companyId'])) {
    $companyId = $mysqli->real_escape_string($data['companyId']);

    $query = "SELECT NameSection, creationDate FROM sections WHERE company_id = '$companyId'";
    $result = $mysqli->query($query);

    if (!$result) {
        echo json_encode(array("success" => false, "error" => "Error al ejecutar la consulta: " . $mysqli->error));
    } else {
        $permissions = array();
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row;
        }
        echo json_encode(array("success" => true, "permissions" => $permissions));
    }
} else {
    echo json_encode(array("success" => false, "error" => "No se proporcionaron los datos necesarios en la solicitud."));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
