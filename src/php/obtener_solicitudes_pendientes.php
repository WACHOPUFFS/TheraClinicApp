<?php
// Incluir archivos de conexión y configuración CORS
require_once 'cors.php';
require_once 'conexion.php';

// Configurar cabeceras para la respuesta JSON
header('Content-Type: application/json');

// Verificar si se recibió el nombre de la empresa como parámetro
if (isset($_GET['company'])) {
    // Obtener el nombre de la empresa seleccionada del parámetro
    $company = trim($_GET['company']);

    if ($company) {
        // Consulta para obtener las solicitudes pendientes asociadas a la empresa seleccionada
        $query = "SELECT * FROM companyRequest WHERE senderCompany = ? AND status = 'pendiente'";
        
        // Preparar la consulta
        if ($stmt = $mysqli->prepare($query)) {
            // Enlazar el parámetro
            $stmt->bind_param('s', $company);

            // Ejecutar la consulta
            $stmt->execute();

            // Obtener el resultado
            $result = $stmt->get_result();

            $solicitudes = array();
            while ($row = $result->fetch_assoc()) {
                $solicitudes[] = $row;
            }

            // Responder con las solicitudes pendientes en formato JSON
            echo json_encode($solicitudes);

            // Liberar el resultado
            $stmt->free_result();

            // Cerrar la declaración
            $stmt->close();
        } else {
            // Responder con un mensaje de error si no se puede preparar la consulta
            echo json_encode(array("success" => false, "message" => "Error al preparar la consulta."));
        }
    } else {
        // Responder con un mensaje de error si el parámetro está vacío
        echo json_encode(array("success" => false, "message" => "Nombre de empresa no válido."));
    }
} else {
    // Si no se recibió el nombre de la empresa, responder con un mensaje de error
    echo json_encode(array("success" => false, "message" => "Nombre de empresa no recibido."));
}

// Cerrar
