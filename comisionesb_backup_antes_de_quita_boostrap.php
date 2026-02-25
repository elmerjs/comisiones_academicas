<?php
// Incluir el archivo de conexión a la base de datos
require 'conn.php';
require('include/headerz.php');
$anio = isset($_GET['anio']) ? $_GET['anio'] : 0;


    
    $sqlu = "
    UPDATE
comision_academica
SET  comision_academica.estado = 'finalizada'
where comision_academica.estado = 'Activa'
AND comision_academica.vence  < NOW()";
        
if ($conn->query($sqlu) === TRUE) {
  echo "actualizado";
} else {
  echo "Error: " . $sqlu . "<br>" . $conn->error;
}



     if ($anio != 0) {
// Consultar los datos de la base de datos
$sql = "SELECT 
            ca.id as id_comision,
            ca.No_resolucion, 
            ca.fecha_resolucion,
            ca.documento as documento_profesor,
            ca.tipo_estudio,
            ca.fecha_aval,
            ca.duracion_horas,
            ca.fechasol,
            ca.organizado_por,
            ca.tipo_participacion,
            ca.evento,
            ca.nombre_trabajo,
            ca.estado as estado_comision,
            ca.observacion,
            ca.fechaINI, 
            ca.vence,
            ca.vigencia,
            ca.periodo,
            CONCAT_WS('-', ca.vigencia, ca.periodo) AS periodo_academico,
            ca.reintegrado,
            ca.fecha_informe,
            ca.folios,
            ca.tramito,
            ca.id_rector,
            ca.id_vice,
            ca.reviso,
            ca.justificacion,
            ca.viaticos,
            ca.tiquetes,
            ca.inscripcion,
            ca.cargo_a,
            ca.valor,
            ca.cdp,
            t.apellido1, t.nombre1,t.nombre2,t.apellido2,
            t.nombre_completo AS nombre_completo,
            t.email AS email_tercero,
            CONCAT_WS('-', t.vincul, t.vinculacion) AS vinculacionr,
            t.vincul AS vinculacion,
            t.vinculacion AS dedicacion,
            d.depto_nom_propio AS depto_nom_propio,
            f.NOMBREC_FAC AS nombre_fac_min,
            f.email_fac, 
            GROUP_CONCAT(dest.ciudad SEPARATOR ', ') AS ciudades_concat,
            GROUP_CONCAT(dest.pais SEPARATOR ', ') AS paises_concat,
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
              ca.link_resolucion
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
            where  ca.vigencia = $anio
        GROUP BY 
            ca.id  
        ORDER BY 
            id DESC;";
         
       } else {  
$sql = "SELECT 
            ca.id as id_comision,
            ca.No_resolucion, 
            ca.fecha_resolucion,
            ca.documento as documento_profesor,
            ca.tipo_estudio,
            ca.fecha_aval,
            ca.duracion_horas,
            ca.fechasol,
            ca.organizado_por,
            ca.tipo_participacion,
            ca.evento,
            ca.nombre_trabajo,
            ca.estado as estado_comision,
            ca.observacion,
            ca.fechaINI, 
            ca.vence,
            ca.vigencia,
            ca.periodo,
            CONCAT_WS('-', ca.vigencia, ca.periodo) AS periodo_academico,
            ca.reintegrado,
            ca.fecha_informe,
            ca.folios,
            ca.tramito,
            ca.id_rector,
            ca.id_vice,
            ca.reviso,
            ca.justificacion,
            ca.viaticos,
            ca.tiquetes,
            ca.inscripcion,
            ca.cargo_a,
            ca.valor,
            ca.cdp,
            t.apellido1, t.nombre1,t.nombre2,t.apellido2,
            t.nombre_completo AS nombre_completo,
            t.email AS email_tercero,
            CONCAT_WS('-', t.vincul, t.vinculacion) AS vinculacionr,
            t.vincul AS vinculacion,
            t.vinculacion AS dedicacion,
            d.depto_nom_propio AS depto_nom_propio,
            f.NOMBREC_FAC AS nombre_fac_min,
            f.email_fac, 
            GROUP_CONCAT(dest.ciudad SEPARATOR ', ') AS ciudades_concat,
            GROUP_CONCAT(dest.pais SEPARATOR ', ') AS paises_concat,
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
              ca.link_resolucion
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
        GROUP BY 
            ca.id  
        ORDER BY 
            id DESC;";
         
       }
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comisiones Académicas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    /* Cambiar el color de los iconos */
   
    /* Estilo para el botón de informe */
    .informe-btn {
        background-color: transparent !important; /* Cambia este valor al color que desees */
        border: 0px solid #111; /* Cambia este valor al color del contorno que desees */
        border-radius: 5px; /* Agrega esquinas redondeadas al contorno si lo deseas */
        padding: 5px 10px; /* Ajusta el relleno según sea necesario */
        color: #111; /* Cambia este valor al color del texto que desees */
        text-decoration: none; /* Elimina cualquier subrayado del texto */
        cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
    
    
    } th {
        background-color: #060264; /* Fondo azul oscuro */
        color: white; /* Texto blanco */
        text-align: center; /* Centrar el texto */
        margin: 0 5px; /* Espacio entre columnas */
        padding: 10px 15px; /* Espacio interno */
      /*  display: inline-block; /* Hacer que el espacio entre ellos funcione */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Sombra ligera */
    }
    td {
    background-color: #FCFCFC; /* Fondo gris muy tenue */
    padding: 10px 15px; /* Espacio interno */
            border: 1px solid #f5f5f5; /* Línea blanca entre campos */

}   
    
    .icono-doc {
    color: #2B579A; /* Color azul similar al de Word */
}
     .icono-informe {
            color: darkred; /* Color naranja alusivo para informe */
        }
    .icono-editar {
            color: #4CAF50; /* Color verde alusivo para editar */
        }
      .anular-btn {
        padding: 2px 4px; /* Ajusta el padding para hacerlo más pequeño */
        font-size: 0.75rem; /* Reduce el tamaño de la fuente */
        line-height: 1.2; /* Ajusta la altura de la línea */
        border-radius: 0.2rem; /* Ajusta el radio de las esquinas si es necesario */
    }
