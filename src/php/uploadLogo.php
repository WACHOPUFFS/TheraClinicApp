<?php
include_once 'cors.php';
include_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se ha enviado un archivo
    if (isset($_FILES['logo']) && isset($_POST['companyId'])) {
        $companyId = $mysqli->real_escape_string($_POST['companyId']);
        $logo = $_FILES['logo'];

        // Verificar si el archivo se ha subido correctamente
        if ($logo['error'] === UPLOAD_ERR_OK) {
            // Definir la ruta de la carpeta donde se guardarán los logos
            $uploadDir = 'uploads/logos/';
            // Asegurarse de que la carpeta exista
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Definir la ruta completa del archivo
            $filePath = $uploadDir . basename($logo['name']);

            // Mover el archivo a la carpeta de destino
            if (move_uploaded_file($logo['tmp_name'], $filePath)) {
                // Verificar la ruta del archivo
                if (file_exists($filePath)) {
                    // Actualizar la base de datos con la ruta del logo
                    $query = "UPDATE companies SET imageLogo = '$filePath' WHERE id = '$companyId'";
                    if ($mysqli->query($query) === TRUE) {
                        echo json_encode(array("success" => true, "message" => "Logo subido y guardado correctamente.", "filePath" => $filePath));
                    } else {
                        echo json_encode(array("success" => false, "error" => "Error al guardar el logo en la base de datos: " . $mysqli->error));
                    }
                } else {
                    echo json_encode(array("success" => false, "error" => "El archivo no existe en la ruta especificada."));
                }
            } else {
                echo json_encode(array("success" => false, "error" => "Error al mover el archivo subido."));
            }
        } else {
            echo json_encode(array("success" => false, "error" => "Error en la subida del archivo: " . $logo['error']));
        }
    } else {
        echo json_encode(array("success" => false, "error" => "No se proporcionaron todos los datos necesarios."));
    }
} else {
    echo json_encode(array("success" => false, "error" => "Método de solicitud no permitido."));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
