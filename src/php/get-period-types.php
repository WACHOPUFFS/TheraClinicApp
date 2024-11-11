<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión a la base de datos
require_once 'cors.php';
require_once 'conexion.php';

$response = [];

// Verificar si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Obtener el ID de la compañía desde el cuerpo de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['companyId'])) {
        echo json_encode(['error' => 'Parámetro companyId faltante']);
        exit;
    }

    $companyId = intval($data['companyId']);

    // Consulta para obtener los tipos de periodos y sus detalles
    $sql = "SELECT pt.period_type_id, pt.period_type_name, pp.month, pp.fiscal_year, pp.period_number, pp.start_date, pp.end_date, pp.payment_days
            FROM period_types pt
            LEFT JOIN payroll_periods pp ON pt.period_type_id = pp.period_type_id
            WHERE pt.company_id = $companyId
            ORDER BY pp.fiscal_year ASC, pp.period_number ASC";

    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $periodTypes = [];
        while ($row = $result->fetch_assoc()) {
            // Agrupar por tipo de periodo
            $periodTypeId = $row['period_type_id'];
            $periodTypeName = $row['period_type_name'];
            $fiscalYear = $row['fiscal_year'];

            // Si el tipo de periodo aún no existe en el arreglo, lo agregamos
            if (!isset($periodTypes[$periodTypeName])) {
                $periodTypes[$periodTypeName] = [
                    'period_type_id' => $periodTypeId,
                    'period_type_name' => $periodTypeName,
                    'years' => [],
                    'periods' => []
                ];
            }

            // Agregar el año si no está en la lista de años
            if (!in_array($fiscalYear, $periodTypes[$periodTypeName]['years'])) {
                $periodTypes[$periodTypeName]['years'][] = $fiscalYear;
            }

            // Agregar el periodo a la lista de periodos, incluyendo el mes
            $periodTypes[$periodTypeName]['periods'][] = [
                'year' => $fiscalYear,
                'month' => $row['month'],  // Incluir el mes
                'period_number' => $row['period_number'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'payment_days' => $row['payment_days']
            ];
        }

        $response['periodTypes'] = array_values($periodTypes);
    } else {
        $response['periodTypes'] = [];
    }

    $mysqli->close();
    echo json_encode($response);

} else {
    echo json_encode(['error' => 'Método no permitido']);
}
?>
