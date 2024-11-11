<?php
require_once 'cors.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$host = $data['host'];
$database = $data['database'];
$user = $data['user'];
$password = $data['password'];

try {
    $conn = new mysqli($host, $user, $password, $database);

    if ($conn->connect_error) {
        throw new Exception('Error conectando a MySQL: ' . $conn->connect_error);
    }

    echo json_encode(["message" => "Conexión a MySQL exitosa"]);
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
