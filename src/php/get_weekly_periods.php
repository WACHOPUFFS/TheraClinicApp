<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se han pasado los parámetros necesarios
if (!isset($_GET['company_id']) || !isset($_GET['period_type_id'])) {
    echo json_encode(['error' => 'company_id and period_type_id parameters are required']);
    exit;
}

$company_id = intval($_GET['company_id']);
$period_type_id = intval($_GET['period_type_id']);

// Consulta SQL para obtener las semanas filtradas por company_id y period_type_id
$sql = "
    SELECT period_id, fiscal_year, period_number, start_date, end_date 
    FROM payroll_periods 
    WHERE company_id = ? AND period_type_id = ?
    ORDER BY fiscal_year DESC, period_number ASC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $company_id, $period_type_id);
$stmt->execute();
$result = $stmt->get_result();

$periods = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Generar el número de la semana (week_number)
        $week_number = $row['fiscal_year'] . str_pad($row['period_number'], 2, '0', STR_PAD_LEFT);

        // Agregar el periodo al arreglo de periodos incluyendo el period_number
        $periods[] = [
            'period_id' => $row['period_id'],
            'week_number' => $week_number,
            'period_number' => $row['period_number'], // Incluir el número de periodo
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date']
        ];
    }
} else {
    echo json_encode([]);
    exit;
}

// Retornar los periodos en formato JSON
echo json_encode($periods);

$stmt->close();
$mysqli->close();
?>
