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
$query = "SELECT c.id AS idCompany, c.nameCompany, c.rfc 
          FROM companies c";

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
