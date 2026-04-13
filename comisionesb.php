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
    <title>Comisiones Académicas · Unicauca</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ===== INSTITUTIONAL VARIABLES ===== */
        :root {
            --azul-oscuro: #002A9E;
            --morado: #4C19AF;
            --azul-rey: #0051C6;
            --azul-cielo: #16A8E1;
            --turquesa: #04B2B5;
            --verde: #249337;
            --verde-limon: #8CBD22;
            --rojo: #E52724;
            --naranja: #EC6C1F;
            --amarillo: #F8AE15;
            --gris-light: #F8FAFE;
            --gris-border: #E9EEF3;
            --shadow-card: 0 12px 28px rgba(0,0,0,0.05), 0 0 0 1px rgba(0,0,0,0.02);
        }

        body {
            background: #F1F5F9;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto;
        }

        /* Contenedor principal de la tabla */
        .uc-page-wrapper {
            background: white;
            border-radius: 28px;
            box-shadow: var(--shadow-card);
            overflow: hidden;

            /* CAMBIO CLAVE: Añadimos margen lateral para que no pegue a los bordes */
            margin: 0 20px 2rem 20px; 

            transition: all 0.2s;
        }

        /* Header interno del reporte */
        .uc-card-header {
            background: white;
            padding: 1rem 1.5rem;
            border-bottom: 2px solid var(--gris-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .uc-card-title {
            font-weight: 700;
            font-size: 1.3rem;
            background: linear-gradient(135deg, var(--azul-oscuro), var(--morado));
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .uc-badge-anio {
            background: var(--azul-cielo);
            color: white;
            border-radius: 40px;
            padding: 4px 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* Botones de exportación */
        .uc-btn-xls, .uc-btn-aust {
            border: none;
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .uc-btn-xls {
            background: var(--verde);
            color: white;
        }
        .uc-btn-xls:hover {
            background: #1a6e2c;
            transform: translateY(-2px);
        }

        .uc-btn-aust {
            background: var(--verde-limon);
            color: #2c3e2f;
        }
        .uc-btn-aust:hover {
            background: #7aa51f;
            color: white;
        }

        /* Tabla moderna */
        .table {
            margin: 0;
            font-size: 0.85rem;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: var(--azul-oscuro);
            color: white;
            font-weight: 600;
            padding: 14px 10px;
            border-bottom: none;
            font-size: 0.8rem;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .table tbody tr {
            transition: background 0.2s;
        }
        .table tbody tr:hover {
            background: #FEFCE8;
        }
        .table td {
            padding: 12px 8px;
            vertical-align: middle;
            border-bottom: 1px solid var(--gris-border);
            background-color: white;
        }

        /* Scroll horizontal suave */
        .uc-table-scroll {
            overflow-x: auto;
            width: 100%;
            scrollbar-width: thin;
        }

        /* Botón acciones moderno */
        .acciones-btn {
            background: #F1F5F9;
            border: 1px solid #E2E8F0;
            border-radius: 40px;
            padding: 6px 16px;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--azul-oscuro);
            transition: all 0.2s;
        }
        .acciones-btn:hover {
            background: var(--azul-cielo);
            color: white;
            border-color: var(--azul-cielo);
        }

        /* Modal acciones cards mejorado */
        .action-card {
            background: white;
            border-radius: 20px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            border: 1px solid #EDF2F7;
        }
        .action-card:hover {
            transform: translateY(-5px);
            border-color: var(--azul-cielo);
            box-shadow: 0 12px 22px rgba(0,0,0,0.08);
        }
        .icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 60px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #F1F5F9;
        }

        /* Estados abreviados */
        .estado-badge {
            display: inline-block;
            background: #EFF6FF;
            border-radius: 30px;
            padding: 2px 10px;
            font-size: 0.7rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div id="contenido">
    <div class="uc-page-wrapper">
        <div class="uc-card-header">
            <h5 class="uc-card-title">
                <i class="fas fa-chalkboard-user"></i> Comisiones Académicas
                <?php if ($anio != 0): ?>
                    <span class="uc-badge-anio"><?= htmlspecialchars($anio) ?></span>
                <?php endif; ?>
            </h5>
            <div class="uc-header-actions">
                <button type="button" class="btn uc-btn-xls" data-toggle="modal" data-target="#filterModal">
                    <i class="fas fa-file-excel"></i> Reporte Comisiones
                </button>
                <button type="button" class="btn uc-btn-aust" data-toggle="modal" data-target="#filterModalb">
                    <i class="fas fa-chart-line"></i> Austeridad
                </button>
            </div>
        </div>
        <div class="uc-table-scroll">
            <table id="comisionesTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th><th>Profesor</th><th>Periodo</th><th>INT/EXT</th>
                        <th>Depto</th><th>Destino</th><th>#Res</th><th>Tramitó</th><th>Est</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $observacionx = $row["link_resolucion"];
                        $link = !empty($observacionx) ? $observacionx : '';
                        echo "<tr>";
                        // ID con enlace si existe resolución
                        if (!empty($link)) {
                            echo "<td><a href='$link' target='_blank' class='fw-bold' style='color:var(--azul-rey)'>" . $row["id_comision"] . "</a></td>";
                        } else {
                            echo "<td>" . $row["id_comision"] . "</td>";
                        }
                        echo "<td>" . $row["documento_profesor"] . " - " . substr($row["apellido1"] . " " . substr($row["apellido1"],0,1) . ". " . $row["nombre1"]. " " . $row["nombre2"], 0, 25) . "</td>";
                        echo "<td>" . $row["periodo_academico"] . "</td>";
                        echo "<td>" . $row["tipo_estudio"] . "</td>";
                        echo "<td>" . substr($row["depto_nom_propio"], 0, 15) . " - " . $row["nombre_fac_min"] . "</td>";
                        echo "<td title='" . htmlspecialchars($row["evento"] . " - " . $row["fecha_formateada"], ENT_QUOTES) . "'>" . substr($row["ciudades_concat"], 0, 10) . "</td>";
                        if (!empty($link)) {
                            echo "<td><a href='$link' target='_blank'>" . substr($row["No_resolucion"], 0, 20) . "</a></td>";
                        } else {
                            echo "<td>" . substr($row["No_resolucion"], 0, 20) . "</td>";
                        }
                        echo "<td>" . substr($row["tramito"], 0, 6) . "</td>";
                        $estado_abreviado = '';
                        switch ($row["estado_comision"]) {
                            case 'finalizada': $estado_abreviado = 'fn'; break;
                            case 'Activa': $estado_abreviado = 'ac'; break;
                            case 'anulada': $estado_abreviado = 'an'; break;
                            default: $estado_abreviado = '';
                        }
                        $color_estado = ($row["estado_comision"] == "anulada") ? "var(--rojo)" : "var(--verde)";
                        echo "<td style='color:$color_estado; font-weight:600;'>" . strtoupper($estado_abreviado) . "</td>";
                        echo "<td class='text-center'>
                                <button class='btn acciones-btn' data-id='{$row["id_comision"]}' data-tipo='{$row["tipo_estudio"]}'
                                    data-no-resolucion='".htmlspecialchars($row["No_resolucion"])."'
                                    data-fecha-resolucion='{$row["fecha_resolucion"]}' data-toggle='modal' data-target='#accionesModal'>
                                    <i class='fas fa-ellipsis-h'></i> Acciones
                                </button>
                              </td>";
                        echo "</tr>";
                    }
                } ?>
                </tbody>
            </table>
        </div>
    </div>
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
<!-- Modal de acciones unificadas -->
<!-- Modal de acciones unificadas - Versión profesional Unicauca -->
<div class="modal fade" id="accionesModal" tabindex="-1" role="dialog" aria-labelledby="accionesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 28px; overflow: hidden;">
            <!-- Header con gradiente institucional -->
            <div class="modal-header" style="background: linear-gradient(135deg, #002A9E 0%, #4C19AF 100%); border-bottom: none; padding: 1.2rem 1.8rem;">
                <h5 class="modal-title fw-bold text-white" id="accionesModalLabel">
                    <i class="fas fa-cog me-2"></i> Acciones de la comisión
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Body con tarjetas de acción -->
            <div class="modal-body" style="padding: 2rem 1.5rem 1.5rem 1.5rem;">
                <div class="row g-3 text-center">
                    <!-- Editar -->
                    <div class="col-6 col-md-3">
                        <a href="#" id="modalEditar" class="action-card d-block p-3 rounded-4 text-decoration-none transition-all">
                            <div class="icon-circle mx-auto mb-3" style="width: 56px; height: 56px; background: #EFF6FF; border-radius: 60px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                                <i class="fas fa-pencil-alt fa-2x" style="color: #002A9E;"></i>
                            </div>
                            <span class="fw-semibold" style="color: #1E293B; font-size: 0.85rem;">Editar</span>
                        </a>
                    </div>
                    <!-- Resolución Individual -->
                    <div class="col-6 col-md-3">
                        <a href="#" id="modalResInd" class="action-card d-block p-3 rounded-4 text-decoration-none transition-all">
                            <div class="icon-circle mx-auto mb-3" style="width: 56px; height: 56px; background: #FEFCE8; border-radius: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="far fa-file-word fa-2x" style="color: #8CBD22;"></i>
                            </div>
                            <span class="fw-semibold" style="color: #1E293B; font-size: 0.85rem;">Res. Individual</span>
                        </a>
                    </div>
                    <!-- Resolución Multi (párrafo) -->
                    <div class="col-6 col-md-3">
                        <a href="#" id="modalResMultiP" class="action-card d-block p-3 rounded-4 text-decoration-none transition-all">
                            <div class="icon-circle mx-auto mb-3" style="width: 56px; height: 56px; background: #FEFCE8; border-radius: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-layer-group fa-2x" style="color: #8CBD22;"></i>
                            </div>
                            <span class="fw-semibold" style="color: #1E293B; font-size: 0.85rem;">Res. Multi (párrafo)</span>
                        </a>
                    </div>
                    <!-- Resolución Multi (tabla) -->
                    <div class="col-6 col-md-3">
                        <a href="#" id="modalResMultiT" class="action-card d-block p-3 rounded-4 text-decoration-none transition-all">
                            <div class="icon-circle mx-auto mb-3" style="width: 56px; height: 56px; background: #FEFCE8; border-radius: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-table fa-2x" style="color: #8CBD22;"></i>
                            </div>
                            <span class="fw-semibold" style="color: #1E293B; font-size: 0.85rem;">Res. Multi (tabla)</span>
                        </a>
                    </div>
                    <!-- Subir Informe -->
                    <div class="col-6 col-md-3">
                        <button type="button" id="modalSubirInforme" class="action-card w-100 p-3 rounded-4 text-decoration-none border-0 bg-transparent transition-all">
                            <div class="icon-circle mx-auto mb-3" style="width: 56px; height: 56px; background: #E6F4EA; border-radius: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-file-signature fa-2x" style="color: #249337;"></i>
                            </div>
                            <span class="fw-semibold" style="color: #1E293B; font-size: 0.85rem;">Subir informe</span>
                        </button>
                    </div>
                    <!-- Anular -->
                    <div class="col-6 col-md-3">
                        <button type="button" id="modalAnular" class="action-card w-100 p-3 rounded-4 text-decoration-none border-0 bg-transparent transition-all">
                            <div class="icon-circle mx-auto mb-3" style="width: 56px; height: 56px; background: #FEE2E2; border-radius: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-ban fa-2x" style="color: #E52724;"></i>
                            </div>
                            <span class="fw-semibold" style="color: #1E293B; font-size: 0.85rem;">Anular</span>
                        </button>
                    </div>
                    <!-- Clonar -->
                    <div class="col-6 col-md-3">
                        <button type="button" id="modalClonar" class="action-card w-100 p-3 rounded-4 text-decoration-none border-0 bg-transparent transition-all">
                            <div class="icon-circle mx-auto mb-3" style="width: 56px; height: 56px; background: #EFF6FF; border-radius: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-clone fa-2x" style="color: #0051C6;"></i>
                            </div>
                            <span class="fw-semibold" style="color: #1E293B; font-size: 0.85rem;">Clonar</span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Footer simplificado -->
            <div class="modal-footer border-0 pt-0 pb-4" style="justify-content: center;">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-5" data-dismiss="modal" style="font-size: 0.8rem; border-color: #E2E8F0; color: #475569;">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales para las tarjetas de acción (añadir al <style> de comisionesb.php) -->
<style>
    .action-card {
        transition: all 0.25s ease-in-out;
        background-color: #ffffff;
        border: 1px solid #E9EEF3;
        cursor: pointer;
    }
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px -8px rgba(0,0,0,0.1);
        border-color: #C8A951;
    }
    .action-card:hover .icon-circle {
        transform: scale(1.05);
    }
    .transition-all {
        transition: all 0.2s ease;
    }
</style>
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
    
    <div class="modal fade" id="filterModalb" tabindex="-1" role="dialog" aria-labelledby="filterModalbLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="filterModalbLabel" style="color: grey; font-weight: 700;">Filtros Reporte Austeridad</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: black; opacity: 1; font-size: 28px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="vigenciab">Vigencia</label>
            <select class="form-control" id="vigenciab">
              <option value="Todos">Todos</option>
              <option value="2023">2023</option>
              <option value="2024">2024</option>
              <option value="2025">2025</option>
            </select>
          </div>
          <div class="form-group">
            <label for="trimestreb">Trimestre</label>
            <select class="form-control" id="trimestreb">
              <option value="Todos">Todos</option>
              <option value="I">I</option>
              <option value="II">II</option>
              <option value="III">III</option>
              <option value="IV">IV</option>
            </select>
          </div>
          <div class="form-group">
            <label for="tipo_comisionb">INT/EXT</label>
            <select class="form-control" id="tipo_comisionb">
              <option value="Todos">Todos</option>
              <option value="INT">Interior</option>
              <option value="EXT">Exterior</option>
            </select>
          </div>
          <button type="button" class="btn btn-primary btn-unicauca-success" onclick="applyFiltersb()">Reporte Austeridad</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
// Funciones globales de anulación y clonación (definidas una sola vez)
function confirmarAnulacion(comisionId) {
    var medio_comunicacion = prompt('Indique el medio de comunicación (ej. Oficio 3.5.5-4 del 3 de agosto de 2024):');
    if (!medio_comunicacion) {
        alert('Debe proporcionar el medio de comunicación.');
        return;
    }
    var razon = prompt('Indique el motivo de anulación (ej. Problemas logísticos con la entidad...):');
    if (!razon) {
        alert('Debe proporcionar una razón.');
        return;
    }
    if (confirm('¿Está seguro que desea anular el registro?')) {
        document.body.insertAdjacentHTML('beforeend', '<div id=\"loading\" style=\"position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); padding:20px; background:white; border:1px solid #ccc; border-radius:5px; box-shadow:0 0 10px rgba(0,0,0,0.2);\">Procesando, por favor espere...</div>');
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = 'anular_registro.php?comision_id=' + comisionId + 
                      '&medio_comunicacion=' + encodeURIComponent(medio_comunicacion) +
                      '&razon=' + encodeURIComponent(razon);
        document.body.appendChild(iframe);
        setTimeout(function() {
            document.getElementById('loading').remove();
            location.reload();
        }, 1000);
    }
}

function confirmarClonacion(comisionId) {
    var cedula_tercero = prompt('Indique la cédula del tercero:');
    if (!cedula_tercero) {
        alert('Debe proporcionar la cédula del tercero.');
        return;
    }
    if (confirm('¿Está seguro que desea clonar el registro con la cédula proporcionada?')) {
        document.body.insertAdjacentHTML('beforeend', '<div id=\"loading\" style=\"position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); padding:20px; background:white; border:1px solid #ccc; border-radius:5px; box-shadow:0 0 10px rgba(0,0,0,0.2);\">Procesando, por favor espere...</div>');
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = 'clonar_registro.php?comision_id=' + comisionId + 
                      '&cedula_tercero=' + encodeURIComponent(cedula_tercero);
        document.body.appendChild(iframe);
        setTimeout(function() {
            document.getElementById('loading').remove();
            location.reload();
        }, 1000);
    }
}

$(document).ready(function() {
    // 1. Inicializar DataTable asignándolo a una variable y agregando autoWidth: false
    var table = $('#comisionesTable').DataTable({
        "order": [],
        "columnDefs": [
            { "targets": 0, "visible": false }
        ],
        "stateSave": true,
        "autoWidth": false // Ayuda a que las columnas se ajusten al nuevo contenedor
    });

    // 2. Escuchar el evento 'resize' disparado desde el menú lateral para ajustar las columnas
    window.addEventListener('resize', function() {
        if (table) {
            table.columns.adjust().draw();
        }
    });

    // --- El resto de tu código queda exactamente igual ---

    // Modal de acciones
    $('#accionesModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var tipo = button.data('tipo');
        var noResolucion = button.data('no-resolucion');
        var fechaResolucion = button.data('fecha-resolucion');

        var linkEditar = 'actualizar_formacion.php?id=' + id;
        var linkResInd = (tipo === 'EXT') ? 'resolucion_doc_ext_b.php?id=' + id : 'resolucion_docc.php?id=' + id;
        var linkResMultiP = 'resolucion_docc_grupal.php?no_resolucion=' + encodeURIComponent(noResolucion) + '&fecha_resolucion=' + encodeURIComponent(fechaResolucion);
        var linkResMultiT = 'resolucion_docc_grupal_t.php?no_resolucion=' + encodeURIComponent(noResolucion) + '&fecha_resolucion=' + encodeURIComponent(fechaResolucion);

        $('#modalEditar').attr('href', linkEditar);
        $('#modalResInd').attr('href', linkResInd);
        $('#modalResMultiP').attr('href', linkResMultiP);
        $('#modalResMultiT').attr('href', linkResMultiT);

        // Guardar ID para acciones que no son enlaces
        $('#modalSubirInforme').data('id', id);
        $('#modalAnular').data('id', id);
        $('#modalClonar').data('id', id);
    });

    // Subir informe: abrir el modal existente
    $('#modalSubirInforme').on('click', function() {
        var id = $(this).data('id');
        // Activar el modal de informe pasándole el ID
        $('#informeModal').data('id', id);
        $('#informeModal').modal('show');
    });

    // Anular
    $('#modalAnular').on('click', function() {
        var id = $(this).data('id');
        confirmarAnulacion(id);
        $('#accionesModal').modal('hide');
    });

    // Clonar
    $('#modalClonar').on('click', function() {
        var id = $(this).data('id');
        confirmarClonacion(id);
        $('#accionesModal').modal('hide');
    });

    // Modal de informe: adaptado para recibir ID desde el nuevo botón
    $('#informeModal').on('show.bs.modal', function (event) {
        var comisionId = $(this).data('id');
        if (!comisionId) {
            // Si viene del botón tradicional, lo obtiene del relatedTarget
            var button = $(event.relatedTarget);
            comisionId = button ? button.data('id') : null;
        }
        if (comisionId) {
            $(this).find('#comision_id').val(comisionId);
            // Cargar datos del informe
            $.ajax({
                url: 'obtener_datos_informe.php',
                type: 'POST',
                data: { comision_id: comisionId },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (!data.error) {
                        $('#informeModal').find('#fecha_informe').val(data.fecha_informe_formateada);
                        $('#informeModal').find('#folios').val(data.folios);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    });

    // Guardar cambios del informe
    $('#guardarCambiosBtn').click(function() {
        var comisionId = $('#informeModal').find('#comision_id').val();
        var fechaInforme = $('#informeModal').find('#fecha_informe').val();
        var folios = $('#informeModal').find('#folios').val();

        $.ajax({
            url: 'actualizar_solicitud_informe_modal.php',
            type: 'POST',
            data: {
                comision_id: comisionId,
                fecha_informe: fechaInforme,
                folios: folios
            },
            success: function(response) {
                $('#informeModal').modal('hide');
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});

// Funciones para los reportes (sin cambios)
function applyFilters() {
    var estado = document.getElementById("estado").value;
    var reintegrado = document.getElementById("reintegrado").value;
    var vigencia = document.getElementById("vigencia").value;
    var tipo_comision = document.getElementById("tipo_comision").value;
    var url = "excel_c_academicas.php?estado=" + encodeURIComponent(estado) + "&reintegrado=" + encodeURIComponent(reintegrado)+ "&vigencia=" + encodeURIComponent(vigencia)+ "&tipo_comision=" + encodeURIComponent(tipo_comision);
    window.location.href = url;
}

function applyFiltersb() {
    var vigenciab = document.getElementById("vigenciab").value;
    var trimestreb = document.getElementById("trimestreb").value;
    var tipo_comisionb = document.getElementById("tipo_comisionb").value;
    var url = "excel_austeridad.php?vigencia=" + encodeURIComponent(vigenciab) + 
              "&trimestre=" + encodeURIComponent(trimestreb) + 
              "&tipo_comision=" + encodeURIComponent(tipo_comisionb);
    window.location.href = url;
}
</script>
</body>
</html>