</style>

</head>
<body>
<div id ="contenido">
    <br>
 <?php if ($anio === 0): ?>
        <h2>Comisiones Académicas</h2>
    <?php else: ?>
        <h2>Comisiones Académicas <?php echo $anio; ?></h2>
    <?php endif; ?><table id="comisionesTable" class="table table-hover">
        
        <thead>
        <tr>
                <th style="width: 30px;">Id</th>
            <th style="width: 290px;font-size: 14px;">Profesor</th>
            <th style="width: 40px;font-size: 14px;">Periodo</th>
            <th style="width: 30px;font-size: 14px;">INT/EXT</th>
            <th style="width: 200px;font-size: 14px;">Depto</th>
  <!-- <th style="width: 200px;font-size: 14px;">Evento</th> -->         
            <th style="width: 100px;font-size: 14px;">Destino</th>
           <!--  <th style="width: 200px;font-size: 14px;">Fechas</th> --> 
            <th style="width: 60px;font-size: 14px;">#Res</th>
            <!-- <th style="width: 20px;font-size: 14px;" class="text-center">Inform</th> -->
            <th style="width: 50px;font-size: 14px;">Tramitó</th>
                      <th style="width: 30px;font-size: 14px;">Est</th>

<th style="width: 80px;" class="text-center">Edit</th>
            <th style="width: 80px;" class="text-center">Resol Indivd</th>
                <th style="width: 80px;" class="text-center">Resol multi</th>

<th style="width: 80px;" class="text-center">Subir informe</th>
           <th style="width: 80px;" class="text-center">Anular</th>
           <th style="width: 80px;" class="text-center">Clonar</th>

        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                
                  $observacionx = $row["link_resolucion"];

    // Extraer el enlace desde observacion
    $link = '';
   if (!empty($observacionx)) {  // Verifica si $observacion no está vacía
    $link = $observacionx;  // Asigna el valor de $observacion a $link
}

    // Crear el enlace HTML si se encuentra un enlace válido
    if (!empty($link)) {
        echo "<td style='font-size: 14px;'><a href='$link' target='_blank'>" . $row["id_comision"] . "</a></td>";
    } else {
        echo "<td style='font-size: 14px;'>" . $row["id_comision"] . "</td>";
    }
                
                
             //   echo "<td style='font-size: 14px;'>". $row["id_comision"] . "</td>";
