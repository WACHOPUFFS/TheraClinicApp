<?php

include_once 'cors.php';
include_once 'conexion.php';

// Configurar cabeceras para la respuesta JSON
header('Content-Type: application/json');

// Verifica si se proporcionó el parámetro 'empresa' en la URL
if (isset($_GET['empresa'])) {
    // Escapa y sanitiza el valor del parámetro para evitar inyección SQL
    $empresa = trim($_GET['empresa']);

    if ($empresa) {
        // Query para recuperar los archivos recibidos por la empresa específica
        $query = "SELECT * FROM archivos_enviados WHERE empresa_destino = ?";
        
        // Preparar la consulta
        if ($stmt = $mysqli->prepare($query)) {
            // Enlazar el parámetro
            $stmt->bind_param('s', $empresa);

            // Ejecutar la consulta
            $stmt->execute();

            // Obtener el resultado
            $result = $stmt->get_result();

            // Verifica si se obtuvieron resultados
            if ($result && $result->num_rows > 0) {
                $files = array();
                // Recorre los resultados y los agrega al array $files
                while ($row = $result->fetch_assoc()) {
                    // Construye la URL completa del archivo
                    $row['ubicacion_archivos'] = 'https://ctrlobra.sinsetec.com.mx/php/' . $row['ubicacion_archivos'];
                    $files[] = $row;
                }
                // Devuelve los archivos en formato JSON con las URL completas
                echo json_encode($files);
            } else {
                // Si no se encontraron archivos, devuelve un array vacío
                echo json_encode(array());
            }

            // Liberar el resultado
            $stmt->free_result();

            // Cerrar la declaración
            $stmt->close();
        } else {
            // Responder con un mensaje de error si no se puede preparar la consulta
            echo json_encode(array("error" => "Error al preparar la consulta."));
        }
    } else {
        // Si el parámetro está vacío, devuelve un mensaje de error
        echo json_encode(array("error" => "El parámetro 'empresa' no es válido."));
    }
} else {
    // Si no se proporcionó el parámetro 'empresa', devuelve un mensaje de error
    echo json_encode(array("error" => "El parámetro 'empresa' no se ha proporcionado."));
}

// Cerrar la conexión a la base de datos
$mysqli->close();

?>
