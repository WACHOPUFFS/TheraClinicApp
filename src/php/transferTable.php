<?php
require_once 'cors.php';

header('Content-Type: application/json');

// Recibir datos de la solicitud POST
$data = json_decode(file_get_contents('php://input'), true);

$sqlServerConfig = $data['sqlServerConfig'];
$mySqlConfig = $data['mySqlConfig'];
$tableName = $data['tableName'];

try {
    // Conectar a SQL Server
    $sqlServerConnectionInfo = array(
        "Database" => $sqlServerConfig['database'],
        "UID" => $sqlServerConfig['user'],
        "PWD" => $sqlServerConfig['password']
    );
    $sqlServerConn = sqlsrv_connect($sqlServerConfig['server'], $sqlServerConnectionInfo);

    if ($sqlServerConn === false) {
        throw new Exception('Error conectando a SQL Server: ' . print_r(sqlsrv_errors(), true));
    }

    // Obtener datos de la tabla en SQL Server
    $query = "SELECT * FROM $tableName";
    $sqlServerStmt = sqlsrv_query($sqlServerConn, $query);

    if ($sqlServerStmt === false) {
        throw new Exception('Error al ejecutar la consulta en SQL Server: ' . print_r(sqlsrv_errors(), true));
    }

    $rows = [];
    $columns = [];
    while ($row = sqlsrv_fetch_array($sqlServerStmt, SQLSRV_FETCH_ASSOC)) {
        if (empty($columns)) {
            $columns = array_keys($row);
        }
        $rows[] = $row;
    }

    // Cerrar la conexión a SQL Server
    sqlsrv_free_stmt($sqlServerStmt);
    sqlsrv_close($sqlServerConn);

    // Conectar a MySQL
    $mySqlConn = new mysqli(
        $mySqlConfig['host'],
        $mySqlConfig['user'],
        $mySqlConfig['password'],
        $mySqlConfig['database']
    );

    if ($mySqlConn->connect_error) {
        throw new Exception('Error conectando a MySQL: ' . $mySqlConn->connect_error);
    }

    // Crear la tabla en MySQL
    $columnDefinitions = array_map(function ($col) {
        return "`$col` VARCHAR(255)";
    }, $columns);
    $createTableQuery = "CREATE TABLE IF NOT EXISTS $tableName (" . implode(', ', $columnDefinitions) . ")";
    if ($mySqlConn->query($createTableQuery) === FALSE) {
        throw new Exception('Error creando la tabla en MySQL: ' . $mySqlConn->error);
    }

    // Insertar los datos en la tabla MySQL
    foreach ($rows as $row) {
        $values = array_map(function ($val) use ($mySqlConn) {
            return "'" . $mySqlConn->real_escape_string($val) . "'";
        }, $row);
        $insertQuery = "INSERT INTO $tableName (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
        if ($mySqlConn->query($insertQuery) === FALSE) {
            throw new Exception('Error insertando datos en MySQL: ' . $mySqlConn->error);
        }
    }

    // Cerrar la conexión a MySQL
    $mySqlConn->close();

    echo json_encode(["message" => "Tabla $tableName transferida exitosamente de SQL Server a MySQL"]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
