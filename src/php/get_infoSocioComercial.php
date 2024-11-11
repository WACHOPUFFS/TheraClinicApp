<?php
include_once 'cors.php';
include_once 'conexion.php';

// Verificar la conexión a la base de datos
if ($mysqli->connect_error) {
    die(json_encode(array("error" => "Error de conexión a la base de datos: " . $mysqli->connect_error)));
}

// Consulta para seleccionar a todos los usuarios
$query = "SELECT id, username, name, email, phone, nameCompany, rfc, fecha_inicio, fecha_fin, fecha_inicio_request, requestFolio  
          FROM premium_user_requests";

if ($stmt = $mysqli->prepare($query)) {
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        // Manejar el error en caso de que la consulta falle
        echo json_encode(array("error" => "Error al ejecutar la consulta: " . $mysqli->error));
    } else {
        // Array para almacenar los datos de los usuarios
        $usuarios = array();

        // Almacenar los datos de los usuarios en el array
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }

        // Devolver los datos de los usuarios en formato JSON
        header('Content-Type: application/json');
        if (count($usuarios) > 0) {
            echo json_encode($usuarios);
        } else {
            // Enviar un mensaje si no se encontraron usuarios
            echo json_encode(array("mensaje" => "No se encontraron usuarios en la base de datos."));
        }
    }

    $stmt->close();
} else {
    echo json_encode(array("error" => "Error al preparar la consulta: " . $mysqli->error));
}

$mysqli->close();
?>
