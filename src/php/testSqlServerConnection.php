<?php

require_once 'cors.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$server = $data['server'];
$database = $data['database'];
$user = $data['user'];
$password = $data['password'];

try {
    $connectionInfo = array("Database" => $database, "UID" => $user, "PWD" => $password);
    $conn = sqlsrv_connect($server, $connectionInfo);

    if ($conn === false) {
        throw new Exception('Error conectando a SQL Server: ' . print_r(sqlsrv_errors(), true));
    }

    echo json_encode(["message" => "Conexión a SQL Server exitosa"]);
    sqlsrv_close($conn);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
