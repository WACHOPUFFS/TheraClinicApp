<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y habilitar CORS
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los parámetros del periodo y la compañía
$companyId = $_GET['company_id'];
$periodId = $_GET['period_id'];

// Verificar que se recibieron los parámetros necesarios
if (!isset($companyId, $periodId)) {
    echo json_encode(['error' => 'Faltan parámetros necesarios.']);
    exit;
}

// Sanitizar las entradas para evitar inyecciones SQL
$companyId = intval($companyId);
$periodId = intval($periodId);

// Consulta para obtener los días pendientes por confirmar desde employee_assignments
$sql = "SELECT ea.assignment_id, ea.employee_id, ea.period_id, ea.period_type_id, e.employee_code, e.first_name, e.last_name, e.middle_name, ea.project_id, 
               p.project_name, ea.work_week, ea.day_of_week, ea.project_status, ea.assignment_date,
               ea.period_start_date, ea.period_end_date  -- Aquí se agregan los campos de periodo
        FROM employee_assignments ea
        JOIN employees e ON ea.employee_id = e.employee_id
        JOIN projects p ON ea.project_id = p.project_id
        WHERE ea.company_id = $companyId 
          AND ea.period_type_id = $periodId 
        ORDER BY ea.assignment_date ASC";  // Ordenar por la fecha de asignación

$result = $mysqli->query($sql);

$diasPendientes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $diasPendientes[] = $row;
    }
}

echo json_encode($diasPendientes);

$mysqli->close();
?>
