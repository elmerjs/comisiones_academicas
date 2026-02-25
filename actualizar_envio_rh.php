<?php
require 'conn.php';
require('fpdf186/fpdf.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['seleccionados'])) {
    $seleccionados = $_POST['seleccionados'];
    
    if (!empty($seleccionados)) {
        // Actualizar los registros seleccionados
        foreach ($seleccionados as $id) {
            $sql = "UPDATE comision_academica SET envio_rh = 1 WHERE id = '$id'";
            mysqli_query($conn, $sql);
        }
        
        // Obtener los datos de los registros seleccionados para el PDF
        $ids = implode(",", array_map('intval', $seleccionados));
        $sql = "SELECT 
                    comision_academica.documento, 
                    tercero.nombre_completo, 
                    comision_academica.No_resolucion, 
                CONCAT(REPLACE(comision_academica.No_resolucion, 'RESOL ', ''), ' - ', DATE_FORMAT(comision_academica.fecha_resolucion, '%Y-%m-%d')) AS resolucion_fecha,

                    comision_academica.folios, 
                    comision_academica.envio_rh 
                FROM 
                    comision_academica 
                JOIN 
                    tercero 
                ON 
                    comision_academica.documento = tercero.documento_tercero 
                WHERE 
                    comision_academica.id IN ($ids)";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            // Crear el PDF
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetLeftMargin(30); // Margen izquierdo de 30 mm (3 cm)
            $pdf->SetRightMargin(30); // Margen derecho de 30 mm (3 cm)
            $pdf->SetFont('Arial', '', 9);
            
            // Títulos de las columnas
            $pdf->Cell(20, 10, 'Documento', 1);
            $pdf->Cell(80, 10, 'Nombre Completo', 1);
            $pdf->Cell(40, 10, 'No. Resolucion', 1);
            $pdf->Cell(10, 10, 'Folios', 1);
            $pdf->Cell(10, 10, 'RRHH', 1);
            $pdf->Ln();
            
            // Datos
            while ($row = mysqli_fetch_assoc($result)) {
                $pdf->Cell(20, 10, $row['documento'], 1);
                $pdf->Cell(80, 10, utf8_decode($row['nombre_completo']), 1);
                $no_resolucion = $row['resolucion_fecha'];
                $pdf->Cell(40, 10, $no_resolucion, 1);
                $pdf->Cell(10, 10, $row['folios'], 1);
                $pdf->Cell(10, 10, $row['envio_rh'], 1);
                $pdf->Ln();
            }
            
            // Guardar el PDF en el servidor
            $pdfFilePath = 'reportes/reporte_envio_rh.pdf';
            $pdf->Output('F', $pdfFilePath);
            
            // Redireccionar con enlace al PDF
            header("Location: report_pendientes.php?pdf=$pdfFilePath");
            exit();
        }
    }
}

// Si no hay selección o no hay registros, redirigir de vuelta sin PDF
header('Location: report_pendientes.php');
exit();
?>
