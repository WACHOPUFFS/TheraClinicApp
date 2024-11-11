<?php

// Incluir la configuración de la base de datos y manejo de CORS
include_once 'cors.php';
include_once 'conexion.php';

// Obtener el parámetro association_id de la solicitud GET
$associationId = isset($_GET['association_id']) ? $mysqli->real_escape_string($_GET['association_id']) : null;

// Verificar si se recibió el parámetro association_id
if ($associationId) {
    // Consulta SQL para obtener la información de los socios comerciales
    $sql = "
        SELECT 
            u.id, 
            u.username, 
            u.name AS name, 
            u.email, 
            u.phone, 
            c.nameCompany, 
            r.roleName, 
            p.fecha_inicio, 
            p.fecha_fin, 
            c.rfc AS companyRFC,
            c.id AS idCompany
        FROM 
            users u
        INNER JOIN 
            user_company_roles uc ON u.id = uc.user_id
        INNER JOIN 
            companies c ON uc.company_id = c.id
        INNER JOIN 
            periodos p ON u.id = p.usuario_id
        INNER JOIN 
            roles r ON uc.role_id = r.id
        WHERE 
            uc.association_id = '$associationId'
    ";

    $result = $mysqli->query($sql);

    if ($result) {
        // Crear un array para almacenar los resultados
        $data = array();

        // Recorrer los resultados y almacenarlos en el array
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // Enviar los resultados como respuesta en formato JSON
        echo json_encode($data);
    } else {
        // Enviar una respuesta de error si hubo un problema con la consulta
        $response = array("success" => false, "message" => "Error al ejecutar la consulta: " . $mysqli->error);
        echo json_encode($response);
    }
} else {
    // Enviar una respuesta de error si no se recibió el parámetro association_id
    $response = array("success" => false, "message" => "Parámetro association_id es requerido");
    echo json_encode($response);
}

// Cerrar la conexión a la base de datos
$mysqli->close();

?>