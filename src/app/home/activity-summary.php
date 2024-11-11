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

// 1. Total de citas por día en el mes actual
$currentMonth = date('Y-m'); // Formato 'YYYY-MM'
$sqlTotalAppointmentsByDay = "
    SELECT DAY(a.date) as day, COUNT(a.id) as total
    FROM appointments a
    JOIN employees e ON a.employee_id = e.employee_id
    WHERE e.company_id = $company_id
    AND DATE_FORMAT(a.date, '%Y-%m') = '$currentMonth'
    GROUP BY DAY(a.date)
    ORDER BY DAY(a.date)
";
$resultTotalAppointmentsByDay = $mysqli->query($sqlTotalAppointmentsByDay);

// Procesar los resultados de citas diarias
$totalAppointmentsByDay = array_fill(1, 31, 0); // Inicializa con 31 días en 0
if ($resultTotalAppointmentsByDay) {
    while ($row = $resultTotalAppointmentsByDay->fetch_assoc()) {
        $day = intval($row['day']);
        $totalAppointmentsByDay[$day] = intval($row['total']);
    }
}

// 2. Nuevos empleados por día en el mes actual
$sqlNewEmployeesByDay = "
    SELECT DAY(created_at) as day, COUNT(*) as total
    FROM employees
    WHERE company_id = $company_id
    AND DATE_FORMAT(created_at, '%Y-%m') = '$currentMonth'
    GROUP BY DAY(created_at)
    ORDER BY DAY(created_at)
";
$resultNewEmployeesByDay = $mysqli->query($sqlNewEmployeesByDay);

// Procesar los resultados de nuevos empleados diarios
$newEmployeesByDay = array_fill(1, 31, 0); // Inicializa con 31 días en 0
if ($resultNewEmployeesByDay) {
    while ($row = $resultNewEmployeesByDay->fetch_assoc()) {
        $day = intval($row['day']);
        $newEmployeesByDay[$day] = intval($row['total']);
    }
}

// Devolver el resumen de actividad diario en formato JSON
echo json_encode([
    'success' => true,
    'appointmentsByDay' => $totalAppointmentsByDay,
    'newEmployeesByDay' => $newEmployeesByDay
]);

// Cerrar la conexión
$mysqli->close();
?>
