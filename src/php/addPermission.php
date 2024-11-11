<?php
include_once 'cors.php';
include_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['userId']) && isset($data['section']) && isset($data['companyId']) && isset($data['subSections'])) {
    $userId = $mysqli->real_escape_string($data['userId']);
    $section = $mysqli->real_escape_string($data['section']);
    $companyId = $mysqli->real_escape_string($data['companyId']);
    $subSections = $data['subSections'];

    $success = true;
    $errorMessages = [];

    foreach ($subSections as $subSection) {
        $subSectionEscaped = $mysqli->real_escape_string($subSection);
        $query = "INSERT INTO permissions (user_id, section, subSection, company_id) VALUES ('$userId', '$section', '$subSectionEscaped', '$companyId')";
        if ($mysqli->query($query) !== TRUE) {
            $success = false;
            $errorMessages[] = "Error al añadir permiso: " . $mysqli->error;
        }
    }

    if ($success) {
        echo json_encode(array("success" => true, "message" => "Permisos añadidos correctamente."));
    } else {
        echo json_encode(array("success" => false, "error" => implode(", ", $errorMessages)));
    }
} else {
    echo json_encode(array("success" => false, "error" => "Datos incompletos."));
}
?>
