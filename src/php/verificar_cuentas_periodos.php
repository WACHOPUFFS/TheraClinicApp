<?php

include_once 'conexion.php';
include_once 'cors.php';

function actualizarEstadoUsuario($mysqli, $estadoActual, $nuevoEstado) {
    // Preparar la consulta para seleccionar usuarios por estado actual
    $stmtSelect = $mysqli->prepare("SELECT id FROM users WHERE state = ?");
    $stmtSelect->bind_param('s', $estadoActual);
    $stmtSelect->execute();
    $resultSelect = $stmtSelect->get_result();

    // Preparar la consulta para verificar periodos activos
    $stmtPeriodo = $mysqli->prepare("SELECT COUNT(*) FROM periodos WHERE usuario_id = ? AND fecha_inicio <= CURDATE() AND fecha_fin >= CURDATE()");

    // Preparar la consulta para actualizar el estado del usuario
    $stmtUpdate = $mysqli->prepare("UPDATE users SET state = ? WHERE id = ?");

    while ($row = $resultSelect->fetch_assoc()) {
        $usuario_id = $row['id'];

        // Inicializar la variable $periodosActivos
        $periodosActivos = 0;

        // Verificar periodos activos
        $stmtPeriodo->bind_param('i', $usuario_id);
        $stmtPeriodo->execute();
        $stmtPeriodo->bind_result($periodosActivos);
        $stmtPeriodo->fetch();

        // Si el usuario es 'active' y no tiene periodos activos, o si es 'nonActive' y tiene periodos activos, actualizar su estado
        if (($estadoActual === 'active' && $periodosActivos == 0) || ($estadoActual === 'nonActive' && $periodosActivos > 0)) {
            $stmtUpdate->bind_param('si', $nuevoEstado, $usuario_id);
            if (!$stmtUpdate->execute()) {
                die("Error al actualizar el estado del usuario: " . $stmtUpdate->error);
            }
        }
    }

    // Cerrar los statements
    $stmtSelect->close();
    $stmtPeriodo->close();
    $stmtUpdate->close();
}

// Actualizar estados de usuarios activos a no activos
actualizarEstadoUsuario($mysqli, 'active', 'nonActive');

// Actualizar estados de usuarios no activos a activos
actualizarEstadoUsuario($mysqli, 'nonActive', 'active');

$mensaje = array('mensaje' => 'Se realizaron las actualizaciones de estado.');
header('Content-Type: application/json');
echo json_encode($mensaje);

$mysqli->close();

?>
