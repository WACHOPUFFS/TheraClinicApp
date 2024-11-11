<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';


// Verificar si se ha pasado el parámetro company_id
if (!isset($_GET['company_id'])) {
    echo json_encode(['error' => 'company_id parameter is required']);
    exit;
}

$company_id = intval($_GET['company_id']);

// Consulta SQL para obtener los departamentos de una compañía específica
$sql = "SELECT * FROM departments WHERE company_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();

$departments = array();

if ($result->num_rows > 0) {
    // Salida de datos de cada fila
    while($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
} else {
    echo json_encode([]);
    exit;
}

echo json_encode($departments);

$stmt->close();
$mysqli->close();
?>