echo "<td style='font-size: 14px;'>" . $row["documento_profesor"] . " - " . substr($row["apellido1"] . " " . substr($row["apellido1"], 0, 1) . ". " . $row["nombre1"]. " " . $row["nombre2"], 0, 20) . "</td>";

             echo "<td style='font-size: 14px;'>" . $row["periodo_academico"] . "</td>";
              echo "<td style='font-size: 14px;'>". $row["tipo_estudio"] . "</td>";
              echo "<td style='font-size: 14px;'>" . substr($row["depto_nom_propio"], 0, 15) . " - " . $row["nombre_fac_min"] . "</td>";

               // echo "<td style='font-size: 14px;'>" . substr($row["evento"], 0, 25) . "</td>";
            //    echo "<td style='font-size: 14px;'>" . substr($row["ciudades_concat"], 0, 10) . "</td>";
             
                echo "<td style='font-size: 14px;' title='" . htmlspecialchars($row["evento"] . " - " . $row["fecha_formateada"], ENT_QUOTES) . "'>"
    . substr($row["ciudades_concat"], 0, 10) . 
    "</td>";
                //  echo "<td style='font-size: 14px;'>". substr($row["fecha_formateada"], 0, 20) . "</td>";
                //echo "<td style='font-size: 14px;' title='" . htmlspecialchars($row["evento"], ENT_QUOTES) . "'>"    . substr($row["fecha_formateada"], 0, 20) .     "</td>";
if (!empty($link)) {
    echo "<td style='font-size: 14px;'><a href='$link' target='_blank'>" . substr($row["No_resolucion"], 0, 20) . "</a></td>";
} else {
    echo "<td style='font-size: 14px;'>" . substr($row["No_resolucion"], 0, 20) . "</td>";
}                
        //echo "<td style='font-size: 14px;'class='text-center'>". ($row["reintegrado"] == 1 ? '✓' : '✗') . "</td>";
                echo "<td style='font-size: 14px;'>" . substr($row["tramito"], 0, 6) . "</td>";
// Definir las abreviaciones para cada estado
$estado_abreviado = '';
switch ($row["estado_comision"]) {
    case 'finalizada':
        $estado_abreviado = 'fn';
        break;
    case 'Activa':
        $estado_abreviado = 'ac';
        break;
    case 'anulada':
        $estado_abreviado = 'an';
        break;
    default:
        $estado_abreviado = ''; // Vacío para cualquier otro estado
        break;
}

// Mostrar el estado abreviado con el estilo apropiado
echo "<td style='font-size: 14px; color:" . ($row["estado_comision"] == "anulada" ? "red" : "black") . ";'>" . $estado_abreviado . "</td>";

$tipo_estudio = $row['tipo_estudio'];
$id_comision = $row['id_comision'];
   // Icono de editar
$link_editar = "actualizar_formacion.php?id={$id_comision}";
$icon_editar = '<i class="fas fa-pencil-alt icono-editar"></i>'; // Agregamos la clase "icono-editar" al icono
echo "<td class='text-center'><a href='$link_editar'>$icon_editar</a></td>";
     
// Icono de documento

$link = $tipo_estudio == 'EXT' ? "resolucion_doc_ext.php?id={$id_comision}" : "resolucion_docb.php?id={$id_comision}";
 $linkb = $tipo_estudio == 'EXT' ? "resolucion_doc_ext_b.php?id={$id_comision}" : "resolucion_docc.php?id={$id_comision}";

$icon_doc = '<i class="far fa-file-word icono-doc"></i>'; // Agregamos la clase "icono-doc" al icono
echo "<td class='text-center'> <a href='$linkb'>$icon_doc</a></td>";
//nuevo td dow conjunto
                $no_resolucion = $row["No_resolucion"];
$fecha_resolucion = $row["fecha_resolucion"];

// Enlace con parámetros
$linkb = "resolucion_docc_grupal_t.php?no_resolucion=$no_resolucion&fecha_resolucion=$fecha_resolucion";
$link = "resolucion_docc_grupal.php?no_resolucion=$no_resolucion&fecha_resolucion=$fecha_resolucion";

// Ícono de documento agrupado
$icon_doc = '<i class="fas fa-layer-group icono-doc"></i>'; // Utilizamos el ícono "fa-layer-group" para representar agrupación

// Generar la celda
echo "<td class='text-center'>
        <a href='$link' title='en párrafo'>$icon_doc</a>
        <a href='$linkb' title='en tabla'>$icon_doc</a>
      </td>";//termina doc concjunto

// Icono de informe con clase
$icon_informe = '<i class="fas fa-file-signature icono-informe"></i>'; // Agregamos la clase "icono-informe" al icono
echo "<td class='text-center'><button type='button' class='btn btn-primary informe-btn' data-toggle='modal' data-target='#informeModal' data-id='$id_comision'>$icon_informe</button></td>";
echo "<td class='text-center'>";
echo "<button type='button' class='btn btn-danger btn-sm anular-btn' onclick=\"confirmarAnulacion({$id_comision});\" title='Anular y descargar'>";
echo "<i class='fas fa-ban'></i>"; // Ícono pequeño
echo "</button>";
echo "</td>";

