<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y habilitar CORS
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se han pasado los parámetros necesarios
if (!isset($_GET['start_date']) || !isset($_GET['end_date']) || !isset($_GET['company_id'])) {
    echo json_encode(['error' => 'start_date, end_date, and company_id parameters are required']);
    exit;
}

// Obtener y sanitizar los parámetros de entrada
$start_date = $mysqli->real_escape_string($_GET['start_date']);
$end_date = $mysqli->real_escape_string($_GET['end_date']);
$company_id = intval($_GET['company_id']); // Convertir company_id a un número entero para seguridad

// Consulta SQL para obtener empleados activos durante el período seleccionado
$sql = "
    SELECT e.employee_id, e.first_name, e.last_name, e.middle_name, e.start_date, e.termination_date, e.reinstatement_date, e.employee_status
    FROM employees e
    LEFT JOIN employee_dismissals_reentries edr ON e.employee_id = edr.employee_id
    WHERE e.company_id = $company_id
    AND (
        (e.start_date <= '$end_date' AND (e.termination_date IS NULL OR e.termination_date >= '$start_date')) -- Empleado activo sin baja antes de que finalice el periodo
        OR
        (edr.date BETWEEN '$start_date' AND '$end_date' AND edr.dismissal_reentry_code IN ('A', 'R')) -- Empleado reingresado o dado de alta durante el período
    )
    GROUP BY e.employee_id
    ORDER BY e.first_name ASC
";

// Ejecutar la consulta
$result = $mysqli->query($sql);

$employees = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Agregar el empleado al arreglo de empleados
        $employees[] = [
            'employee_id' => $row['employee_id'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'middle_name' => $row['middle_name'],
            'start_date' => $row['start_date'],
            'termination_date' => $row['termination_date'],
            'reinstatement_date' => $row['reinstatement_date'],
            'employee_status' => $row['employee_status']
        ];
    }
} else {
    echo json_encode([]);
    exit;
}

// Retornar los empleados en formato JSON
echo json_encode($employees);

// Cerrar la conexión
$mysqli->close();
?>
