<?php
include_once 'cors.php';
include_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['user_id']) && isset($data['company_id']) && isset($data['principal']) && isset($data['levelUser_id']) && isset($data['status'])) {
    $user_id = $mysqli->real_escape_string($data['user_id']);
    $company_id = $mysqli->real_escape_string($data['company_id']);
    $principal = $mysqli->real_escape_string($data['principal']);
    $levelUser_id = $mysqli->real_escape_string($data['levelUser_id']);
    $status = $mysqli->real_escape_string($data['status']);

    // Consulta para insertar la nueva relación usuario-empresa
    $query = "INSERT INTO user_company_roles (user_id, company_id, principal, levelUser_id, status)
              VALUES ('$user_id', '$company_id', '$principal', '$levelUser_id', '$status')";

    if ($mysqli->query($query)) {
        echo json_encode(array("success" => true, "message" => "Empresa asignada correctamente"));
    } else {
        echo json_encode(array("success" => false, "error" => "Error al asignar la empresa: " . $mysqli->error));
    }
} else {
    echo json_encode(array("success" => false, "error" => "Datos incompletos para realizar la asignación"));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
