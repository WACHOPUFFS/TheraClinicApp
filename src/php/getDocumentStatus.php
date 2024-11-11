<?php
header('Content-Type: application/json');

// Incluir el archivo de configuración CORS si es necesario
include_once 'cors.php';

// Incluir el archivo de conexión
include 'conexion.php';

// Verificar que el método de la solicitud sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

// Obtener los parámetros de la URL
$companyId = $_GET['companyId'];


// Verificar que los parámetros no estén vacíos
if (empty($companyId)) {
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

$query = "
    SELECT du.id, du.file_path, du.upload_date, du.company_id, du.user_id, du.tarea_id,
           du.comentario, du.estado, c.nameCompany as company_name, u.username as uploaded_by
    FROM documentUploads du
    INNER JOIN companies c ON du.company_id = c.id
    INNER JOIN users u ON du.user_id = u.id
    WHERE du.company_id = ?
";

// Preparar la declaración
if ($stmt = $mysqli->prepare($query)) {
    // Vincular parámetros
    $stmt->bind_param('i', $companyId);
    $stmt->execute();
    
    // Obtener el resultado
    $result = $stmt->get_result();
    
    // Verificar si hay resultados
    if ($result->num_rows > 0) {
        $documents = [];
        while ($row = $result->fetch_assoc()) {
            $documents[] = $row;
        }
        echo json_encode($documents);
    } else {
        echo json_encode([]); // Devolver un array vacío si no hay resultados
    }

    // Cerrar la declaración
    $stmt->close();
} else {
    echo json_encode(["error" => "Failed to prepare the SQL statement: " . $mysqli->error]);
}

// Cerrar la conexión
$mysqli->close();
?>
