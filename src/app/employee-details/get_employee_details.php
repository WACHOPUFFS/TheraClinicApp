<?php
header('Content-Type: application/json');

// Incluir el archivo de conexión y manejo de CORS
require_once 'cors.php';
require_once 'conexion.php';

// Verificar si se ha pasado el parámetro necesario (employee_id)
if (!isset($_GET['employee_id'])) {
    echo json_encode(['success' => false, 'error' => 'employee_id is required']);
    exit;
}

// Obtener el parámetro desde la URL
$employee_id = intval($_GET['employee_id']); // Asegurar que el parámetro es un número entero

// Construcción segura de la consulta
$sql = "
    SELECT 
        e.employee_id, 
        e.department_id, 
        e.position_id, 
        e.period_type_id, 
        e.shift_id, 
        e.employee_code, 
        e.first_name, 
        e.middle_name, 
        e.last_name, 
        CONCAT(e.first_name, ' ', e.middle_name, ' ', e.last_name) AS full_name, 
        e.photo, 
        e.birth_date, 
        e.birth_place, 
        e.marital_status_id, 
        e.gender_id, 
        e.curp, 
        e.curp_initials, 
        e.curp_final, 
        e.social_security_number, 
        e.unique_medical_unit_code, 
        e.rfc, 
        e.homoclave, 
        e.bank_account_number, 
        e.bank_branch, 
        e.bank_name, 
        e.employee_status, 
        e.daily_salary, 
        e.daily_salary_date, 
        e.variable_salary, 
        e.variable_salary_date, 
        e.average_salary, 
        e.average_salary_date, 
        e.integrated_salary, 
        e.integrated_salary_date, 
        e.calculated_salary, 
        e.affected_salary, 
        e.extraordinary_calculated_salary, 
        e.extraordinary_affected_salary, 
        e.cheq_paqw_interface, 
        e.salary_modification_net, 
        e.start_date, 
        e.cw_account, 
        e.contract_type, 
        e.social_security_base, 
        e.employee_type, 
        e.payment_base, 
        e.payment_method, 
        e.salary_zone, 
        e.ptu_calculation, 
        e.christmas_bonus_calculation, 
        e.salary_modification_social_security, 
        e.imss_registration, 
        e.imss_deregistration, 
        e.social_security_rate_change, 
        e.record_file, 
        e.phone_number, 
        e.postal_code, 
        e.address, 
        e.city, 
        e.state, 
        e.father_name, 
        e.mother_name, 
        e.afore_number, 
        e.termination_date, 
        e.termination_reason, 
        e.settlement_base_salary, 
        e.extra_field_1, 
        e.extra_field_2, 
        e.extra_field_3, 
        e.reinstatement_date, 
        e.adjustment_to_net, 
        e.extra_numeric_field_1, 
        e.extra_numeric_field_2, 
        e.extra_numeric_field_3, 
        e.extra_numeric_field_4, 
        e.extra_numeric_field_5, 
        e.employee_status_period, 
        e.mixed_salary_date, 
        e.mixed_salary, 
        e.fonacot_number, 
        e.email, 
        e.tax_regimen_type, 
        e.clabe, 
        e.federal_entity, 
        e.outsourcing, 
        e.foreigner_without_curp, 
        e.benefit_type, 
        e.vacation_days_taken_before_hiring, 
        e.vacation_bonus_days_taken_before_hiring, 
        e.reduced_week_type, 
        e.emergency_contact_name, 
        e.emergency_contact_number, 
        e.created_at, 
        e.updated_at, 
        e.company_id, 
        e.net_balance,
        e.lot_number_imss,
        e.folio_number_imss,
        d.department_name, 
        p.position_name, 
        s.shift_name,
        m.status_name,
        g.gender_name,
         er.request_id,
        er.status AS request_status,
        er.created_at AS request_created_at,
        er.updated_at AS request_updated_at

    FROM 
        employees e
    LEFT JOIN 
        departments d ON e.department_id = d.department_id
    LEFT JOIN 
        positions p ON e.position_id = p.position_id
    LEFT JOIN 
        shifts s ON e.shift_id = s.shift_id
    LEFT JOIN 
        marital_statuses m ON e.marital_status_id = m.status_id
    LEFT JOIN 
        genders g ON e.gender_id = g.gender_id
    LEFT JOIN
         employee_requests er ON e.employee_id = er.employee_id
    WHERE 
        e.employee_id = $employee_id
";

// Ejecutar la consulta
$result = $mysqli->query($sql);

if ($result && $result->num_rows > 0) {
    $employee = $result->fetch_assoc();

    // Reemplazar todos los campos vacíos o nulos con una cadena vacía
    $employee = array_map(function ($value) {
        return $value === null ? '' : $value;
    }, $employee);

    // Obtener archivos relacionados con el empleado desde la tabla employee_files
    $sql_files = "
        SELECT 
            ef.file_id,
            ef.file_type,
            ef.file_name,
            ef.file_path,
            ef.uploaded_at
        FROM 
            employee_files ef
        WHERE 
            ef.employee_id = $employee_id
    ";

    $result_files = $mysqli->query($sql_files);

    $files = [];
    while ($row_file = $result_files->fetch_assoc()) {
        $files[] = array_map(function ($value) {
            return $value === null ? '' : $value;
        }, $row_file);
    }

    // Devolver los detalles del empleado y los archivos en formato JSON
    echo json_encode(['success' => true, 'employee' => $employee, 'files' => $files]);

} else {
    // Si no se encuentra el empleado o hay un error
    echo json_encode(['success' => false, 'error' => 'Employee not found']);
}

// Cerrar la conexión
$mysqli->close();
?>