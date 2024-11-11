<?php
header('Content-Type: application/json');

require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se han pasado los parámetros necesarios
if (!isset($_GET['start_date']) || !isset($_GET['end_date']) || !isset($_GET['project_id']) || !isset($_GET['week_number']) || !isset($_GET['day_of_week'])) {
    echo json_encode(['error' => 'start_date, end_date, project_id, week_number, and day_of_week parameters are required']);
    exit;
}

// Obtener y sanitizar los parámetros de entrada
$start_date = $mysqli->real_escape_string($_GET['start_date']);
$end_date = $mysqli->real_escape_string($_GET['end_date']);
$project_id = intval($_GET['project_id']);
$week_number = intval($_GET['week_number']);
$day_of_week = $mysqli->real_escape_string($_GET['day_of_week']);

// Consulta para obtener empleados asignados anteriormente con más detalles
$sql = "
    SELECT ea.employee_id, e.first_name, e.middle_name, e.last_name, e.position_id, p.project_name
    FROM employee_assignments ea 
    JOIN employees e ON ea.employee_id = e.employee_id
    JOIN projects p ON ea.project_id = p.project_id
    WHERE (ea.project_id = $project_id OR $project_id = 0) -- Manejar project_id 0 para cualquier proyecto
    AND ea.work_week = $week_number 
    AND ea.day_of_week = '$day_of_week'
";

$result = $mysqli->query($sql);

$assigned_employees = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Agregar detalles del empleado al arreglo de empleados asignados
        $assigned_employees[] = [
            'employee_id' => $row['employee_id'],
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'position_id' => $row['position_id'],
            'project_name' => $row['project_name']
        ];
    }
}

// Retornar los empleados asignados en formato JSON
echo json_encode($assigned_employees);
$mysqli->close();
?>
