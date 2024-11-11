<?php
include_once 'cors.php';
include_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se ha enviado un archivo
    if (isset($_FILES['avatar']) && isset($_POST['userId'])) {
        $userId = $mysqli->real_escape_string($_POST['userId']);
        $avatar = $_FILES['avatar'];

        // Verificar si el archivo se ha subido correctamente
        if ($avatar['error'] === UPLOAD_ERR_OK) {
            // Definir la ruta de la carpeta donde se guardarán los avatares
            $uploadDir = 'uploads/avatars/';
            // Asegurarse de que la carpeta exista
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Definir la ruta completa del archivo
            $filePath = $uploadDir . basename($avatar['name']);

            // Mover el archivo a la carpeta de destino
            if (move_uploaded_file($avatar['tmp_name'], $filePath)) {
                // Verificar la ruta del archivo
                if (file_exists($filePath)) {
                    // Actualizar la base de datos con la ruta del avatar
                    $query = "UPDATE users SET avatar = '$filePath' WHERE id = '$userId'";
                    if ($mysqli->query($query) === TRUE) {
                        echo json_encode(array("success" => true, "message" => "Avatar uploaded and saved successfully.", "filePath" => $filePath));
                    } else {
                        echo json_encode(array("success" => false, "error" => "Error saving avatar in database: " . $mysqli->error));
                    }
                } else {
                    echo json_encode(array("success" => false, "error" => "File does not exist at the specified path."));
                }
            } else {
                echo json_encode(array("success" => false, "error" => "Error moving uploaded file."));
            }
        } else {
            echo json_encode(array("success" => false, "error" => "File upload error: " . $avatar['error']));
        }
    } else {
        echo json_encode(array("success" => false, "error" => "All necessary data not provided."));
    }
} else {
    echo json_encode(array("success" => false, "error" => "Request method not allowed."));
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
