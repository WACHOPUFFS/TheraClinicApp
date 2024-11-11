<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se han pasado los datos necesarios
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['company_id']) || !isset($data['shift_id']) || !isset($data['shift_name']) || !isset($data['start_time']) || !isset($data['end_time'])) {
    echo json_encode(['error' => 'company_id, shift_id, shift_name, start_time, and end_time are required']);
    exit;
}

$company_id = intval($data['company_id']);
$shift_id = intval($data['shift_id']);
$shift_name = $data['shift_name'];
$description = isset($data['description']) ? $data['description'] : '';
$start_time = $data['start_time'];
$end_time = $data['end_time'];

// Nuevos campos para las horas de comida (primera y segunda comida)
$lunch_start_time = isset($data['lunch_start_time']) ? $data['lunch_start_time'] : null;
$lunch_end_time = isset($data['lunch_end_time']) ? $data['lunch_end_time'] : null;
$second_lunch_start_time = isset($data['second_lunch_start_time']) ? $data['second_lunch_start_time'] : null;
$second_lunch_end_time = isset($data['second_lunch_end_time']) ? $data['second_lunch_end_time'] : null;

// Consulta SQL para actualizar el turno asegurando que coincida el company_id
$sql = "
    UPDATE shifts 
    SET shift_name = ?, description = ?, start_time = ?, end_time = ?, lunch_start_time = ?, lunch_end_time = ?, second_lunch_start_time = ?, second_lunch_end_time = ? 
    WHERE shift_id = ? AND company_id = ?
";

$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare the SQL statement']);
    exit;
}

// Vincular los parámetros, asegurando que se permita NULL para las horas de comida si no se proporcionan
$stmt->bind_param("sssssssiii", 
    $shift_name, 
    $description, 
    $start_time, 
    $end_time, 
    $lunch_start_time, 
    $lunch_end_time, 
    $second_lunch_start_time, 
    $second_lunch_end_time, 
    $shift_id, 
    $company_id
);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Turno actualizado exitosamente']);
    } else {
        echo json_encode(['error' => 'No se encontró ningún turno con ese ID y company_id, o no hubo cambios en los datos']);
    }
} else {
    echo json_encode(['error' => 'Failed to update shift: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
