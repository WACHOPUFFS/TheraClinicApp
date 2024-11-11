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

// Definir el ID correspondiente al nivel de usuario de terapeuta
$therapistLevelId = 3; // Cambia este valor al ID real del nivel de terapeuta en tu tabla levelUser

// Consulta para obtener los terapeutas de la empresa, directamente sin bind_param
$sql = "
    SELECT 
        u.id,
        u.name,
        u.email
    FROM 
        users u
    JOIN 
        user_company_roles ucr ON u.id = ucr.user_id
    JOIN 
        levelUser lu ON ucr.levelUser_id = lu.id
    WHERE 
        ucr.company_id = $company_id AND ucr.levelUser_id = $therapistLevelId
";

// Ejecutar la consulta
$result = $mysqli->query($sql);

if ($result) {
    $therapists = [];

    // Recorrer los resultados y construir el arreglo de terapeutas
    while ($row = $result->fetch_assoc()) {
        $therapists[] = $row;
    }

    // Devolver los terapeutas en formato JSON
    echo json_encode(['success' => true, 'therapists' => $therapists]);
} else {
    // Manejo de errores
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve therapists: ' . $mysqli->error]);
}

// Cerrar la conexión
$mysqli->close();
?>
