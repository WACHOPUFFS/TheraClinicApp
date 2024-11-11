<?php
include_once 'cors.php';
include_once 'conexion.php';

// Verificar la conexión a la base de datos
if ($mysqli->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Conexión fallida: " . $mysqli->connect_error));
    exit();
}

// Obtener el ID de la empresa desde los parámetros de la solicitud
$companyId = isset($_GET['companyId']) ? $mysqli->real_escape_string($_GET['companyId']) : null;

if ($companyId) {
    // Consulta para obtener la URL del logo de la empresa
    $query = "SELECT imageLogo FROM companies WHERE id = '$companyId'";

    $result = $mysqli->query($query);

    if ($result) {
        if ($row = $result->fetch_assoc()) {
            // Comprobar si la URL del logo está vacía y asignar la imagen predeterminada si es necesario
            $logoUrl = $row['imageLogo'] ? 'https://www.siinad.mx/php/' . $row['imageLogo'] : 'https://www.siinad.mx/php/uploads/logos/no-image.png';

            // Devolver la URL del logo en formato JSON
            header('Content-Type: application/json');
            echo json_encode(array("logoUrl" => $logoUrl));
        } else {
            // Manejar el caso en que no se encuentre la empresa
            header('Content-Type: application/json');
            echo json_encode(array("error" => "Empresa no encontrada."));
        }
    } else {
        // Manejar el error en caso de que la consulta falle
        header('Content-Type: application/json');
        echo json_encode(array("error" => "Error al ejecutar la consulta: " . $mysqli->error));
    }
} else {
    // Manejar el caso en que no se proporcione un ID de empresa válido
    header('Content-Type: application/json');
    echo json_encode(array("error" => "ID de empresa no proporcionado."));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
