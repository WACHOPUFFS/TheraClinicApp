<?php
// PHP script para manejar la carga de archivos
include_once 'cors.php';
include_once 'conexion.php';

// Verifica si se ha enviado algún archivo
if ($_FILES['file']['error'] !== UPLOAD_ERR_OK || !isset($_FILES['file']['tmp_name'])) {
    die(json_encode(array("success" => false, "message" => 'Error al cargar el archivo.')));
}

$uploadsDirectory = 'uploads/'; // Carpeta donde se guardarán los archivos

// Genera un nombre aleatorio para el archivo
$randomNumber = rand(1000, 9999);
$uploadedFileName = $randomNumber . '_' . basename($_FILES['file']['name']);
$uploadedFilePath = $uploadsDirectory . $uploadedFileName;

// Mueve el archivo cargado a la carpeta de destino
if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadedFilePath)) {
    die(json_encode(array("success" => false, "message" => 'Error al guardar el archivo.')));
}

// Obtén el nombre de usuario y la empresa del remitente del formulario
$sender = $_POST['sender'];
$senderEmpresa = $_POST['sender_empresa'];

// Obtén la empresa destino del formulario
$recipient = $_POST['recipient'];

// Inserta la información del archivo enviado en la base de datos
$query = "INSERT INTO archivos_enviados (nombre_archivos, ubicacion_archivos, usuario_envio, empresa_destino) VALUES (?, ?, ?, ?)";
$stmt = $mysqli->prepare($query);

if ($stmt === false) {
    die(json_encode(array("success" => false, "message" => 'Error en la preparación de la consulta.')));
}

$stmt->bind_param('ssss', $uploadedFileName, $uploadedFilePath, $sender, $recipient);

if (!$stmt->execute()) {
    die(json_encode(array("success" => false, "message" => 'Error al insertar la información del archivo en la base de datos.')));
}

echo json_encode(array("success" => true, "message" => 'Archivo cargado exitosamente en el servidor como ' . $uploadedFileName . ' y la información se ha guardado en la base de datos.'));
?>
