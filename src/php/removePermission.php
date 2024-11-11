<?php
include_once 'cors.php';
include_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['userId']) && isset($data['section']) && isset($data['companyId'])) {
    $userId = $mysqli->real_escape_string($data['userId']);
    $section = $mysqli->real_escape_string($data['section']);
    $companyId = $mysqli->real_escape_string($data['companyId']);

    $query = "DELETE FROM permissions WHERE user_id = '$userId' AND section = '$section' AND company_id = '$companyId'";
    if ($mysqli->query($query) === TRUE) {
        echo json_encode(array("success" => true, "message" => "Permiso eliminado correctamente."));
    } else {
        echo json_encode(array("success" => false, "error" => "Error al eliminar permiso: " . $mysqli->error));
    }
} else {
    echo json_encode(array("success" => false, "error" => "Datos incompletos."));
}
?>
