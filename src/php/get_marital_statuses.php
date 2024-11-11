<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los estados civiles
$result = $mysqli->query("SELECT * FROM marital_statuses");

$statuses = array();
while($row = $result->fetch_assoc()) {
    $statuses[] = $row;
}

echo json_encode($statuses);

$mysqli->close();
?>
