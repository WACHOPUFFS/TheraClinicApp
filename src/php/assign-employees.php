<?php
// Mostrar todos los errores para facilitar la depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');

// Incluir el archivo de conexión y habilitar CORS
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method. Only POST is allowed.']);
    exit;
}

// Obtener y decodificar los datos JSON de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// Verificar si la decodificación fue exitosa
if (!$data) {
    echo json_encode(['error' => 'Invalid input. No data received.']);
    // Para depurar la entrada que está recibiendo
    var_dump(file_get_contents('php://input'));
    exit;
}

// Verificar que todos los campos necesarios están presentes
if (!isset(
    $data['weekNumber'], 
    $data['dayOfWeek'], 
    $data['obraId'], 
    $data['employeeIds'], 
    $data['companyId'], 
    $data['fiscalYear'], 
    $data['periodNumber'], 
    $data['startDate'], 
    $data['endDate'],
    $data['periodId'], // Asegurar que el 'periodId' esté presente
    $data['periodTypeId']
    ) || empty($data['employeeIds'])) {
    echo json_encode(['error' => 'Missing required fields.']);
    exit;
}

// Extraer datos de la solicitud
$weekNumber = intval($data['weekNumber']);
$dayOfWeek = $mysqli->real_escape_string($data['dayOfWeek']);
$projectId = intval($data['obraId']);
$employeeIds = $data['employeeIds']; // Array de IDs de empleados
$companyId = intval($data['companyId']);
$fiscalYear = intval($data['fiscalYear']);
$periodNumber = $mysqli->real_escape_string($data['periodNumber']);
$periodStartDate = $mysqli->real_escape_string($data['startDate']);
$periodEndDate = $mysqli->real_escape_string($data['endDate']);
$workWeek = $mysqli->real_escape_string($data['weekNumber']);
$periodId = intval($data['periodId']); // Extraer el 'periodId'
$periodTypeId = intval($data['periodTypeId']);

// Iniciar una transacción para insertar los registros
$mysqli->begin_transaction();

try {
    // Insertar cada asignación de empleado
    foreach ($employeeIds as $employeeId) {
        $employeeId = intval($employeeId);

        // Construir la consulta SQL para insertar la asignación
        $sql = "
            INSERT INTO employee_assignments 
            (employee_id, project_id, company_id, assignment_date, day_of_week, period_number, fiscal_year, work_week, period_start_date, period_end_date, period_id, period_type_id, project_status)
            VALUES 
            (
                $employeeId, 
                $projectId, 
                $companyId, 
                CURDATE(), 
                '$dayOfWeek', 
                '$periodNumber', 
                $fiscalYear, 
                '$workWeek', 
                '$periodStartDate', 
                '$periodEndDate', 
                $periodId,
                $periodTypeId, 
                'Pending'
            )";

        // Ejecutar la consulta
        if (!$mysqli->query($sql)) {
            throw new Exception('Error al insertar la asignación: ' . $mysqli->error);
        }
    }

    // Confirmar la transacción
    $mysqli->commit();
    // Enviar la respuesta JSON válida
    echo json_encode(['success' => true, 'message' => 'Employees assigned successfully.']);

} catch (Exception $e) {
    // Si hay un error, revertir la transacción
    $mysqli->rollback();
    echo json_encode(['error' => 'Failed to assign employees: ' . $e->getMessage()]);
}

// Cerrar la conexión
$mysqli->close();
