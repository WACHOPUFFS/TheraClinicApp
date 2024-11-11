<?php
include_once 'cors.php';
include_once 'conexion.php';

// Verificar la conexión a la base de datos
if ($mysqli->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Conexión fallida: " . $mysqli->connect_error));
    exit();
}

// Consulta para obtener todos los niveles de usuario, excluyendo ciertos niveles
$query = "SELECT id, levelUserName 
          FROM levelUser 
          WHERE levelUserName NOT IN ('adminS', 'adminE', 'adminEE', 'adminPE')";

$result = $mysqli->query($query);

if ($result) {
    $levelUsers = array();

    while ($row = $result->fetch_assoc()) {
        $levelUsers[] = $row;
    }

    // Devolver los datos en formato JSON
    header('Content-Type: application/json');
    echo json_encode($levelUsers);
} else {
    // Manejar el error en caso de que la consulta falle
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Error al ejecutar la consulta: " . $mysqli->error));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
