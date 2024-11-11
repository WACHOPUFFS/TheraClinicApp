<?php

// Incluir la configuración de la base de datos
include_once 'cors.php';
include_once 'conexion.php';

// Configurar cabeceras para la respuesta JSON
header('Content-Type: application/json');

// Obtener los datos del cuerpo de la solicitud POST en formato JSON
$json = file_get_contents('php://input');

// Decodificar los datos JSON en un array asociativo
$data = json_decode($json, true);

// Verificar si se recibió el RFC en la solicitud
if (isset($data['rfc'])) {
    // Obtener el RFC de la solicitud
    $rfc = trim($data['rfc']);

    // Consulta SQL para buscar la empresa por su RFC
    $sql = "SELECT nameCompany FROM companies WHERE rfc = ?";

    // Preparar la consulta
    if ($stmt = $mysqli->prepare($sql)) {
        // Enlazar el parámetro
        $stmt->bind_param('s', $rfc);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $resultado = $stmt->get_result();

        // Verificar si se encontró alguna empresa
        if ($resultado->num_rows > 0) {
            // La empresa se encontró, obtener su nombre
            $fila = $resultado->fetch_assoc();
            $nombreEmpresa = $fila['nameCompany'];

            // Enviar una respuesta con el nombre de la empresa
            $response = array("success" => true, "nombreEmpresa" => $nombreEmpresa);
            echo json_encode($response);
        } else {
            // No se encontró ninguna empresa con el RFC proporcionado
            $response = array("success" => false, "message" => "No se encontró ninguna empresa con el RFC proporcionado.");
            echo json_encode($response);
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        // Error al preparar la consulta
        $response = array("success" => false, "message" => "Error al preparar la consulta SQL.");
        echo json_encode($response);
    }
} else {
    // Enviar una respuesta de error si no se recibió el RFC en la solicitud
    $response = array("success" => false, "message" => "No se proporcionó el RFC en la solicitud.");
    echo json_encode($response);
}

// Cerrar la conexión a la base de datos
$mysqli->close();

?>
