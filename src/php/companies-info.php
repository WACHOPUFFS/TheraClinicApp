<?php
include_once 'cors.php';
include_once 'conexion.php';

// Verificar la conexión a la base de datos
if ($mysqli->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Conexión fallida: " . $mysqli->connect_error));
    exit();
}

// Consulta para obtener todos los datos de las empresas registradas
$query = "SELECT u.id, u.username, u.name AS name, u.email, u.phone, c.nameCompany, c.id AS idCompany, l.levelUserName, p.fecha_inicio, p.fecha_fin, c.rfc AS companyRFC
          FROM users u
          INNER JOIN user_company_roles uc ON u.id = uc.user_id
          INNER JOIN companies c ON uc.company_id = c.id
          INNER JOIN periodos p ON u.id = p.usuario_id
          INNER JOIN levelUser l ON uc.levelUser_id = l.id";

$result = $mysqli->query($query);

if ($result) {
    // Array para almacenar los datos de las empresas registradas
    $empresasRegistradas = array();

    // Almacenar los datos en el array
    while ($row = $result->fetch_assoc()) {
        $empresasRegistradas[] = $row;
    }

    // Devolver los datos en formato JSON
    header('Content-Type: application/json');
    echo json_encode($empresasRegistradas);
} else {
    // Manejar el error en caso de que la consulta falle
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Error al ejecutar la consulta: " . $mysqli->error));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