echo "<script>
function confirmarAnulacion(comisionId) {
    // Pedir el medio de comunicación con un texto de ejemplo
    var medio_comunicacion = prompt('Indique el medio de comunicación (ej. Oficio 3.5.5-4 del 3 de agosto de 2024):');
    
    // Si se cancela el prompt o se deja vacío, cancelar la operación
    if (!medio_comunicacion) {
        alert('Debe proporcionar el medio de comunicación.');
        return;
    }
    
    // Pedir la razón con un texto de ejemplo
    var razon = prompt('Indique el motivo de anulación (ej. Problemas logísticos con la entidad...):');
    
    // Si se cancela el prompt o se deja vacío, cancelar la operación
    if (!razon) {
        alert('Debe proporcionar una razón.');
        return;
    }
    
    // Confirmar la acción de anulación
    if (confirm('¿Está seguro que desea anular el registro?')) {
        // Mostrar un mensaje de carga
        document.body.insertAdjacentHTML('beforeend', '<div id=\"loading\" style=\"position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); padding:20px; background:white; border:1px solid #ccc; border-radius:5px; box-shadow:0 0 10px rgba(0,0,0,0.2);\">Procesando, por favor espere...</div>');

        // Crear un iframe oculto para realizar la solicitud y descargar el archivo
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = 'anular_registro.php?comision_id=' + comisionId + 
                      '&medio_comunicacion=' + encodeURIComponent(medio_comunicacion) +
                      '&razon=' + encodeURIComponent(razon);
        document.body.appendChild(iframe);

        // Esperar un momento para que la descarga se complete
        setTimeout(function() {
            // Ocultar el mensaje de carga
            document.getElementById('loading').remove();
            // Recargar la página principal
            location.reload();
        }, 1000); // Esperar 3 segundos
    }
}
</script>";
                
                echo "<td class='text-center'>";
echo "<button type='button' class='btn btn-success btn-sm clonar-btn' onclick=\"confirmarClonacion({$id_comision});\" title='Clonar registro'>";
echo "<i class='fas fa-clone'></i>"; // Ícono pequeño de clonar
echo "</button>";
echo "</td>";
                echo "<script>
function confirmarClonacion(comisionId) {
    // Pedir la cédula del tercero con un texto de ejemplo
    var cedula_tercero = prompt('Indique la cédula del tercero:');
    
    // Si se cancela el prompt o se deja vacío, cancelar la operación
    if (!cedula_tercero) {
        alert('Debe proporcionar la cédula del tercero.');
        return;
    }

    // Confirmar la acción de clonación
    if (confirm('¿Está seguro que desea clonar el registro con la cédula proporcionada?')) {
        // Mostrar un mensaje de carga
        document.body.insertAdjacentHTML('beforeend', '<div id=\"loading\" style=\"position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); padding:20px; background:white; border:1px solid #ccc; border-radius:5px; box-shadow:0 0 10px rgba(0,0,0,0.2);\">Procesando, por favor espere...</div>');
        
        // Crear un iframe oculto para realizar la solicitud
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = 'clonar_registro.php?comision_id=' + comisionId + 
                      '&cedula_tercero=' + encodeURIComponent(cedula_tercero);
        document.body.appendChild(iframe);

        // Esperar un momento para que la clonación se complete
        setTimeout(function() {
            // Ocultar el mensaje de carga
            document.getElementById('loading').remove();
            // Recargar la página principal
            location.reload();
        }, 1000); // Esperar 1 segundo
    }
}
</script>";
                echo "</tr>";
            }
        }
        ?>
        </tbody>
    </table>
    <button type="button" class="btn" style="background-color: #217346; color: white;" data-toggle="modal" data-target="#filterModal"><i class="fas fa-file-excel"></i> xls Comisiones Académicas</button>

</div>

