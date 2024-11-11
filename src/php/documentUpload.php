<?php
include_once 'cors.php';
include_once 'conexion.php';

function generateUniqueFileName($path, $filename) {
    $fileParts = pathinfo($filename);
    $baseName = $fileParts['filename'];
    $extension = isset($fileParts['extension']) ? '.' . $fileParts['extension'] : '';
    $counter = 1;

    while (file_exists($path . $baseName . $extension)) {
        $baseName = $fileParts['filename'] . '_' . $counter;
        $counter++;
    }

    return $baseName . $extension;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && isset($_POST['userId']) && isset($_POST['companyId']) && isset($_POST['tareaId'])) {
        $userId = $mysqli->real_escape_string($_POST['userId']);
        $companyId = $mysqli->real_escape_string($_POST['companyId']);
        $tareaId = $mysqli->real_escape_string($_POST['tareaId']);
        $file = $_FILES['file'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/files/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uniqueFileName = generateUniqueFileName($uploadDir, $file['name']);
            $filePath = $uploadDir . $uniqueFileName;

            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                if (file_exists($filePath)) {
                    // Verificar si ya existe un registro para esta combinación de user_id, company_id y tarea_id
                    $checkQuery = "SELECT id FROM documentUploads WHERE user_id='$userId' AND company_id='$companyId' AND tarea_id='$tareaId'";
                    $result = $mysqli->query($checkQuery);

                    if ($result->num_rows > 0) {
                        // Actualizar el registro existente
                        $updateQuery = "UPDATE documentUploads SET file_path='$filePath', upload_date=NOW(), estado='cargado', comentario=NULL WHERE user_id='$userId' AND company_id='$companyId' AND tarea_id='$tareaId'";
                        if ($mysqli->query($updateQuery) === TRUE) {
                            echo json_encode(array("success" => true, "message" => "Archivo subido y actualizado correctamente.", "filePath" => $filePath));
                        } else {
                            echo json_encode(array("success" => false, "error" => "Error al actualizar la base de datos: " . $mysqli->error));
                        }
                    } else {
                        // Insertar un nuevo registro
                        $insertQuery = "INSERT INTO documentUploads (user_id, company_id, tarea_id, file_path, upload_date, estado) VALUES ('$userId', '$companyId', '$tareaId', '$filePath', NOW(), 'cargado')";
                        if ($mysqli->query($insertQuery) === TRUE) {
                            echo json_encode(array("success" => true, "message" => "Archivo subido y guardado correctamente.", "filePath" => $filePath));
                        } else {
                            echo json_encode(array("success" => false, "error" => "Error al guardar la ruta en la base de datos: " . $mysqli->error));
                        }
                    }
                } else {
                    echo json_encode(array("success" => false, "error" => "El archivo no existe en la ruta especificada."));
                }
            } else {
                echo json_encode(array("success" => false, "error" => "Error al mover el archivo subido."));
            }
        } else {
            echo json_encode(array("success" => false, "error" => "Error en la subida del archivo: " . $file['error']));
        }
    } else {
        echo json_encode(array("success" => false, "error" => "No se proporcionaron todos los datos necesarios."));
    }
} else {
    echo json_encode(array("success" => false, "error" => "Método de solicitud no permitido."));
}

$mysqli->close();
?>
