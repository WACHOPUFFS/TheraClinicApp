<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y configurar CORS
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se ha pasado el parámetro company_id
if (!isset($_GET['company_id'])) {
    echo json_encode(['error' => 'company_id parameter is required']);
    exit;
}

$company_id = intval($_GET['company_id']);

// Consulta SQL para obtener todos los turnos de una empresa específica
$sql = "SELECT * FROM shifts WHERE company_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();

$shifts = array();

if ($result->num_rows > 0) {
    // Salida de datos de cada fila
    while ($row = $result->fetch_assoc()) {
        $shifts[] = $row;
    }
} else {
    echo json_encode([]);
    exit;
}

echo json_encode($shifts);

$stmt->close();
$mysqli->close();
?>