<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y habilitar CORS
require_once 'cors.php';
require_once 'conexion.php';

// Obtener los parámetros de la compañía y periodo
$companyId = $_GET['company_id'];
$periodId = $_GET['period_id'];

// Verificar que se recibieron los parámetros necesarios
if (!isset($companyId, $periodId)) {
    echo json_encode(['error' => 'Faltan parámetros necesarios.']);
    exit;
}

// Sanitizar las entradas para evitar inyecciones SQL
$companyId = intval($companyId);
$periodId = intval($periodId);

$diasOldest = [];

// Consulta para obtener la semana más antigua con días no confirmados
$sqlOldestNonConfirmedWeek = "SELECT dc.week_number
                              FROM day_confirmations dc
                              WHERE dc.company_id = $companyId 
                                AND dc.period_type_id = $periodId
                                AND dc.week_number NOT IN (
                                  SELECT week_number FROM week_confirmations
                                  WHERE company_id = $companyId
                                  AND period_type_id = $periodId
                                )
                              ORDER BY dc.week_number ASC
                              LIMIT 1";

$resultOldestNonConfirmedWeek = $mysqli->query($sqlOldestNonConfirmedWeek);

// Verificar si hay una semana más antigua no confirmada
if ($resultOldestNonConfirmedWeek->num_rows > 0) {
    $oldestNonConfirmedWeek = $resultOldestNonConfirmedWeek->fetch_assoc()['week_number'];

    // Obtener todos los días de la semana más antigua no confirmada
    $sqlNonConfirmedDays = "SELECT dc.*, pp.start_date AS period_start_date, pp.end_date AS period_end_date
                            FROM day_confirmations dc
                            JOIN payroll_periods pp ON dc.period_id = pp.period_id
                            WHERE dc.company_id = $companyId 
                              AND dc.period_type_id = $periodId
                              AND dc.week_number = $oldestNonConfirmedWeek";

    $resultNonConfirmedDays = $mysqli->query($sqlNonConfirmedDays);

    while ($dayRow = $resultNonConfirmedDays->fetch_assoc()) {
        $diasOldest[] = $dayRow;
    }
} else {
    // Si no hay semanas más recientes no confirmadas, obtener la última semana confirmada
    $sqlLastConfirmedWeek = "SELECT dc.week_number
                             FROM day_confirmations dc
                             JOIN week_confirmations wc ON dc.week_number = wc.week_number
                             WHERE dc.company_id = $companyId 
                               AND dc.period_type_id = $periodId
                             ORDER BY dc.week_number DESC
                             LIMIT 1";

    $resultLastConfirmedWeek = $mysqli->query($sqlLastConfirmedWeek);

    if ($resultLastConfirmedWeek->num_rows > 0) {
        $lastConfirmedWeek = $resultLastConfirmedWeek->fetch_assoc()['week_number'];

        // Obtener todos los días de la última semana confirmada
        $sqlConfirmedDays = "SELECT dc.*, pp.start_date AS period_start_date, pp.end_date AS period_end_date
                             FROM day_confirmations dc
                             JOIN payroll_periods pp ON dc.period_id = pp.period_id
                             WHERE dc.company_id = $companyId 
                               AND dc.period_type_id = $periodId
                               AND dc.week_number = $lastConfirmedWeek";

        $resultConfirmedDays = $mysqli->query($sqlConfirmedDays);

        while ($dayRow = $resultConfirmedDays->fetch_assoc()) {
            $diasOldest[] = $dayRow;
        }
    }
}

// Devolver los días como JSON
echo json_encode($diasOldest);

$mysqli->close();
?>
