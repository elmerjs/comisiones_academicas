<?php
require 'conn.php';
require('include/headerz.php');
$usuario = $_SESSION['name'];
$anio = isset($_GET['anio']) ? $_GET['anio'] : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Informes · Unicauca</title>
    <!-- Bootstrap 4, DataTables, Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ===== VARIABLES INSTITUCIONALES ===== */
        :root {
            --azul-oscuro: #002A9E;
            --morado: #4C19AF;
            --azul-rey: #0051C6;
            --azul-cielo: #16A8E1;
            --verde: #249337;
            --verde-limon: #8CBD22;
            --rojo: #E52724;
            --gris-border: #E9EEF3;
            --shadow-card: 0 12px 28px rgba(0,0,0,0.05);
        }
        body {
            background: #F1F5F9;
            font-family: 'Segoe UI', system-ui;
        }
        /* Contenedor principal tipo card */
        .uc-page-wrapper {
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-card);
            margin: 0 20px 2rem 20px;
            overflow: hidden;
        }
        .uc-card-header {
            background: white;
            padding: 0.8rem 1.5rem;
            border-bottom: 2px solid var(--gris-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .uc-card-title {
            font-weight: 700;
            font-size: 1.2rem;
            background: linear-gradient(135deg, var(--azul-oscuro), var(--morado));
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        /* Botones de filtro */
        .filter-btn {
            background: white;
            border: 1px solid var(--gris-border);
            border-radius: 30px;
            padding: 4px 16px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--azul-oscuro);
            transition: all 0.2s;
            margin-right: 8px;
        }
        .filter-btn:hover {
            background: var(--azul-cielo);
            color: white;
            border-color: var(--azul-cielo);
        }
        .btn-uc-primary {
            background: var(--verde);
            color: white;
            border-radius: 30px;
            padding: 6px 18px;
            font-size: 0.8rem;
            font-weight: 600;
            border: none;
        }
        .btn-uc-secondary {
            background: #e9ecef;
            color: #1e293b;
            border-radius: 30px;
            padding: 6px 18px;
            font-size: 0.8rem;
            border: none;
        }
        /* Tabla moderna */
        .table {
            margin: 0;
            font-size: 0.85rem;
        }
        .table thead th {
            background: var(--azul-oscuro);
            color: white;
            font-weight: 600;
            padding: 12px 10px;
            font-size: 0.8rem;
            white-space: nowrap;
        }
        .table tbody tr:hover {
            background: #FEFCE8;
        }
        .table td {
            padding: 10px 8px;
            vertical-align: middle;
            border-bottom: 1px solid var(--gris-border);
        }
        /* Checkbox personalizado (opcional, se mantiene nativo) */
        input[type="checkbox"] {
            transform: scale(1.1);
            cursor: pointer;
        }
        .badge-enviado {
            background: var(--verde);
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-pendiente {
            background: var(--rojo);
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .uc-page-wrapper { margin: 0 10px 1rem; }
            .uc-card-header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
<div id="contenido">
    <div class="uc-page-wrapper">
        <div class="uc-card-header">
            <h5 class="uc-card-title">
                <i class="fas fa-file-alt"></i> Reporte Informes de Comisión
                <?php if ($anio != 0): ?>
                    <span class="badge badge-info" style="background: var(--azul-cielo);"><?= htmlspecialchars($anio) ?></span>
                <?php endif; ?>
            </h5>
            <div>
                <button type="button" class="btn btn-uc-secondary" onclick="window.location.href='report_terceros.php'">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
            </div>
        </div>
        <div class="p-3">
            <form id="formActualizar" method="post" action="actualizar_envio_rh.php">
                <div class="mb-3">
                    <button type="button" id="filtroINT" class="filter-btn"><i class="fas fa-map-marker-alt"></i> INT</button>
                    <button type="button" id="filtroEXT" class="filter-btn"><i class="fas fa-globe"></i> EXT</button>
                    <button type="button" id="filtroTodos" class="filter-btn"><i class="fas fa-list"></i> TODOS</button>
                </div>

                <div class="table-responsive">
                    <table id="tablaUsuarios" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Vigencia</th>
                                <th>EXT/INT</th>
                                <th>Documento</th>
                                <th>Nombre</th>
                                <th>No. Resolución</th>
                                <th>Fecha Informe</th>
                                <th>Folios</th>
                                <th>Envío RH</th>
                                <th>Seleccionar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($anio != 0) {
                                $sql = "SELECT ca.id, ca.tipo_estudio, ca.documento, t.nombre_completo, 
                                               ca.No_resolucion, ca.folios, ca.fecha_informe, ca.envio_rh, ca.vigencia
                                        FROM comision_academica ca
                                        JOIN tercero t ON ca.documento = t.documento_tercero
                                        WHERE ca.reintegrado = 1 AND ca.vigencia = $anio";
                            } else {
                                $sql = "SELECT ca.id, ca.tipo_estudio, ca.documento, t.nombre_completo, 
                                               ca.No_resolucion, ca.folios, ca.fecha_informe, ca.envio_rh, ca.vigencia
                                        FROM comision_academica ca
                                        JOIN tercero t ON ca.documento = t.documento_tercero
                                        WHERE ca.reintegrado = 1";
                            }
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["vigencia"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["tipo_estudio"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["documento"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nombre_completo"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["No_resolucion"]) . "</td>";
                                    echo "<td>" . ($row["fecha_informe"] ? date("d-m-Y", strtotime($row["fecha_informe"])) : '') . "</td>";
                                    echo "<td>" . htmlspecialchars($row["folios"]) . "</td>";
                                    if ($row["envio_rh"] == 1) {
                                        echo '<td><span class="badge-enviado"><i class="fas fa-check-circle"></i> Enviado</span></td>';
                                    } else {
                                        echo '<td><span class="badge-pendiente"><i class="fas fa-clock"></i> Pendiente</span></td>';
                                    }
                                    if ($row["envio_rh"] == 0) {
                                        echo '<td><input type="checkbox" class="seleccionable" name="seleccionados[]" value="' . $row["id"] . '"></td>';
                                    } else {
                                        echo '<td><input type="checkbox" disabled></td>';
                                    }
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>No hay resultados</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3 gap-2">
                    <button type="button" id="seleccionarTodos" class="btn btn-uc-secondary" data-checked="false">
                        <i class="fas fa-check-double"></i> Seleccionar Todos
                    </button>
                    <button type="submit" class="btn btn-uc-primary">
                        <i class="fas fa-paper-plane"></i> Envío RH
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#tablaUsuarios').DataTable({
        "order": [],
        "language": {
            "info": "Mostrando de _START_ a _END_ de <span style='color: var(--rojo); font-weight: bold;'>_TOTAL_</span> Registros",
            "infoFiltered": "(filtrados de _MAX_ registros totales)",
            "lengthMenu": "Mostrar _MENU_ registros",
            "search": "Buscar:",
            "paginate": {
                "previous": "Anterior",
                "next": "Siguiente"
            }
        },
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        "columnDefs": [
            { "orderable": false, "targets": [8] } // Deshabilitar orden en columna de checkbox
        ]
    });

    // Seleccionar/Deseleccionar todos los checkboxes visibles en la página actual
    $('#seleccionarTodos').click(function() {
        var isChecked = $(this).data('checked');
        $('input.seleccionable:visible').prop('checked', !isChecked);
        $(this).data('checked', !isChecked);
        $(this).html(isChecked ? '<i class="fas fa-check-double"></i> Seleccionar Todos' : '<i class="fas fa-times-circle"></i> Deseleccionar Todos');
    });

    // Filtros por tipo de comisión (columna índice 1)
    $('#filtroINT').click(function() {
        table.column(1).search('INT').draw();
    });
    $('#filtroEXT').click(function() {
        table.column(1).search('EXT').draw();
    });
    $('#filtroTodos').click(function() {
        table.column(1).search('').draw();
    });
});
</script>
</body>
</html>