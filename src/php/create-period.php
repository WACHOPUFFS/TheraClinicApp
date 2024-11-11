<?php
header('Content-Type: application/json');

include_once 'cors.php';
include_once 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

$company_id = intval($data['company_id']);
$period_type_name = $mysqli->real_escape_string($data['period_type_name']);
$period_days = intval($data['period_days']);
$payment_days = intval($data['payment_days']);
$work_period = intval($data['work_period']);
$adjust_calendar_periods = intval($data['adjust_calendar_periods']);
$rest_days_position = intval($data['rest_days_position']);
$payroll_position = intval($data['payroll_position']);
$fiscal_year_start = $mysqli->real_escape_string($data['fiscal_year_start']);
$payment_frequency = $mysqli->real_escape_string($data['payment_frequency']);

// Verificar que los campos obligatorios no estén vacíos
if (empty($company_id) || empty($period_type_name) || empty($period_days) || empty($payment_days) || empty($fiscal_year_start) || empty($payment_frequency)) {
    echo json_encode(['error' => 'Faltan campos obligatorios']);
    exit;
}

// Construir la consulta SQL
$sql = "INSERT INTO period_types (company_id, period_type_name, period_days, payment_days, work_period, adjust_calendar_periods, rest_days_position, payroll_position, fiscal_year_start, payment_frequency)
        VALUES ($company_id, '$period_type_name', $period_days, $payment_days, $work_period, $adjust_calendar_periods, $rest_days_position, $payroll_position, '$fiscal_year_start', '$payment_frequency')";

// Registrar la consulta SQL para debugging
error_log("SQL: " . $sql);

// Ejecutar la consulta
if ($mysqli->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'period_type_id' => $mysqli->insert_id]);
} else {
    echo json_encode(['error' => 'Error creando el periodo: ' . $mysqli->error]);
}

$mysqli->close();
?>
