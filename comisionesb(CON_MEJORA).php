<?php
require 'conn.php';
require('include/headerz.php');
$anio = isset($_GET['anio']) ? $_GET['anio'] : 0;

// Actualizar comisiones vencidas
$conn->query("UPDATE comision_academica SET estado='finalizada' WHERE estado='Activa' AND vence < NOW()");



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

// Además, se agregan consultas para el dashboard:
$countActivas = $conn->query("SELECT COUNT(*) as total FROM comision_academica WHERE estado='Activa'")->fetch_assoc()['total'];
$countFinalizadas = $conn->query("SELECT COUNT(*) as total FROM comision_academica WHERE estado='finalizada'")->fetch_assoc()['total'];
$countAnuladas = $conn->query("SELECT COUNT(*) as total FROM comision_academica WHERE estado='anulada'")->fetch_assoc()['total'];
$countPendientesInforme = $conn->query("SELECT COUNT(*) as total FROM comision_academica WHERE reintegrado=0 AND estado NOT IN ('anulada')")->fetch_assoc()['total'];

// Obtener datos de la tabla...
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comisiones Académicas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos personalizados (cards, avatares, transiciones) */
        .avatar-sm {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.02);
        }
        .filter-pill.active {
            background-color: var(--bs-primary);
            color: white;
        }
        .card-view .card {
            transition: transform 0.2s;
        }
        .card-view .card:hover {
            transform: translateY(-3px);
        }
        /* Vista de tarjetas: oculta tabla, muestra cards */
        .table-view .card-list {
            display: none;
        }
        .card-view .table-responsive {
            display: none;
        }
        .card-view .card-list {
            display: block;
        }
        @media (max-width: 768px) {
            .btn-group-toggle {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
<div id="contenido">
    <div class="container-fluid px-4">
        <!-- Dashboard de resumen -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-muted mb-1">Comisiones Activas</h6>
                                <h2 class="mb-0"><?= $countActivas ?></h2>
                            </div>
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Más tarjetas: finalizadas, anuladas, pendientes informe -->
        </div>

        <!-- Card principal con filtros y tabla -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center">
                <h2 class="h4 mb-0">Comisiones Académicas</h2>
                <div class="btn-group" role="group">
                    <button type="button" id="tableViewBtn" class="btn btn-outline-secondary active"><i class="fas fa-table"></i> Tabla</button>
                    <button type="button" id="cardViewBtn" class="btn btn-outline-secondary"><i class="fas fa-th-large"></i> Tarjetas</button>
                </div>
            </div>

            <!-- Filtros rápidos -->
            <div class="card-body border-bottom">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="globalSearch" class="form-control" placeholder="Buscar por profesor, resolución...">
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <div class="btn-group flex-wrap" role="group" id="estadoFilters">
                            <button type="button" class="btn btn-outline-secondary filter-pill active" data-filter="estado" data-value="all">Todos</button>
                            <button type="button" class="btn btn-outline-secondary filter-pill" data-filter="estado" data-value="Activa">Activos</button>
                            <button type="button" class="btn btn-outline-secondary filter-pill" data-filter="estado" data-value="finalizada">Finalizados</button>
                            <button type="button" class="btn btn-outline-secondary filter-pill" data-filter="estado" data-value="anulada">Anulados</button>
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <div class="btn-group" role="group" id="tipoFilters">
                            <button type="button" class="btn btn-outline-secondary filter-pill active" data-filter="tipo" data-value="all">Todos</button>
                            <button type="button" class="btn btn-outline-secondary filter-pill" data-filter="tipo" data-value="INT">INT</button>
                            <button type="button" class="btn btn-outline-secondary filter-pill" data-filter="tipo" data-value="EXT">EXT</button>
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <select class="form-select" id="periodoFilter">
                            <option value="">Todos los períodos</option>
                            <?php
                            // Obtener períodos únicos de los datos actuales
                            $periodos = [];
                            $result->data_seek(0);
                            while ($row = $result->fetch_assoc()) {
                                if (!empty($row['periodo_academico']) && !in_array($row['periodo_academico'], $periodos)) {
                                    $periodos[] = $row['periodo_academico'];
                                }
                            }
                            sort($periodos);
                            foreach ($periodos as $per) {
                                echo "<option value=\"$per\">$per</option>";
                            }
                            $result->data_seek(0);
                            ?>
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                            <i class="fas fa-sliders-h"></i> Filtros avanzados
                        </button>
                    </div>
                </div>
                <div class="collapse mt-3" id="advancedFilters">
                    <div class="card card-body bg-light">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Fecha desde</label>
                                <input type="date" id="fechaDesde" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha hasta</label>
                                <input type="date" id="fechaHasta" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Departamento</label>
                                <input type="text" id="deptoFilter" class="form-control" placeholder="Nombre departamento">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <!-- Vista de tabla -->
                <div class="table-responsive table-view">
                    <table id="comisionesTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Profesor</th>
                                <th>Período</th>
                                <th data-bs-toggle="tooltip" title="Interior / Exterior">INT/EXT</th>
                                <th>Departamento</th>
                                <th data-bs-toggle="tooltip" title="Ciudad(es) destino">Destino</th>
                                <th data-bs-toggle="tooltip" title="Número de resolución">#Res</th>
                                <th>Tramitó</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $rows = [];
                        while ($row = $result->fetch_assoc()) {
                            $rows[] = $row;
                            $badgeClass = match($row['estado_comision']) {
                                'Activa' => 'bg-success',
                                'finalizada' => 'bg-secondary',
                                'anulada' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            $estadoTexto = match($row['estado_comision']) {
                                'Activa' => 'Activo',
                                'finalizada' => 'Finalizado',
                                'anulada' => 'Anulado',
                                default => $row['estado_comision']
                            };
                            $nombreCompleto = trim($row['apellido1'] . ' ' . $row['nombre1'] . ' ' . ($row['nombre2'] ?? ''));
                            $iniciales = strtoupper(substr($row['nombre1'], 0, 1) . substr($row['apellido1'], 0, 1));
                            ?>
                            <tr data-id="<?= $row['id_comision'] ?>">
                                <td class="id"><?= $row['id_comision'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle me-2"><?= $iniciales ?></div>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($nombreCompleto) ?></div>
                                            <small class="text-muted">Cédula: <?= $row['documento_profesor'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="periodo"><?= $row['periodo_academico'] ?></td>
                                <td class="tipo"><?= $row['tipo_estudio'] ?></td>
                                <td class="departamento"><?= htmlspecialchars(substr($row['depto_nom_propio'], 0, 30)) ?></td>
                                <td class="destino" title="<?= htmlspecialchars($row['evento'] . ' - ' . $row['fecha_formateada']) ?>">
                                    <?= htmlspecialchars(substr($row['ciudades_concat'], 0, 20)) ?>
                                </td>
                                <td class="resolucion">
                                    <?php if (!empty($row['link_resolucion'])): ?>
                                        <a href="<?= $row['link_resolucion'] ?>" target="_blank"><?= substr($row['No_resolucion'], 0, 15) ?></a>
                                    <?php else: ?>
                                        <?= substr($row['No_resolucion'], 0, 15) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="tramito"><?= $row['tramito'] ?></td>
                                <td class="estado"><span class="badge <?= $badgeClass ?>"><?= $estadoTexto ?></span></td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="actualizar_formacion.php?id=<?= $row['id_comision'] ?>"><i class="fas fa-edit me-2"></i>Editar</a></li>
                                            <li><a class="dropdown-item" href="<?= $row['tipo_estudio'] == 'EXT' ? 'resolucion_doc_ext.php' : 'resolucion_docb.php' ?>?id=<?= $row['id_comision'] ?>"><i class="far fa-file-word me-2"></i>Res. Individual</a></li>
                                            <li><a class="dropdown-item" href="resolucion_docc_grupal.php?no_resolucion=<?= urlencode($row['No_resolucion']) ?>&fecha_resolucion=<?= $row['fecha_resolucion'] ?>"><i class="fas fa-layer-group me-2"></i>Res. Múltiple</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#informeModal" data-id="<?= $row['id_comision'] ?>"><i class="fas fa-upload me-2"></i>Subir informe</button></li>
                                            <li><button class="dropdown-item text-danger" onclick="confirmarAnulacion(<?= $row['id_comision'] ?>)"><i class="fas fa-ban me-2"></i>Anular</button></li>
                                            <li><button class="dropdown-item" onclick="confirmarClonacion(<?= $row['id_comision'] ?>)"><i class="fas fa-copy me-2"></i>Clonar</button></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Vista de tarjetas (se genera dinámicamente con JS) -->
                <div class="card-list p-3" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modales (igual que antes) -->
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#comisionesTable').DataTable({
        "order": [],
        "columnDefs": [{ "targets": 0, "visible": false }],
        "stateSave": true,
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }
    });

    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    // Filtros con pills
    $('#estadoFilters .filter-pill').on('click', function() {
        var value = $(this).data('value');
        $('#estadoFilters .filter-pill').removeClass('active');
        $(this).addClass('active');
        if (value === 'all') table.column(8).search('').draw();
        else table.column(8).search('^' + value + '$', true, false).draw();
    });
    $('#tipoFilters .filter-pill').on('click', function() {
        var value = $(this).data('value');
        $('#tipoFilters .filter-pill').removeClass('active');
        $(this).addClass('active');
        if (value === 'all') table.column(3).search('').draw();
        else table.column(3).search('^' + value + '$', true, false).draw();
    });
    $('#periodoFilter').on('change', function() {
        table.column(2).search($(this).val() ? '^' + $(this).val() + '$' : '', true, false).draw();
    });
    $('#globalSearch').on('keyup', function() {
        table.search(this.value).draw();
    });
    // Filtros avanzados
    $('#fechaDesde, #fechaHasta, #deptoFilter').on('change keyup', function() {
        // Aquí puedes añadir lógica personalizada, por ejemplo filtrar por rango de fechas usando column().search()
        // Por simplicidad, se omite pero se puede implementar.
    });

    // Cambio entre vista tabla/tarjetas
    function generateCards() {
        var cardsHtml = '';
        table.rows().every(function() {
            var data = this.data();
            var id = data[0];
            var profesor = data[1]; // HTML
            var periodo = data[2];
            var tipo = data[3];
            var depto = data[4];
            var destino = data[5];
            var resolucion = data[6];
            var tramito = data[7];
            var estadoHtml = data[8];
            var acciones = data[9]; // HTML del dropdown
            cardsHtml += `
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>${profesor}</div>
                            <div>${estadoHtml}</div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6"><small class="text-muted">Período:</small> ${periodo}</div>
                            <div class="col-6"><small class="text-muted">Tipo:</small> ${tipo}</div>
                            <div class="col-12"><small class="text-muted">Departamento:</small> ${depto}</div>
                            <div class="col-12"><small class="text-muted">Destino:</small> ${destino}</div>
                            <div class="col-12"><small class="text-muted">Resolución:</small> ${resolucion}</div>
                            <div class="col-12"><small class="text-muted">Tramitó:</small> ${tramito}</div>
                        </div>
                        <div class="mt-2">${acciones}</div>
                    </div>
                </div>`;
        });
        $('.card-list').html(cardsHtml);
    }

    $('#tableViewBtn').click(function() {
        $('body').removeClass('card-view').addClass('table-view');
        $(this).addClass('active');
        $('#cardViewBtn').removeClass('active');
        $('.table-responsive').show();
        $('.card-list').hide();
    });
    $('#cardViewBtn').click(function() {
        $('body').removeClass('table-view').addClass('card-view');
        $(this).addClass('active');
        $('#tableViewBtn').removeClass('active');
        $('.table-responsive').hide();
        $('.card-list').show();
        generateCards();
    });
    // Inicializar en tabla
    $('body').addClass('table-view');
    $('#tableViewBtn').addClass('active');
});
</script>
</body>
</html>