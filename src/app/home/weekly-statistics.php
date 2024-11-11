<?php
header('Content-Type: application/json');
require_once 'conexion.php';
require_once 'cors.php';

// Verificar que se haya pasado el parámetro necesario (company_id)
if (!isset($_GET['company_id'])) {
    echo json_encode(['success' => false, 'error' => 'company_id is required']);
    exit;
}

// Obtener el parámetro desde la URL
$company_id = intval($_GET['company_id']);

// Obtener las fechas de inicio y fin de la semana actual
$currentWeekStart = date('Y-m-d', strtotime('monday this week'));
$currentWeekEnd = date('Y-m-d', strtotime('sunday this week'));

// 1. Total de citas esta semana
$sqlAppointmentsThisWeek = "
    SELECT COUNT(a.id) as total
    FROM appointments a
    JOIN employees e ON a.employee_id = e.employee_id
    WHERE e.company_id = $company_id
    AND a.date BETWEEN '$currentWeekStart' AND '$currentWeekEnd'
";
$resultAppointmentsThisWeek = $mysqli->query($sqlAppointmentsThisWeek);
$appointmentsThisWeek = ($resultAppointmentsThisWeek && $row = $resultAppointmentsThisWeek->fetch_assoc()) ? intval($row['total']) : 0;

// 2. Total de pacientes atendidos esta semana (asumiendo que cada cita es un paciente único atendido)
$sqlPatientsThisWeek = "
    SELECT COUNT(DISTINCT a.employee_id) as total
    FROM appointments a
    JOIN employees e ON a.employee_id = e.employee_id
    WHERE e.company_id = $company_id
    AND a.date BETWEEN '$currentWeekStart' AND '$currentWeekEnd'
";
$resultPatientsThisWeek = $mysqli->query($sqlPatientsThisWeek);
$patientsThisWeek = ($resultPatientsThisWeek && $row = $resultPatientsThisWeek->fetch_assoc()) ? intval($row['total']) : 0;

// Devolver las estadísticas de la clínica en formato JSON
echo json_encode([
    'success' => true,
    'appointmentsThisWeek' => $appointmentsThisWeek,
    'patientsThisWeek' => $patientsThisWeek
]);

// Cerrar la conexión
$mysqli->close();
?>
