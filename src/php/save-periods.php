<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión
require_once 'cors.php';  // Si tienes configurado CORS
require_once 'conexion.php';

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['companyId']) || !isset($data['periodos'])) {
        echo json_encode(['error' => 'Parámetros faltantes']);
        exit;
    }

    $companyId = intval($data['companyId']);
    $periodos = $data['periodos'];

    foreach ($periodos as $periodo) {
        // Limpieza de datos para evitar inyección SQL
        $period_type_name = $mysqli->real_escape_string($periodo['nombretipoperiodo']);
        $period_days = intval($periodo['diasdelperiodo']);
        $payment_days = intval($periodo['diasdepago']);
        $work_period = intval($periodo['periodotrabajo']);
        $modify_history = intval($periodo['modificarhistoria']);
        $adjust_calendar_periods = intval($periodo['ajustarperiodoscalendario']);
        $rest_days_count = intval($periodo['numeroseptimos']);
        $rest_days_position = isset($periodo['posicionseptimos']) ? intval($periodo['posicionseptimos']) : 'NULL';
        $payroll_position = intval($periodo['posicionpagonomina']);
        $fiscal_year_start = $mysqli->real_escape_string($periodo['fechainicioejercicio']);
        $fiscal_year = intval($periodo['ejercicio']);
        $calendar_month_calc = intval($periodo['ccalculomescalendario']);
        $payment_frequency = $mysqli->real_escape_string($periodo['PeriodicidadPago']);

        $sql = "INSERT INTO period_types 
                (company_id, period_type_name, period_days, payment_days, work_period, modify_history, adjust_calendar_periods, rest_days_count, rest_days_position, payroll_position, fiscal_year_start, fiscal_year, calendar_month_calc, payment_frequency) 
                VALUES ($companyId, '$period_type_name', $period_days, $payment_days, $work_period, $modify_history, $adjust_calendar_periods, $rest_days_count, $rest_days_position, $payroll_position, '$fiscal_year_start', $fiscal_year, $calendar_month_calc, '$payment_frequency')";

        if ($mysqli->query($sql)) {
            // Obtener el ID del último registro insertado
            $lastInsertedId = $mysqli->insert_id;
            $response[] = [
                'period_type_name' => $period_type_name,
                'period_type_id' => $lastInsertedId,
                'message' => 'Periodo guardado correctamente'
            ];
        } else {
            echo json_encode(['error' => 'Error al insertar los datos: ' . $mysqli->error]);
            exit;
        }
    }

    $mysqli->close();

    // Responder con los datos de los periodos insertados
    echo json_encode(['success' => true, 'periods' => $response]);
} else {
    echo json_encode(['error' => 'Método no permitido']);
}
?>
