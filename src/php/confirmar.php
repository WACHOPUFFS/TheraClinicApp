<?php

// Incluir la configuración de la base de datos
include_once 'conexion.php';

// Verificar si se recibió un token válido por GET
if (isset($_GET['token'])) {
    // Obtener y sanitizar el token de la URL
    $token = $mysqli->real_escape_string($_GET['token']);

    // Consulta SQL para buscar el token en la base de datos
    $sql = "SELECT * FROM users WHERE token = ?";
    $stmt = $mysqli->prepare($sql);

    // Verificar si la preparación de la consulta fue exitosa
    if ($stmt) {
        // Vincular el parámetro y ejecutar la consulta
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar si se encontró un usuario con el token proporcionado
        if ($result->num_rows > 0) {
            // Actualizar el estado de la cuenta del usuario como confirmada
            $sql_update = "UPDATE users SET confirmed = '1' WHERE token = ?";
            $stmt_update = $mysqli->prepare($sql_update);

            if ($stmt_update) {
                $stmt_update->bind_param("s", $token);
                $stmt_update->execute();
                $stmt_update->close();

                // Mostrar un mensaje de confirmación
                echo "¡Tu cuenta ha sido confirmada exitosamente!";
            } else {
                // Manejar el error al preparar la consulta de actualización
                echo "Error al preparar la consulta de actualización: " . $mysqli->error;
            }
        } else {
            // Mostrar un mensaje de error si no se encontró el token en la base de datos
            echo "Token inválido. No se pudo confirmar la cuenta.";
        }

        $stmt->close();
    } else {
        // Manejar el error al preparar la consulta de selección
        echo "Error al preparar la consulta de selección: " . $mysqli->error;
    }
} else {
    // Mostrar un mensaje de error si no se recibió un token válido por GET
    echo "No se proporcionó un token válido.";
}

// Cerrar la conexión a la base de datos
$mysqli->close();

?>
