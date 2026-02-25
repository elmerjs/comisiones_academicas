<?php
// Incluir la librería PHPSpreadsheet
require 'conn.php';
require 'excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

// Establecer la conexión a la base de datos (debes llenar estos valores con tus datos)
/*$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nombre_basedatos";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);
*/
// Verificar la conexión

// Obtener los valores de los filtros del formulario
$reintegrado = $_GET['reintegrado'];
$estado = $_GET['estado'];
$vigencia = $_GET['vigencia'];
$tipo_comision = $_GET['tipo_comision'];

// Construir la parte de la consulta SQL según los filtros seleccionados
$whereClause = "";
if ($vigencia !== "Todos") {
    $whereClause .= " AND ca.vigencia = '$vigencia'";
}
if ($estado !== "Todos") {
    $whereClause .= " AND ca.estado = '$estado'";
}

if ($reintegrado !== "Todos") {
    
    if ($reintegrado == "1") {
    $whereClause .= " AND ca.reintegrado = '$reintegrado'";
    } else /*si es cero   VERIICAR LOS CEROS Y LOS VACIOS*/
    {
            $whereClause .= " AND (ca.reintegrado is null OR ca.reintegrado = '' or ca.reintegrado = '$reintegrado')";

    }
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Definir la consulta SQL
$sqle = "SELECT 
    ca.id AS id_comision,
    t.nombre_completo AS nombre_completo,
    ca.documento AS documento_profesor,
    CONCAT_WS('-', ca.vigencia, ca.periodo) AS periodo_academico,
    ca.tipo_estudio,
    CONCAT_WS('-', t.vincul, t.vinculacion) AS vinculacionr,
    f.NOMBREC_FAC AS nombre_fac_min,
    d.depto_nom_propio AS depto_nom_propio,
    ca.fecha_aval,
    ca.evento,
    ca.organizado_por,
    GROUP_CONCAT(dest.ciudad SEPARATOR ', ') AS ciudades_concat,
    GROUP_CONCAT(dest.pais SEPARATOR ', ') AS paises_concat,
    ca.tipo_participacion,
    ca.nombre_trabajo,
    CASE 
        WHEN ca.fechaINI = ca.vence THEN 
            CONCAT('el ', DAY(ca.fechaINI), ' de ', 
                CASE MONTH(ca.fechaINI)
                    WHEN 1 THEN 'enero'
                    WHEN 2 THEN 'febrero'
                    WHEN 3 THEN 'marzo'
                    WHEN 4 THEN 'abril'
                    WHEN 5 THEN 'mayo'
                    WHEN 6 THEN 'junio'
                    WHEN 7 THEN 'julio'
                    WHEN 8 THEN 'agosto'
                    WHEN 9 THEN 'septiembre'
                    WHEN 10 THEN 'octubre'
                    WHEN 11 THEN 'noviembre'
                    WHEN 12 THEN 'diciembre'
                END, 
                ' de ', YEAR(ca.fechaINI)
            )
        WHEN YEAR(ca.fechaINI) != YEAR(ca.vence) THEN 
            CONCAT('del ', DAY(ca.fechaINI), ' de ', 
                CASE MONTH(ca.fechaINI)
                    WHEN 1 THEN 'enero'
                    WHEN 2 THEN 'febrero'
                    WHEN 3 THEN 'marzo'
                    WHEN 4 THEN 'abril'
                    WHEN 5 THEN 'mayo'
                    WHEN 6 THEN 'junio'
                    WHEN 7 THEN 'julio'
                    WHEN 8 THEN 'agosto'
                    WHEN 9 THEN 'septiembre'
                    WHEN 10 THEN 'octubre'
                    WHEN 11 THEN 'noviembre'
                    WHEN 12 THEN 'diciembre'
                END, 
                ' de ', YEAR(ca.fechaINI), ' al ', 
                DAY(ca.vence), ' de ', 
                CASE MONTH(ca.vence)
                    WHEN 1 THEN 'enero'
                    WHEN 2 THEN 'febrero'
                    WHEN 3 THEN 'marzo'
                    WHEN 4 THEN 'abril'
                    WHEN 5 THEN 'mayo'
                    WHEN 6 THEN 'junio'
                    WHEN 7 THEN 'julio'
                    WHEN 8 THEN 'agosto'
                    WHEN 9 THEN 'septiembre'
                    WHEN 10 THEN 'octubre'
                    WHEN 11 THEN 'noviembre'
                    WHEN 12 THEN 'diciembre'
                END, 
                ' de ', YEAR(ca.vence)
            )
        WHEN MONTH(ca.fechaINI) != MONTH(ca.vence) THEN 
            CONCAT('del ', DAY(ca.fechaINI), ' de ', 
                CASE MONTH(ca.fechaINI)
                    WHEN 1 THEN 'enero'
                    WHEN 2 THEN 'febrero'
                    WHEN 3 THEN 'marzo'
                    WHEN 4 THEN 'abril'
                    WHEN 5 THEN 'mayo'
                    WHEN 6 THEN 'junio'
                    WHEN 7 THEN 'julio'
                    WHEN 8 THEN 'agosto'
                    WHEN 9 THEN 'septiembre'
                    WHEN 10 THEN 'octubre'
                    WHEN 11 THEN 'noviembre'
                    WHEN 12 THEN 'diciembre'
                END, 
                ' al ', DAY(ca.vence), ' de ', 
                CASE MONTH(ca.vence)
                    WHEN 1 THEN 'enero'
                    WHEN 2 THEN 'febrero'
                    WHEN 3 THEN 'marzo'
                    WHEN 4 THEN 'abril'
                    WHEN 5 THEN 'mayo'
                    WHEN 6 THEN 'junio'
                    WHEN 7 THEN 'julio'
                    WHEN 8 THEN 'agosto'
                    WHEN 9 THEN 'septiembre'
                    WHEN 10 THEN 'octubre'
                    WHEN 11 THEN 'noviembre'
                    WHEN 12 THEN 'diciembre'
                END, 
                ' de ', YEAR(ca.fechaINI)
            )
        ELSE 
            CONCAT('del ', DAY(ca.fechaINI), ' al ', DAY(ca.vence), ' de ', 
                CASE MONTH(ca.fechaINI)
                    WHEN 1 THEN 'enero'
                    WHEN 2 THEN 'febrero'
                    WHEN 3 THEN 'marzo'
                    WHEN 4 THEN 'abril'
                    WHEN 5 THEN 'mayo'
                    WHEN 6 THEN 'junio'
                    WHEN 7 THEN 'julio'
                    WHEN 8 THEN 'agosto'
                    WHEN 9 THEN 'septiembre'
                    WHEN 10 THEN 'octubre'
                    WHEN 11 THEN 'noviembre'
                    WHEN 12 THEN 'diciembre'
                END, 
                ' de ', YEAR(ca.fechaINI)
            )
    END AS fecha_formateada,
    ca.duracion_horas,
    f.email_fac,
    t.email AS email_tercero,
    ca.No_resolucion,
    CASE 
        WHEN ca.reintegrado = '1' THEN 'ENTREGADO'
        ELSE 'PENDIENTE'
    END AS reintegrado,
    ca.fecha_informe,
    ca.folios,
    ca.observacion,
    ca.tramito,
    ca.estado,
    ca.fechaINI,
    ca.vence,
    ca.fecha_resolucion,
    ca.viaticos,
    ca.tiquetes,
    ca.inscripcion
FROM 
    comision_academica ca
LEFT JOIN 
    tercero t ON ca.documento = t.documento_tercero
LEFT JOIN 
    deparmanentos d ON t.fk_depto = d.PK_DEPTO
LEFT JOIN 
    facultad f ON d.FK_FAC = f.PK_FAC
LEFT JOIN 
    destino dest ON ca.id = dest.id_comision
WHERE 
    ca.id NOT IN ('0') $whereClause
GROUP BY 
    ca.id";


// Ejecutar la consulta SQL
$result = $conn->query($sqle);

// Verificar si hay resultados
if ($result->num_rows > 0) {
    // Crear un nuevo objeto Spreadsheet
    $spreadsheet = new Spreadsheet();

    // Obtener la hoja activa
    $sheet = $spreadsheet->getActiveSheet();

    // Definir los estilos mejorados
    $headerStyle = [
        'font' => [
            'bold' => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
    ];
    $boldFontStyle = [
        'font' => [
            'bold' => true,
        ],
    ];

   $sheet->getStyle('A1:AF1')->applyFromArray($headerStyle);

// Definir los encabezados de las columnas
$sheet->setCellValue('A1', 'ID Comisión');
$sheet->setCellValue('B1', 'Nombre Completo');
$sheet->setCellValue('C1', 'Documento del Profesor');
$sheet->setCellValue('D1', 'Periodo Académico');
$sheet->setCellValue('E1', 'Tipo de Estudio');
$sheet->setCellValue('F1', 'Vinculación');
$sheet->setCellValue('G1', 'Nombre Facultad');
$sheet->setCellValue('H1', 'Departamento');
$sheet->setCellValue('I1', 'Fecha de Aval');
$sheet->setCellValue('J1', 'Evento');
$sheet->setCellValue('K1', 'Organizado Por');
$sheet->setCellValue('L1', 'Ciudades');
$sheet->setCellValue('M1', 'Países');
$sheet->setCellValue('N1', 'Tipo de Participación');
$sheet->setCellValue('O1', 'Nombre del Trabajo');
$sheet->setCellValue('P1', 'Fecha');
$sheet->setCellValue('Q1', 'Duración (Horas)');
$sheet->setCellValue('R1', 'Email Facultad');
$sheet->setCellValue('S1', 'Email Tercero');
$sheet->setCellValue('T1', 'No. Resolución');
$sheet->setCellValue('U1', 'Informe');
$sheet->setCellValue('V1', 'Fecha Informe');
$sheet->setCellValue('W1', 'Folios');
$sheet->setCellValue('X1', 'Observación');
$sheet->setCellValue('Y1', 'Tramitó');
$sheet->setCellValue('Z1', 'Estado');
$sheet->setCellValue('AA1', 'Inicio');
$sheet->setCellValue('AB1', 'Fin');
$sheet->setCellValue('AC1', 'Fecha_resolucion');
$sheet->setCellValue('AD1', 'Viáticos');
$sheet->setCellValue('AE1', 'Tiquetes');
$sheet->setCellValue('AF1', 'Inscripción');

    
    


    // Establecer el ancho de las columnas
   $columnWidths = [
    'A' => 20,  // ID Comisión
    'B' => 30,  // Nombre Completo
    'C' => 15,  // Documento del Profesor
    'D' => 20,  // Periodo Académico
    'E' => 15,  // Tipo de Estudio
    'F' => 15,  // Vinculación
    'G' => 20,  // Nombre Facultad
    'H' => 20,  // Departamento
    'I' => 20,  // Fecha de Aval
    'J' => 30,  // Evento
    'K' => 30,  // Organizado Por
    'L' => 20,  // Ciudades
    'M' => 20,  // Países
    'N' => 20,  // Tipo de Participación
    'O' => 25,  // Nombre del Trabajo
    'P' => 20,  // Fecha
    'Q' => 20,  // Duración (Horas)
    'R' => 30,  // Email Facultad
    'S' => 30,  // Email Tercero
    'T' => 25,  // No. Resolución
    'U' => 20,  // Reintegrado
    'V' => 20,  // Fecha Informe
    'W' => 15,  // Folios
    'X' => 25,  // Observación
    'Y' => 20,  // Tramitó
    'Z' => 5,  // ESt
    'AA' => 10,  // ESt
    'AB' => 10,  // ESt
    'AC' => 15,  // ESt
    'AD' => 5,  // ESt
    'AE' => 5,  // ESt
    'AF' => 5,  // ESt
];
    foreach ($columnWidths as $column => $width) {
        $sheet->getColumnDimension($column)->setWidth($width);
    }

    // Recorrer los resultados y escribirlos en el archivo Excel
    $row = 2; // Empezamos en la fila 2 para dejar espacio para los encabezados
    while ($row_data = $result->fetch_assoc()) {
        $sheet->fromArray($row_data, NULL, 'A' . $row);

      if ($row_data['estado'] == 'anulada') {
        $sheet->getStyle('Z' . $row . ':Z' . $row)->applyFromArray([
            'font' => [
                'color' => ['rgb' => 'FF0000'], // Color rojo en formato RGB
            ],
        ]);
    }
    
        
    // Verificar si el estado es "FINALIZADA" y el campo "reintegrado" es NULL, vacío o cero, y aplicar fondo amarillo
    if ($row_data['estado'] == 'finalizada' && ($row_data['reintegrado'] == 'PENDIENTE')) {
        $sheet->getStyle('Z' . $row . ':Z' . $row)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'], // Color amarillo en formato RGB
            ],
        ]);
    }

    
        
        
    // Ajustar el texto para que se ajuste automáticamente
    $sheet->getStyle('P' . $row . ':V' . $row)->getAlignment()->setWrapText(true);

        // Incrementar el número de fila
        $row++;
    }
    
    // Aplicar formato de fecha a las columnas 'AA' y 'AB'
