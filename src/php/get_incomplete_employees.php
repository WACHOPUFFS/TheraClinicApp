<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Obtener el ID de la empresa desde los parámetros de la URL
$companyId = isset($_GET['company_id']) ? intval($_GET['company_id']) : 0;

if ($companyId > 0) {
    // Preparar la declaración SQL para obtener empleados con estado 'Incomplete' o 'Rejected'
    $stmt = $mysqli->prepare("SELECT * FROM employees e JOIN employee_requests er ON e.employee_id = er.employee_id WHERE er.status IN ('Incomplete', 'Rejected') AND e.company_id = ?");
    
    if ($stmt === false) {
        error_log("Error en la preparación de la consulta: " . $mysqli->error);
        echo json_encode(['error' => 'Error en la preparación de la consulta SQL']);
        exit;
    }

    $stmt->bind_param("i", $companyId);
    $stmt->execute();
    $result = $stmt->get_result();

    $employees = array();
    while($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }

    // Depuración: Imprimir la respuesta JSON
    error_log("Respuesta JSON: " . json_encode($employees));

    echo json_encode($employees);
    $stmt->close();
} else {
    echo json_encode([]);
}

$mysqli->close();
?>
