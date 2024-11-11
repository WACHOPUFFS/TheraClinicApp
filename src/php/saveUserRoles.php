<?php
include_once 'cors.php';
include_once 'conexion.php';

// Configurar cabeceras para la respuesta JSON
header('Content-Type: application/json');

// Decodificar los datos JSON recibidos en el cuerpo del POST
$data = json_decode(file_get_contents("php://input"));

// Verifica si se recibieron los datos del POST correctamente
if (isset($data->companyId, $data->socioComercialId, $data->nuevoRol)) {
    // Obtén los datos necesarios del POST
    $companyId = $data->companyId;
    $socioComercialId = $data->socioComercialId;
    $nuevoRolId = $data->nuevoRol; // Ahora 'nuevoRol' es el ID del rol directamente

    // Actualiza el rol del usuario en la tabla user_company_roles
    $updateUserRoleQuery = "UPDATE user_company_roles SET role_id = $nuevoRolId WHERE company_id = $socioComercialId AND association_id = $companyId";
    if ($mysqli->query($updateUserRoleQuery)) {
        // La actualización del rol fue exitosa

        // Determina el rol opuesto basado en el ID (si es necesario)
        $rolContrarioId = null;
        if ($nuevoRolId == 3) { // Proveedor
            $rolContrarioId = 2; // Cliente
        } elseif ($nuevoRolId == 2) { // Cliente
            $rolContrarioId = 3; // Proveedor
        } elseif ($nuevoRolId == 4) { // Cliente-Proveedor
            $rolContrarioId = 4; // Cliente-Proveedor
        }

        if ($rolContrarioId) {
            // Actualiza la asociación de roles con el rol contrario
            $updateAssociationQuery = "UPDATE user_company_roles SET role_id = $rolContrarioId WHERE association_id = $socioComercialId AND company_id = $companyId";
            if ($mysqli->query($updateAssociationQuery)) {
                // La actualización de la asociación de roles fue exitosa
                $response['success'] = true;
                $response['message'] = "Rol y asociación de roles actualizados correctamente.";
            } else {
                // Error al ejecutar la consulta de actualización de la asociación de roles
                $response['success'] = false;
                $response['message'] = "Error al actualizar la asociación de roles.";
            }
        } else {
            // El nuevo rol no tiene un rol contrario definido
            $response['success'] = false;
            $response['message'] = "El nuevo rol no tiene un rol contrario definido.";
        }
    } else {
        // Error al ejecutar la consulta de actualización del rol
        $response['success'] = false;
        $response['message'] = "Error al actualizar el rol.";
    }
} else {
    // Datos del POST no recibidos correctamente
    $response['success'] = false;
    $response['message'] = "Datos del POST incompletos.";
}

// Envía la respuesta JSON al cliente
echo json_encode($response);

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
