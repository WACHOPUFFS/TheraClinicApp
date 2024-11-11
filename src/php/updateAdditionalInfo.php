<?php
require_once 'cors.php';
include 'conexion.php';

// Obtener los datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'No data provided']);
    exit();
}

// Función para actualizar una tabla específica
function updateTable($mysqli, $table, $idField, $data) {
    foreach ($data as $row) {
        $id = $row[$idField];
        unset($row[$idField]); // Remove ID field from update data

        $fields = [];
        $values = [];

        foreach ($row as $field => $value) {
            $fields[] = "`$field`=?";
            $values[] = $value;
        }

        $fields = implode(', ', $fields);
        $values[] = $id; // Add ID value at the end for the WHERE clause

        $sql = "UPDATE `$table` SET $fields WHERE `$idField`=?";
        $stmt = $mysqli->prepare($sql);

        if ($stmt === false) {
            echo json_encode(['error' => "Failed to prepare statement: " . $mysqli->error]);
            exit();
        }

        $types = str_repeat('s', count($values));
        $stmt->bind_param($types, ...$values);

        if (!$stmt->execute()) {
            echo json_encode(['error' => "Failed to execute statement: " . $stmt->error]);
            exit();
        }

        $stmt->close();
    }
}

// Actualizar cada tabla correspondiente
if (isset($data['actasConstitutivas'])) {
    updateTable($mysqli, 'actas_constitutivas', 'id', $data['actasConstitutivas']);
}

if (isset($data['afil01'])) {
    updateTable($mysqli, 'afil01', 'id', $data['afil01']);
}

if (isset($data['rfc'])) {
    updateTable($mysqli, 'rfc', 'id', $data['rfc']);
}

if (isset($data['autorizacion'])) {
    updateTable($mysqli, 'autorizacion_stps', 'id', $data['autorizacion']);
}

echo json_encode(['success' => true]);

$mysqli->close();
?>
