<?php
include_once 'cors.php';
include_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"));

// Verificar si se recibió el dato esperado
if (isset($data->employeeId)) {
    // Escapar los datos para prevenir inyección SQL
    $employeeId = $mysqli->real_escape_string($data->employeeId);

    // Eliminar el código asociado al employeeId en la tabla user_codes
    $sqlDeleteCode = "DELETE FROM user_codes WHERE user_id = '$employeeId'";

    // Ejecutar la consulta de eliminación
    if ($mysqli->query($sqlDeleteCode)) {
        echo json_encode(array("success" => true, "message" => "Code deleted successfully."));
    } else {
        echo json_encode(array("success" => false, "message" => "Error deleting code: " . $mysqli->error));
    }
} else {
    // No se recibieron los datos esperados
    echo json_encode(array("success" => false, "message" => "Provide all necessary data for deleting the code."));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
