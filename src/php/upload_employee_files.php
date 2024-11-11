<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

$employee_id = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : null;
if ($employee_id === null) {
    echo json_encode(['error' => 'Employee ID is required']);
    exit;
}

$upload_dir = 'uploads/employee_files/'; // Directorio donde se guardarán los archivos

$allowed_types = ['ineFrente', 'ineReverso', 'constanciaFiscal', 'numSeguroSocialArchivo', 'actaNacimiento', 'comprobanteDomicilio', 'cuentaInterbancaria', 'retencionInfonavit', 'antecedentesPenales', 'comprobanteEstudios'];

foreach ($_FILES as $file_type => $file) {
    if (in_array($file_type, $allowed_types) && $file['error'] == 0) {
        $file_name = basename($file['name']);
        $file_path = $upload_dir . $file_name;

        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $stmt = $mysqli->prepare("INSERT INTO employee_files (employee_id, file_type, file_name, file_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $employee_id, $file_type, $file_name, $file_path);
            $stmt->execute();
            $stmt->close();
        } else {
            echo json_encode(['error' => 'Failed to move uploaded file for ' . $file_type]);
            exit;
        }
    } else {
        echo json_encode(['error' => 'Invalid file or upload error for ' . $file_type]);
        exit;
    }
}

echo json_encode(['success' => 'Files uploaded successfully']);

$mysqli->close();
?>