<!-- Modal -->
<div class="modal fade" id="informeModal" tabindex="-1" role="dialog" aria-labelledby="informeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="informeModalLabel">Editar Informe de Comisión</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="actualizar_solicitud_informe_modal.php" method="post">
                    <input type="hidden" name="comision_id" id="comision_id">
                    <div class="form-group">
                        <label for="fecha_informe">Fecha Informe:</label>
                        <input type="date" class="form-control" id="fecha_informe" name="fecha_informe" required>
                    </div>
                    <div class="form-group">
                        <label for="folios">Folios:</label>
                        <input type="text" class="form-control" id="folios" name="folios">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarCambiosBtn">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal de filtros -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="width: 80%;">
    <div class="modal-content">
      <div class="modal-header">
        
        <h5 class="text-primary" id="filterModalLabel">Filtros Reporte Comisiones</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
                 <div class="form-group">
            <label for="vigencia">Vigencia</label>
            <select class="form-control" id="vigencia">
              <option value="Todos">Todos</option>
              <option value="2023">2023</option>
              <option value="2024">2024</option>
              <option value="2025">2025</option>

            </select>
          </div>
             <div class="form-group">
            <label for="tipo_comision">INT/EXT</label>
            <select class="form-control" id="tipo_comision">
              <option value="Todos">Todos</option>
              <option value="INT">Interior</option>
              <option value="EXT">Exterior</option>
            </select>
          </div>
          <div class="form-group">
            <label for="estado">Estado</label>
            <select class="form-control" id="estado">
              <option value="Todos">Todos</option>
              <option value="Activa">Activas</option>
              <option value="finalizada">Finalizadas</option>
              <option value="anulada">Anuladas</option>
            </select>
          </div>
          <div class="form-group">
            <label for="reintegrado">Entrega de informe</label>
            <select class="form-control" id="reintegrado">
              <option value="Todos">Todos</option>
              <option value="1">Entregado</option>
              <option value="0">Pendiente</option>
            </select>
          </div>
          <button type="button" style="background-color: #217346; color: white;" class="btn btn-primary" onclick="applyFilters()">Reporte Comisiones Académicas</button>
        </form>
      </div>
    </div>
  </div>
</div>
    <!--termina filtro-->
<script>
$(document).ready(function() {
    $('#comisionesTable').DataTable({
           "order": [],
         "columnDefs": [
            {
                "targets": 0, // Índice de la primera columna
                "visible": false // Oculta la columna
            }
        ],// No aplica ningún orden predeterminado
                stateSave: true 

    });
    
    // Al abrir el modal de informe, cargar los datos actuales del informe
    $('#informeModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var comisionId = button.data('id');
        var modal = $(this);
        modal.find('#comision_id').val(comisionId);
        
        // Realizar una llamada AJAX para obtener los datos del informe
        $.ajax({
            url: 'obtener_datos_informe.php',
            type: 'POST',
            data: {
                comision_id: comisionId
            },
            success: function(response) {
                console.log(response); // Verificar el contenido de la respuesta

                // Intentar parsear la respuesta JSON
                var data = JSON.parse(response);

                if (data.error) {
                    console.error(data.error);
                } else {
                    modal.find('#fecha_informe').val(data.fecha_informe_formateada);
                    modal.find('#folios').val(data.folios);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    // Guardar cambios del informe
    $('#guardarCambiosBtn').click(function() {
        var form = $('#informeModal form');
        var comisionId = form.find('#comision_id').val();
        var fechaInforme = form.find('#fecha_informe').val();
        var folios = form.find('#folios').val();

        // Mostrar los valores en la consola del navegador
        console.log("Comisión ID: " + comisionId);
        console.log("Fecha Informe: " + fechaInforme);
        console.log("Folios: " + folios);

        // Enviar los datos al script de actualización vía AJAX
        $.ajax({
            url: 'actualizar_solicitud_informe_modal.php',
            type: 'POST',
            data: {
                comision_id: comisionId,
                fecha_informe: fechaInforme,
                folios: folios
            },
            success: function(response) {
                // Manejar la respuesta del servidor (opcional)
                console.log(response);

                // Cerrar el modal después de actualizar los datos
                $('#informeModal').modal('hide');

                // Recargar la página para reflejar los cambios (opcional)
                location.reload();
            },
            error: function(xhr, status, error) {
                // Manejar errores de AJAX (opcional)
                console.error(xhr.responseText);
            }
        });
    }); 
});
</script>

    <script>
function applyFilters() {
  // Obtener los valores seleccionados en el formulario
  var estado = document.getElementById("estado").value;
  var reintegrado = document.getElementById("reintegrado").value;
  var vigencia = document.getElementById("vigencia").value;
  var tipo_comision = document.getElementById("tipo_comision").value;

  // Construir la URL con los parámetros de filtro
  var url = "excel_c_academicas.php?estado=" + encodeURIComponent(estado) + "&reintegrado=" + encodeURIComponent(reintegrado)+ "&vigencia=" + encodeURIComponent(vigencia)+ "&tipo_comision=" + encodeURIComponent(tipo_comision);
  
  // Redirigir al script de generación del archivo Excel
  window.location.href = url;
}
</script>
</body>
</html>
