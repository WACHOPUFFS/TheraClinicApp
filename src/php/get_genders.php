<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los géneros
$result = $mysqli->query("SELECT * FROM genders");

$genders = array();
while($row = $result->fetch_assoc()) {
    $genders[] = $row;
}

echo json_encode($genders);

$mysqli->close();
?>

