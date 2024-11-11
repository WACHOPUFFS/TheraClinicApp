<?php

// Incluir la configuración de la base de datos
include_once 'cors.php';
include_once 'conexion.php';

// Obtener los datos del cuerpo de la solicitud POST
$data = json_decode(file_get_contents("php://input"));

// Verificar si los datos necesarios están presentes
if (isset($data->empresa) && isset($data->empresaSocio) && isset($data->role)) {
    // Obtener los valores de la solicitud y sanitizar
    $empresa = filter_var($data->empresa, FILTER_SANITIZE_NUMBER_INT);
    $empresaSocio = filter_var($data->empresaSocio, FILTER_SANITIZE_NUMBER_INT);
    $role = filter_var($data->role, FILTER_SANITIZE_STRING);

    // Consulta SQL para obtener el ID del rol
    $sql_busqueda_rol = "SELECT id FROM roles WHERE roleName = ?";
    if ($stmt_busqueda_rol = $mysqli->prepare($sql_busqueda_rol)) {
        $stmt_busqueda_rol->bind_param("s", $role);
        $stmt_busqueda_rol->execute();
        $resultado_busqueda_rol = $stmt_busqueda_rol->get_result();
        if ($resultado_busqueda_rol->num_rows > 0) {
            $rol = $resultado_busqueda_rol->fetch_assoc()['id'];
            $stmt_busqueda_rol->close();

            // Consulta SQL para insertar los datos en la tabla user_company_roles (consulta original)
            $sql_original = "INSERT INTO user_company_roles (company_id, association_id, role_id) VALUES (?, ?, ?)";
            $sql_modificada = "INSERT INTO user_company_roles (association_id, company_id, role_id) VALUES (?, ?, ?)";

            if ($stmt_original = $mysqli->prepare($sql_original)) {
                $stmt_original->bind_param("iii", $empresa, $empresaSocio, $rol);
                $original_success = $stmt_original->execute();
                $stmt_original->close();
            } else {
                $original_success = false;
            }

            if ($stmt_modificada = $mysqli->prepare($sql_modificada)) {
                $stmt_modificada->bind_param("iii", $empresaSocio, $empresa, $rol); // Cambio en el orden de los parámetros
                $modificada_success = $stmt_modificada->execute();
                $stmt_modificada->close();
            } else {
                $modificada_success = false;
            }

            // Verificar si ambas inserciones fueron exitosas
            if ($original_success && $modificada_success) {
                $response = array(
                    "success" => true,
                    "message" => "Asociaciones de empresas insertadas correctamente"
                );
            } else {
                $response = array(
                    "success" => false,
                    "message" => "Error al insertar las asociaciones de empresas: " . $mysqli->error
                );
            }
        } else {
            $response = array(
                "success" => false,
                "message" => "Rol no encontrado"
            );
        }
    } else {
        $response = array(
            "success" => false,
            "message" => "Error al preparar la consulta del rol: " . $mysqli->error
        );
    }

    // Cerrar la conexión a la base de datos
    $mysqli->close();
} else {
    // Datos insuficientes en la solicitud
    $response = array(
        "success" => false,
        "message" => "Datos insuficientes en la solicitud"
    );
}

// Devolver la respuesta en formato JSON
echo json_encode($response);

?>
