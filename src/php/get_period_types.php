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

// Consulta SQL para obtener los tipos de periodo de una empresa específica
$sql = "
    SELECT period_type_id, period_type_name, fiscal_year
    FROM period_types 
    WHERE company_id = ?
    ORDER BY fiscal_year DESC, period_type_name ASC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();

$periodTypes = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $periodTypes[] = $row;
    }
} else {
    echo json_encode([]);
    exit;
}

echo json_encode($periodTypes);

$stmt->close();
$mysqli->close();
?>