$sheet->getStyle('AA2:AA' . ($row - 1))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
$sheet->getStyle('AB2:AB' . ($row - 1))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
    
// Obtener la última fila en el archivo Excel
$lastRow = $sheet->getHighestRow();

// Agregar celdas al final del archivo Excel
$sheet->setCellValue('A' . ($lastRow + 2), 'Finalizados pero no reincorporados');/*
$sheet->setCellValue('A' . ($lastRow + 3), 'Anuladas');
$sheet->setCellValue('A' . ($lastRow + 4), 'Activos');
$sheet->setCellValue('A' . ($lastRow + 5), 'Finalizados');
*/
// Aplicar estilos a las nuevas celdas
$sheet->getStyle('A' . ($lastRow + 2))->applyFromArray([
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'FFFFE0'], // Color amarillo en formato RGB
    ],
]);
/*
$sheet->getStyle('A' . ($lastRow + 3))->applyFromArray([
    'font' => [
        'color' => ['rgb' => 'FF0000'], // Color rojo en formato RGB
    ],
]);

    
$sheet->getStyle('A' . ($lastRow + 4))->applyFromArray([
    'font' => [
        'color' => ['rgb' => '0E5210'], // Color verd en formato RGB
    ],
]);
    
    
$sheet->getStyle('A' . ($lastRow + 5))->applyFromArray([
    'font' => [
        'color' => ['rgb' => '0000FF'], // Color az en formato RGB
    ],
]);*/
    // Guardar el archivo Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save('comisiones_academicas.xlsx');

    // Descargar el archivo Excel generado
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="comisiones_academicas.xlsx"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
} else {
    echo "No se encontraron resultados.";
}

// Cerrar la conexión a la base de datos
$conn->close();
?>