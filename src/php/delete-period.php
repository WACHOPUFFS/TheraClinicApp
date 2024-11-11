<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';  // Si tienes configurado CORS
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['period_type_id'])) {
        echo json_encode(['error' => 'Parámetro period_type_id es requerido']);
        exit;
    }

    $period_type_id = intval($data['period_type_id']);

    // Eliminar todos los periodos de nómina asociados con este tipo de periodo
    $sql_payroll_periods = "DELETE FROM payroll_periods WHERE period_type_id = $period_type_id";
    if ($mysqli->query($sql_payroll_periods) === TRUE) {
        // Proceder a eliminar el tipo de periodo solo si los periodos de nómina se eliminaron correctamente
        $sql_period_types = "DELETE FROM period_types WHERE period_type_id = $period_type_id";
        if ($mysqli->query($sql_period_types) === TRUE) {
            echo json_encode(['success' => true, 'message' => 'Periodo y periodos de nómina asociados eliminados correctamente']);
        } else {
            echo json_encode(['error' => 'Error al eliminar el tipo de periodo: ' . $mysqli->error]);
        }
    } else {
        echo json_encode(['error' => 'Error al eliminar los periodos de nómina: ' . $mysqli->error]);
    }
} else {
    echo json_encode(['error' => 'Método no permitido']);
}

$mysqli->close();
?>
