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

// Consulta SQL para obtener las obras que están activas durante el rango de fechas seleccionado y pertenecen a la empresa
$sql = "
    SELECT project_id, alt_id, project_name, start_date, end_date, prospect_campaign_id, project_key, user_id, created_on, created_by, deleted_on, deleted_by, segment, company_id, resources_expenses
    FROM projects
    WHERE company_id = $company_id
    AND (
        (start_date <= '$end_date' AND end_date >= '$start_date')  -- La obra debe estar activa en algún punto del periodo seleccionado
    )
    ORDER BY start_date ASC
";

// Ejecutar la consulta
$result = $mysqli->query($sql);

$projects = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Agregar el proyecto al arreglo de proyectos
        $projects[] = [
            'project_id' => $row['project_id'],
            'alt_id' => $row['alt_id'],
            'project_name' => $row['project_name'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'prospect_campaign_id' => $row['prospect_campaign_id'],
            'project_key' => $row['project_key'],
            'user_id' => $row['user_id'],
            'created_on' => $row['created_on'],
            'created_by' => $row['created_by'],
            'deleted_on' => $row['deleted_on'],
            'deleted_by' => $row['deleted_by'],
            'segment' => $row['segment'],
            'company_id' => $row['company_id'],
            'resources_expenses' => $row['resources_expenses']
        ];
    }
} else {
    echo json_encode([]);
    exit;
}

// Retornar los proyectos en formato JSON
echo json_encode($projects);

// Cerrar la conexión
$mysqli->close();
?>
