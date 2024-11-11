<?php
include_once 'cors.php';
include_once 'conexion.php';

// Obtener los datos enviados desde la solicitud
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // Extraer los datos
    $id = $data['id'];
    $username = $data['username'];
    $name = $data['name'];
    $email = $data['email'];
    $phone = $data['phone'];
    $nameCompany = $data['nameCompany'];
    $levelUserName = $data['levelUserName'];
    $fecha_inicio = $data['fecha_inicio'];
    $fecha_fin = $data['fecha_fin'];
    $companyRFC = $data['companyRFC'];

    // Consulta para actualizar los datos de la empresa
    $query = "UPDATE users u
              INNER JOIN user_company_roles uc ON u.id = uc.user_id
              INNER JOIN companies c ON uc.company_id = c.id
              INNER JOIN periodos p ON u.id = p.usuario_id
              INNER JOIN levelUser l ON uc.levelUser_id = l.id

              SET 
                  u.username = '$username', 
                  u.name = '$name', 
                  u.email = '$email', 
                  u.phone = '$phone', 
                  c.nameCompany = '$nameCompany', 
                  l.levelUserName = '$levelUserName',
                  p.fecha_inicio = '$fecha_inicio', 
                  p.fecha_fin = '$fecha_fin',
                  c.rfc = '$companyRFC'
              WHERE u.id = '$id'";

    $result = $mysqli->query($query);

    if ($result) {
        // Devolver una respuesta de éxito
        header('Content-Type: application/json');
        echo json_encode(array("message" => "Datos actualizados correctamente."));
    } else {
        // Manejar el error en caso de que la consulta falle
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al actualizar los datos: " . $mysqli->error));
    }
} else {
    // Manejar el error en caso de que no se reciban datos
    header('Content-Type: application/json');
    echo json_encode(array("error" => "No se recibieron datos."));
}

$mysqli->close();
?>
