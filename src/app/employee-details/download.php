<?php
// Habilita CORS para permitir el acceso desde tu aplicación
require_once 'cors.php';

// Verifica si se pasó el nombre del archivo
if (isset($_GET['file_path'])) {
    // Sanitiza el nombre del archivo (recomendado por seguridad)
    $filePath = urldecode($_GET['file_path']);
    $fullPath = __DIR__ . "/uploads/" . basename($filePath); // ajusta según la estructura del servidor

    // Verifica si el archivo existe
    if (file_exists($fullPath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    } else {
        echo json_encode(["success" => false, "error" => "Archivo no encontrado"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Parámetro file_path requerido"]);
}
?>
