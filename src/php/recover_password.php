<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

require_once 'cors.php';
require_once 'conexion.php';

// Configurar cabeceras para la respuesta JSON
header('Content-Type: application/json');

// Recibir datos del formulario de recuperación de contraseña
$email = trim($_POST['email']);

// Verificar si se proporcionó un correo electrónico válido
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(array('success' => false, 'message' => 'Correo electrónico no válido'));
    exit();
}

// Consultar la base de datos para verificar si el correo electrónico existe
$query = "SELECT * FROM users WHERE email = ?";
if ($stmt = $mysqli->prepare($query)) {
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El correo electrónico existe en la base de datos
        $row = $result->fetch_assoc();
        $userId = $row['id'];

        // Generar un token único para la solicitud de recuperación de contraseña
        $token = md5(uniqid(rand(), true));

        // Guardar el token en la base de datos junto con el correo electrónico del usuario
        $updateTokenSql = "UPDATE users SET password_reset_token = ? WHERE id = ?";
        if ($updateStmt = $mysqli->prepare($updateTokenSql)) {
            $updateStmt->bind_param('si', $token, $userId);
            $updateStmt->execute();
            $updateStmt->close();

            // Enviar correo electrónico al usuario con el enlace de recuperación de contraseña
            $mail = new PHPMailer(true);

            try {
                // Configurar el servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'mail.sinsetec.com.mx';
                $mail->SMTPAuth = true;
                $mail->Username = 'tu_correo@sinsetec.com.mx';
                $mail->Password = 'tu_contraseña';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Configurar el remitente y el destinatario
                $mail->setFrom('tu_correo@sinsetec.com.mx', 'Tu Nombre');
                $mail->addAddress($email);

                // Configurar el correo electrónico
                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de contraseña';
                $mail->Body = 'Para restablecer tu contraseña, haz clic en el siguiente enlace: <a href="https://ctrlobra.sinsetec.com.mx/php/reset_password.php?token=' . $token . '">Restablecer contraseña</a>';

                // Enviar el correo electrónico
                $mail->send();

                // Éxito al enviar el correo electrónico
                echo json_encode(array('success' => true, 'message' => 'Se ha enviado un correo electrónico con instrucciones para restablecer tu contraseña'));
            } catch (Exception $e) {
                // Error al enviar el correo electrónico
                echo json_encode(array('success' => false, 'message' => 'Error al enviar el correo electrónico: ' . $mail->ErrorInfo));
            }
        } else {
            // Error al preparar la consulta de actualización
            echo json_encode(array('success' => false, 'message' => 'Error al actualizar el token de recuperación'));
        }
    } else {
        // El correo electrónico no existe en la base de datos
        echo json_encode(array('success' => false, 'message' => 'El correo electrónico no está asociado a ninguna cuenta'));
    }

    // Cerrar la declaración de selección
    $stmt->close();
} else {
    // Error al preparar la consulta de selección
    echo json_encode(array('success' => false, 'message' => 'Error al consultar la base de datos'));
}

// Cerrar la conexión a la base de datos
$mysqli->close();

?>
