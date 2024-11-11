<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y habilitar CORS
require_once 'cors.php';
require_once 'conexion.php';

// Consulta SQL para obtener todos los tipos de incidencias
$sql = "SELECT id, name FROM incident_types";

// Ejecutar la consulta
$result = $mysqli->query($sql);

$incident_types = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Agregar cada tipo de incidencia al arreglo
        $incident_types[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }
}

// Retornar los tipos de incidencia en formato JSON
echo json_encode($incident_types);

// Cerrar la conexión
$mysqli->close();
?>
