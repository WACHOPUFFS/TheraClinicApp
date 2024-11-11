<?php
header('Content-Type: application/json');

require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se han pasado los parámetros necesarios
if (!isset($_GET['start_date']) || !isset($_GET['end_date']) || !isset($_GET['company_id']) || !isset($_GET['project_id']) || !isset($_GET['week_number']) || !isset($_GET['day_of_week'])) {
    echo json_encode(['error' => 'start_date, end_date, company_id, project_id, week_number, and day_of_week parameters are required']);
    exit;
}

$start_date = $mysqli->real_escape_string($_GET['start_date']);
$end_date = $mysqli->real_escape_string($_GET['end_date']);
$company_id = intval($_GET['company_id']);
$project_id = intval($_GET['project_id']);
$week_number = intval($_GET['week_number']);
$day_of_week = $mysqli->real_escape_string($_GET['day_of_week']);

// Consulta para obtener empleados asignados anteriormente
$sql = "
    SELECT ea.employee_id 
    FROM employee_assignments ea 
    WHERE ea.company_id = $company_id 
    AND ea.project_id = $project_id 
    AND ea.work_week = $week_number 
    AND ea.day_of_week = '$day_of_week'
";

$result = $mysqli->query($sql);

$assigned_employees = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $assigned_employees[] = $row['employee_id'];
    }
}

echo json_encode($assigned_employees);
$mysqli->close();
?>
