<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

$period_type_id = intval($data['period_type_id']);
$company_id = intval($data['company_id']);
$period_type_name = $mysqli->real_escape_string($data['period_type_name']);
$period_days = intval($data['period_days']);
$payment_days = intval($data['payment_days']);
$work_period = intval($data['work_period']);
$modify_history = intval($data['modify_history']);
$adjust_calendar_periods = intval($data['adjust_calendar_periods']);
$rest_days_position = intval($data['rest_days_position']);
$payroll_position = intval($data['payroll_position']);
$fiscal_year_start = $mysqli->real_escape_string($data['fiscal_year_start']);
$fiscal_year = intval($data['fiscal_year']);
$calendar_month_calc = intval($data['calendar_month_calc']);
$payment_frequency = $mysqli->real_escape_string($data['payment_frequency']);

$sql = "UPDATE period_types SET 
        period_type_name = '$period_type_name',
        period_days = $period_days,
        payment_days = $payment_days,
        work_period = $work_period,
        modify_history = $modify_history,
        adjust_calendar_periods = $adjust_calendar_periods,
        rest_days_position = $rest_days_position,
        payroll_position = $payroll_position,
        fiscal_year_start = '$fiscal_year_start',
        fiscal_year = $fiscal_year,
        calendar_month_calc = $calendar_month_calc,
        payment_frequency = '$payment_frequency'
        WHERE period_type_id = $period_type_id AND company_id = $company_id";

if ($mysqli->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Error actualizando el periodo: ' . $mysqli->error]);
}

$mysqli->close();
?>
