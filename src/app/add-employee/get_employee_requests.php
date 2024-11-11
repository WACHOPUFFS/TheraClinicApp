<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y manejo de CORS
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se han pasado los parámetros necesarios (company_id, fechaInicio, fechaFin, user_id)
if (!isset($_GET['company_id']) || !isset($_GET['fechaInicio']) || !isset($_GET['fechaFin']) || !isset($_GET['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Parámetros incompletos']);
    exit;
}

// Obtener los parámetros desde la URL
$company_id = intval($_GET['company_id']);
$user_id = intval($_GET['user_id']); // ID del usuario que inició sesión
$fechaInicio = $_GET['fechaInicio']; // Fecha de inicio del rango (formato YYYY-MM-DD)
$fechaFin = $_GET['fechaFin']; // Fecha de fin del rango (formato YYYY-MM-DD)

// Construcción segura de la consulta con JOIN entre las tablas employee_requests y employees
// Construcción segura de la consulta con JOIN entre las tablas employee_requests y employees
$sql = "
    SELECT 
        er.request_id, 
        er.status AS request_status, 
        er.description,
        er.created_at AS request_created_at, 
        er.updated_at AS request_updated_at, 
        er.created_by,
        e.employee_id, 
        e.first_name, 
        e.middle_name, 
        e.last_name, 
        CONCAT(e.first_name, ' ', e.middle_name, ' ', e.last_name) AS full_name,
        e.department_id, 
        d.department_name,
        e.position_id, 
        p.position_name
    FROM 
        employee_requests er
    LEFT JOIN 
        employees e ON er.employee_id = e.employee_id
    LEFT JOIN 
        departments d ON e.department_id = d.department_id
    LEFT JOIN 
        positions p ON e.position_id = p.position_id
    WHERE 
        e.company_id = ? 
    AND 
        er.created_by = ?  -- Filtrar por el usuario que inició sesión
    AND 
        (DATE(er.created_at) BETWEEN ? AND ? OR DATE(er.updated_at) BETWEEN ? AND ?)
";


// Preparar y ejecutar la consulta
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('iissss', $company_id, $user_id, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontraron resultados
if ($result && $result->num_rows > 0) {
    $solicitudes = [];

    // Obtener cada fila de resultados y agregarla al array
    while ($row = $result->fetch_assoc()) {
        $solicitudes[] = $row;
    }

    // Devolver las solicitudes en formato JSON
    echo json_encode(['success' => true, 'solicitudes' => $solicitudes]);

} else {
    // Si no se encontraron solicitudes o hay un error
    echo json_encode(['success' => false, 'error' => 'No se encontraron solicitudes en el rango de fechas especificado.']);
}

// Cerrar la conexión
$stmt->close();
$mysqli->close();
?>
