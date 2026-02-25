<?php
// 1. Cargar dependencias y conexión
require 'conn.php'; // Archivo de conexión a la base de datos (AJUSTA LA RUTA SI ES NECESARIO)
require 'excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// 2. Obtener y sanitizar parámetros de filtro
$vigencia = isset($_GET['vigencia']) && $_GET['vigencia'] !== 'Todos' ? $conn->real_escape_string($_GET['vigencia']) : null;
$trimestre = isset($_GET['trimestre']) && $_GET['trimestre'] !== 'Todos' ? $conn->real_escape_string($_GET['trimestre']) : null;
$tipo_comision = isset($_GET['tipo_comision']) && $_GET['tipo_comision'] !== 'Todos' ? $conn->real_escape_string($_GET['tipo_comision']) : null;

// 3. Lógica para determinar el rango de fechas por trimestre
$start_date = null;
$end_date = null;

if ($vigencia) {
    $year = (int)$vigencia;
    
    // Si hay trimestre, define el rango de meses
    if ($trimestre) {
        $month_map = [
            'I' => ['start' => 1, 'end' => 3],
            'II' => ['start' => 4, 'end' => 6],
            'III' => ['start' => 7, 'end' => 9],
            'IV' => ['start' => 10, 'end' => 12],
        ];

        if (isset($month_map[$trimestre])) {
            $start_month = $month_map[$trimestre]['start'];
            $end_month = $month_map[$trimestre]['end'];
            
            // Construir las fechas de inicio y fin del trimestre
            $start_date = date('Y-m-d', mktime(0, 0, 0, $start_month, 1, $year));
            $end_date = date('Y-m-d', mktime(0, 0, 0, $end_month + 1, 0, $year)); // Último día del mes
        }
    } else {
        // Si no hay trimestre, pero sí hay vigencia, se filtra por todo el año
        $start_date = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year));
        $end_date = date('Y-m-d', mktime(0, 0, 0, 13, 0, $year));
    }
}


// 4. Construir la consulta SQL
$sql = "SELECT 
            t.nombre_completo AS nombre_completo,
            ca.No_resolucion, 
            ca.fecha_resolucion,
            ca.viaticos,
            ca.tiquetes,
            ca.inscripcion,
            ca.evento,
            ca.organizado_por,
            ca.tipo_estudio,
            ca.modalidad,
            GROUP_CONCAT(dest.ciudad SEPARATOR ', ') AS ciudades_concat,
            GROUP_CONCAT(dest.pais SEPARATOR ', ') AS paises_concat,
            
            -- Reutilizamos la lógica de formato de fecha que ya tenías en comisionesb.php
            CASE 
                WHEN ca.fechaINI = ca.vence THEN 
                    CONCAT('el ', DAY(ca.fechaINI), ' de ', 
                        CASE MONTH(ca.fechaINI)
                            WHEN 1 THEN 'enero' WHEN 2 THEN 'febrero' WHEN 3 THEN 'marzo' WHEN 4 THEN 'abril' WHEN 5 THEN 'mayo' WHEN 6 THEN 'junio'
                            WHEN 7 THEN 'julio' WHEN 8 THEN 'agosto' WHEN 9 THEN 'septiembre' WHEN 10 THEN 'octubre' WHEN 11 THEN 'noviembre' WHEN 12 THEN 'diciembre'
                        END, ' de ', YEAR(ca.fechaINI)
                    )
                WHEN YEAR(ca.fechaINI) != YEAR(ca.vence) THEN 
                    CONCAT('del ', DAY(ca.fechaINI), ' de ', 
                        CASE MONTH(ca.fechaINI)
                            WHEN 1 THEN 'enero' WHEN 2 THEN 'febrero' WHEN 3 THEN 'marzo' WHEN 4 THEN 'abril' WHEN 5 THEN 'mayo' WHEN 6 THEN 'junio'
                            WHEN 7 THEN 'julio' WHEN 8 THEN 'agosto' WHEN 9 THEN 'septiembre' WHEN 10 THEN 'octubre' WHEN 11 THEN 'noviembre' WHEN 12 THEN 'diciembre'
                        END, ' de ', YEAR(ca.fechaINI), ' al ', 
                        DAY(ca.vence), ' de ', 
                        CASE MONTH(ca.vence)
                            WHEN 1 THEN 'enero' WHEN 2 THEN 'febrero' WHEN 3 THEN 'marzo' WHEN 4 THEN 'abril' WHEN 5 THEN 'mayo' WHEN 6 THEN 'junio'
                            WHEN 7 THEN 'julio' WHEN 8 THEN 'agosto' WHEN 9 THEN 'septiembre' WHEN 10 THEN 'octubre' WHEN 11 THEN 'noviembre' WHEN 12 THEN 'diciembre'
                        END, ' de ', YEAR(ca.vence)
                    )
                WHEN MONTH(ca.fechaINI) != MONTH(ca.vence) THEN 
                    CONCAT('del ', DAY(ca.fechaINI), ' de ', 
                        CASE MONTH(ca.fechaINI)
                            WHEN 1 THEN 'enero' WHEN 2 THEN 'febrero' WHEN 3 THEN 'marzo' WHEN 4 THEN 'abril' WHEN 5 THEN 'mayo' WHEN 6 THEN 'junio'
                            WHEN 7 THEN 'julio' WHEN 8 THEN 'agosto' WHEN 9 THEN 'septiembre' WHEN 10 THEN 'octubre' WHEN 11 THEN 'noviembre' WHEN 12 THEN 'diciembre'
                        END, ' al ', DAY(ca.vence), ' de ', 
                        CASE MONTH(ca.vence)
                            WHEN 1 THEN 'enero' WHEN 2 THEN 'febrero' WHEN 3 THEN 'marzo' WHEN 4 THEN 'abril' WHEN 5 THEN 'mayo' WHEN 6 THEN 'junio'
                            WHEN 7 THEN 'julio' WHEN 8 THEN 'agosto' WHEN 9 THEN 'septiembre' WHEN 10 THEN 'octubre' WHEN 11 THEN 'noviembre' WHEN 12 THEN 'diciembre'
                        END, ' de ', YEAR(ca.fechaINI)
                    )
                ELSE 
                    CONCAT('del ', DAY(ca.fechaINI), ' al ', DAY(ca.vence), ' de ', 
                        CASE MONTH(ca.fechaINI)
                            WHEN 1 THEN 'enero' WHEN 2 THEN 'febrero' WHEN 3 THEN 'marzo' WHEN 4 THEN 'abril' WHEN 5 THEN 'mayo' WHEN 6 THEN 'junio'
                            WHEN 7 THEN 'julio' WHEN 8 THEN 'agosto' WHEN 9 THEN 'septiembre' WHEN 10 THEN 'octubre' WHEN 11 THEN 'noviembre' WHEN 12 THEN 'diciembre'
                        END, ' de ', YEAR(ca.fechaINI)
                    )
            END AS fecha_formateada
        FROM 
            comision_academica ca
        LEFT JOIN 
            tercero t ON ca.documento = t.documento_tercero
        LEFT JOIN 
            destino dest ON ca.id = dest.id_comision
        WHERE 
            ca.estado != 'anulada'"; // Condición: No anulados

