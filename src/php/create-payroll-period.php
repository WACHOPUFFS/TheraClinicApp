<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';  // Si tienes configurado CORS
require_once 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($data['company_id']) || !isset($data['period_type_id']) || !isset($data['fiscal_year'])) {
        echo json_encode(['error' => 'Parámetros faltantes']);
        exit;
    }

    $company_id = intval($data['company_id']);
    $period_type_id = isset($data['period_type_id']) ? intval($data['period_type_id']) : null;
    $period_number = isset($data['period_number']) ? intval($data['period_number']) : null;
    $fiscal_year = isset($data['fiscal_year']) ? intval($data['fiscal_year']) : null;
    $month = isset($data['month']) ? intval($data['month']) : null;
    $payment_days = isset($data['payment_days']) ? intval($data['payment_days']) : null;
    $rest_days = isset($data['rest_days']) ? intval($data['rest_days']) : null;
    $interface_check = isset($data['interface_check']) ? intval($data['interface_check']) : null;
    $net_modification = isset($data['net_modification']) ? floatval($data['net_modification']) : null;
    $calculated = isset($data['calculated']) ? intval($data['calculated']) : null;
    $affected = isset($data['affected']) ? intval($data['affected']) : null;
    $start_date = isset($data['start_date']) ? $mysqli->real_escape_string($data['start_date']) : null;
    $end_date = isset($data['end_date']) ? $mysqli->real_escape_string($data['end_date']) : null;
    $fiscal_start = isset($data['fiscal_start']) ? intval($data['fiscal_start']) : null;
    $month_start = isset($data['month_start']) ? intval($data['month_start']) : null;
    $month_end = isset($data['month_end']) ? intval($data['month_end']) : null;
    $fiscal_end = isset($data['fiscal_end']) ? intval($data['fiscal_end']) : null;
    $timestamp = isset($data['timestamp']) ? $mysqli->real_escape_string($data['timestamp']) : null;
    $imss_bimonthly_start = isset($data['imss_bimonthly_start']) ? intval($data['imss_bimonthly_start']) : null;
    $imss_bimonthly_end = isset($data['imss_bimonthly_end']) ? intval($data['imss_bimonthly_end']) : null;
    $payment_date = isset($data['payment_date']) ? $mysqli->real_escape_string($data['payment_date']) : null;

    if ($period_type_id === null || $period_number === null || $fiscal_year === null) {
        echo json_encode(['error' => 'Datos insuficientes para insertar el periodo de nómina']);
        exit;
    }

    $sql = "INSERT INTO payroll_periods (company_id, period_type_id, period_number, fiscal_year, month, payment_days, rest_days, interface_check, net_modification, calculated, affected, start_date, end_date, fiscal_start, month_start, month_end, fiscal_end, timestamp, imss_bimonthly_start, imss_bimonthly_end, payment_date)
            VALUES ($company_id, $period_type_id, $period_number, $fiscal_year, $month, $payment_days, $rest_days, $interface_check, $net_modification, $calculated, $affected, '$start_date', '$end_date', $fiscal_start, $month_start, $month_end, $fiscal_end, '$timestamp', $imss_bimonthly_start, $imss_bimonthly_end, '$payment_date')";

    if ($mysqli->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Periodo de nómina guardado correctamente']);
    } else {
        echo json_encode(['error' => 'Error al insertar el periodo de nómina: ' . $mysqli->error]);
    }

    $mysqli->close();
} else {
    echo json_encode(['error' => 'Método no permitido']);
}
?>
