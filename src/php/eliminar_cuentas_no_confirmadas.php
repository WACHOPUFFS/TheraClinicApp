<?php

// Incluir la configuración de la base de datos
include_once 'conexion.php';
include_once 'cors.php';
include_once 'enviar_correo.php'; // Incluir el archivo enviar_correo.php

// Calcular la fecha hace 3 días
$fechaLimiteAdvertencia = date('Y-m-d H:i:s', strtotime('-3 days'));

// Consulta SQL para seleccionar las cuentas no confirmadas creadas hace más de 3 días
$sqlAdvertencia = "SELECT email, token FROM users WHERE confirmed = 0 AND creation_date < ?";

// Preparar la consulta
if ($stmtAdvertencia = $mysqli->prepare($sqlAdvertencia)) {
    $stmtAdvertencia->bind_param("s", $fechaLimiteAdvertencia);
    $stmtAdvertencia->execute();
    $stmtAdvertencia->bind_result($correoUsuario, $tokenUsuario);

    // Verificar si se encontraron cuentas para enviar advertencias
    while ($stmtAdvertencia->fetch()) {
        // Enviar advertencia
        enviarCorreo($correoUsuario, $tokenUsuario, 'advertencia');
    }

    $stmtAdvertencia->close();
}

// Calcular la fecha hace 5 días
$fechaLimiteEliminacion = date('Y-m-d H:i:s', strtotime('-5 days'));

// Consulta SQL para seleccionar las cuentas no confirmadas creadas hace más de 5 días
$sqlEliminacion = "SELECT id, email FROM users WHERE confirmed = 0 AND creation_date < ?";

// Preparar la consulta
if ($stmtEliminacion = $mysqli->prepare($sqlEliminacion)) {
    $stmtEliminacion->bind_param("s", $fechaLimiteEliminacion);
    $stmtEliminacion->execute();
    $stmtEliminacion->bind_result($idCuenta, $correoUsuario);

    // Array para almacenar los datos de las cuentas no confirmadas
    $cuentasNoConfirmadas = array();

    // Verificar si se encontraron cuentas para eliminar
    while ($stmtEliminacion->fetch()) {
        // Almacenar los datos de la cuenta en el array
        $cuentasNoConfirmadas[] = array("id" => $idCuenta, "email" => $correoUsuario);

        // Enviar correo de eliminación
        enviarCorreo($correoUsuario, null, 'eliminacion'); // No necesitas un token para el correo de eliminación

        // Eliminar la cuenta
        $sqlDelete = "DELETE FROM users WHERE id = ?";
        if ($stmtDelete = $mysqli->prepare($sqlDelete)) {
            $stmtDelete->bind_param("i", $idCuenta);
            if (!$stmtDelete->execute()) {
                // Manejar errores en la eliminación de cuentas
                die("Error al eliminar la cuenta: " . $stmtDelete->error);
            }
            $stmtDelete->close();
        } else {
            // Manejar errores en la preparación de la consulta de eliminación
            die("Error al preparar la consulta de eliminación: " . $mysqli->error);
        }
    }

    $stmtEliminacion->close();

    // Devolver los datos en formato JSON
    header('Content-Type: application/json');
    echo json_encode($cuentasNoConfirmadas);
} else {
    // Si no se encontraron cuentas para eliminar, devolver un mensaje JSON indicando esto
    $mensaje = array('mensaje' => 'No se encontraron cuentas para eliminar.');
    header('Content-Type: application/json');
    echo json_encode($mensaje);
}

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