// Aplicar filtro de tipo INT/EXT
if ($tipo_comision) {
    $sql .= " AND ca.tipo_estudio = '$tipo_comision'";
}

// Aplicar filtro de Vigencia/Trimestre basado en fechaINI
if ($start_date && $end_date) {
    $sql .= " AND ca.fechaINI BETWEEN '$start_date' AND '$end_date'";
}

$sql .= " GROUP BY ca.id ORDER BY ca.fechaINI DESC;";

$result = $conn->query($sql);

// 5. Generar el archivo Excel (PhpSpreadsheet)
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados del Excel
$headers = [
    'Profesor',
    'No. Resolución', 
    'Fecha Resolución', 
    'Gastos Cubiertos', 
    'Detalle de la Comisión'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    // Establecer ancho de columna para "Detalle de la Comisión"
    if ($header === 'Detalle de la Comisión') {
        $sheet->getColumnDimension($col)->setWidth(100);
    } else {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    $col++;
}

// Llenar datos
$row = 2;
if ($result->num_rows > 0) {
    while ($data = $result->fetch_assoc()) {
        
        // --- 5.1. Campo Gastos Cubiertos ---
        $gastos = [];
        if ($data['viaticos'] == 1) $gastos[] = 'Viáticos';
        if ($data['tiquetes'] == 1) $gastos[] = 'Tiquetes';
        if ($data['inscripcion'] == 1) $gastos[] = 'Inscripción';
        $gastos_str = empty($gastos) ? 'Ninguno' : implode(', ', $gastos);

        // --- 5.2. Campo Detalle de la Comisión ---
        // Detalle: evento, organizado por X, en ciudad.. País fecha (modalidad)
        $detalle = sprintf(
            "%s, organizado por %s, en %s.. %s %s (%s) (%s)",
            $data['evento'],
            $data['organizado_por'],
            $data['ciudades_concat'],
            $data['paises_concat'],
            $data['fecha_formateada'], 
            $data['tipo_estudio'], // INT/EXT
            $data['modalidad'] // INT/EXT
        );
        
        // Escribir datos en el Excel
        $sheet->setCellValue('A' . $row, $data['nombre_completo']);
        $sheet->setCellValue('B' . $row, $data['No_resolucion']);
        $sheet->setCellValue('C' . $row, $data['fecha_resolucion']);
        $sheet->setCellValue('D' . $row, $gastos_str);
        $sheet->setCellValue('E' . $row, $detalle);
        
        // Ajustar celda del detalle para que envuelva el texto
        $sheet->getStyle('E' . $row)->getAlignment()->setWrapText(true);

        $row++;
    }
} else {
    $sheet->setCellValue('A2', 'No se encontraron registros de austeridad con los filtros seleccionados.');
}

// 6. Configurar descarga del archivo
$writer = new Xlsx($spreadsheet);
$filename = 'Reporte_Austeridad_' . date('Ymd_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>