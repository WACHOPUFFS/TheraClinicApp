<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y habilitar CORS
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los parámetros desde la URL
$companyId = $_GET['company_id'];
$periodId = $_GET['period_id'];
$date = $_GET['date'];

// Verificar que se recibieron todos los parámetros necesarios
if (!isset($companyId, $periodId, $date)) {
    echo json_encode(['error' => 'Faltan parámetros necesarios.']);
    exit;
}

// Sanitizar las entradas para evitar inyecciones SQL
$companyId = intval($companyId);
$periodId = intval($periodId);
$date = $mysqli->real_escape_string($date);

// Consulta para obtener los empleados asignados a un día específico
$sql = "SELECT ea.assignment_id, ea.employee_id, e.employee_code, e.first_name, e.last_name, e.middle_name, ea.project_id, 
               p.project_name, ea.work_week, ea.day_of_week, ea.period_start_date, ea.period_end_date, ea.project_status
        FROM employee_assignments ea
        JOIN employees e ON ea.employee_id = e.employee_id
        JOIN projects p ON ea.project_id = p.project_id
        WHERE ea.company_id = $companyId
          AND ea.period_id = $periodId
          AND ea.day_of_week = '$date'";

$result = $mysqli->query($sql);

$empleadosDia = [];

// Verificar si se obtuvieron resultados
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $empleadosDia[] = $row;
    }
}

// Devolver los empleados como JSON
echo json_encode($empleadosDia);

$mysqli->close();
?>
