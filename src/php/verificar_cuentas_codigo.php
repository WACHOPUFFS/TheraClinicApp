<?php

include_once 'conexion.php';
include_once 'cors.php';

function eliminarCodigosAntiguos($mysqli) {
    // Preparar la consulta SQL para eliminar los códigos que tienen más de 5 días desde su creación
    $sqlToDelete = "DELETE FROM usersCodes WHERE creationDate <= DATE_SUB(NOW(), INTERVAL 5 DAY)";

    if (!$mysqli->query($sqlToDelete)) {
        throw new Exception("Error al eliminar los códigos: " . $mysqli->error);
    }

    return true;
}

try {
    // Ejecutar la función para eliminar los códigos antiguos
    eliminarCodigosAntiguos($mysqli);

    // Devolver un mensaje JSON indicando que se eliminaron los códigos
    $mensaje = array('mensaje' => 'Se eliminaron los códigos que tenían más de 5 días de antigüedad.');
    header('Content-Type: application/json');
    echo json_encode($mensaje);
} catch (Exception $e) {
    // Manejar errores y devolver un mensaje JSON con el error
    $mensaje = array('error' => $e->getMessage());
    header('Content-Type: application/json', true, 500);
    echo json_encode($mensaje);
} finally {
    // Cerrar la conexión a la base de datos
    $mysqli->close();
}

?>
