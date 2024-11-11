<?php
header('Content-Type: application/json');

require_once 'cors.php';
require_once 'conexion.php';

$employeeId = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;

if ($employeeId > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM employee_files WHERE employee_id = ?");
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();

    $files = array();
    while($row = $result->fetch_assoc()) {
        $files[$row['file_type']][] = $row;
    }

    echo json_encode($files);
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid employee ID']);
}

$mysqli->close();
?>
