<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

$company_id = intval($_GET['company_id']);

$sql = "SELECT * FROM period_types WHERE company_id = $company_id";
$result = $mysqli->query($sql);

$periods = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $periods[] = $row;
    }
}

echo json_encode($periods);

$mysqli->close();
?>
