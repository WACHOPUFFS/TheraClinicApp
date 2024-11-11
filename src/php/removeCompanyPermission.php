<?php
include_once 'cors.php';
include_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['companyId']) && isset($data['section'])) {
    $companyId = $mysqli->real_escape_string($data['companyId']);
    $section = $mysqli->real_escape_string($data['section']);

    // Eliminar la sección de la tabla "sections"
    $queryDeleteSections = "DELETE FROM sections WHERE company_id = '$companyId' AND NameSection = '$section'";

    // Eliminar los permisos asociados a la sección de la tabla "permissions"
    $queryDeletePermissions = "DELETE FROM permissions WHERE company_id = '$companyId' AND section = '$section'";

    // Ejecutar ambas consultas dentro de una transacción
    $mysqli->begin_transaction();

    try {
        // Ejecutar la eliminación en "sections"
        if (!$mysqli->query($queryDeleteSections)) {
            throw new Exception("Error al eliminar en la tabla 'sections': " . $mysqli->error);
        }

        // Ejecutar la eliminación en "permissions"
        if (!$mysqli->query($queryDeletePermissions)) {
            throw new Exception("Error al eliminar en la tabla 'permissions': " . $mysqli->error);
        }

        // Confirmar la transacción si ambas eliminaciones son exitosas
        $mysqli->commit();
        echo json_encode(array("success" => true));

    } catch (Exception $e) {
        // Si hay un error, deshacer la transacción
        $mysqli->rollback();
        echo json_encode(array("success" => false, "error" => $e->getMessage()));
    }

} else {
    echo json_encode(array("success" => false, "error" => "No se proporcionaron los datos necesarios en la solicitud."));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
