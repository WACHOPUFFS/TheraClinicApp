<?php
header('Content-Type: application/json');

require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se han pasado los parámetros necesarios
if (!isset($_GET['company_id']) || !isset($_GET['start_date']) || !isset($_GET['end_date']) || !isset($_GET['week_number']) || !isset($_GET['day_of_week'])) {
    echo json_encode(['error' => 'company_id, start_date, end_date, week_number, and day_of_week parameters are required']);
    exit;
}

// Obtener y sanitizar los parámetros de entrada
$company_id = intval($_GET['company_id']);
$start_date = $mysqli->real_escape_string($_GET['start_date']);
$end_date = $mysqli->real_escape_string($_GET['end_date']);
$week_number = intval($_GET['week_number']);
$day_of_week = $mysqli->real_escape_string($_GET['day_of_week']);

// Consulta SQL para obtener empleados no asignados a ningún proyecto durante el período seleccionado
$sql = "
    SELECT e.employee_id, e.first_name, e.middle_name, e.last_name, e.position_id
    FROM employees e
    LEFT JOIN employee_assignments ea ON e.employee_id = ea.employee_id 
    AND ea.work_week = $week_number 
    AND ea.day_of_week = '$day_of_week'
    AND ea.company_id = $company_id 
    WHERE e.company_id = $company_id 
    AND (ea.employee_id IS NULL OR (ea.project_id IS NULL AND ea.work_week IS NULL))
";

$result = $mysqli->query($sql);

$unassigned_employees = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Agregar detalles del empleado al arreglo de empleados no asignados
        $unassigned_employees[] = [
            'employee_id' => $row['employee_id'],
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'position_id' => $row['position_id']
        ];
    }
}

// Retornar los empleados no asignados en formato JSON
echo json_encode($unassigned_employees);
$mysqli->close();
?>
