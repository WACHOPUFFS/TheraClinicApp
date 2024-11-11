<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y manejo de CORS
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se envió una solicitud POST con un archivo
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Verificar si se ha enviado el employee_id
if (!isset($_POST['employee_id'])) {
    echo json_encode(['success' => false, 'error' => 'employee_id is required']);
    exit;
}

$employee_id = intval($_POST['employee_id']); // Convertir employee_id a número entero

// Verificar si se ha enviado un archivo
if (!isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    exit;
}

// Obtener el archivo subido
$file = $_FILES['file'];
$file_name = basename($file['name']);
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_error = $file['error'];

// Verificar si hay errores en el archivo
if ($file_error !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Error uploading file']);
    exit;
}

// Definir el directorio donde se almacenarán las fotos
$upload_dir = 'uploads/employee_photos/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Crear el directorio si no existe
}

// Generar un nombre único para el archivo
$unique_file_name = uniqid() . '_' . $file_name;
$upload_path = $upload_dir . $unique_file_name;

// Mover el archivo a la carpeta de destino
if (!move_uploaded_file($file_tmp, $upload_path)) {
    echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file']);
    exit;
}

// Construir la URL completa
$base_url = 'https://www.siinad.mx/php/'; // Cambia esto a tu URL base
$photo_url = $base_url . $upload_path;

// Guardar la URL en la base de datos
$sql = "
    UPDATE employees 
    SET photo = '$photo_url'
    WHERE employee_id = $employee_id
";

// Ejecutar la consulta
if ($mysqli->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Photo uploaded and path saved successfully', 'photo_url' => $photo_url]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save photo path to database']);
}

// Cerrar la conexión
$mysqli->close();

?>
