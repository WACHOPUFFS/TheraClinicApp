<?php
header('Content-Type: application/json');

include_once 'cors.php';
include 'conexion.php';

$companyId = $_GET['companyId'];

if (empty($companyId)) {
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

$query = "SELECT * FROM actas_constitutivas WHERE companyId = ?";
if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param('i', $companyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);

    $stmt->close();
} else {
    echo json_encode(["error" => "Failed to prepare the SQL statement: " . $mysqli->error]);
}

$mysqli->close();
?>
