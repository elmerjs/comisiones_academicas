<?php
require 'conn.php';
require('include/headerz.php');
$usuario = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profesores · Unicauca</title>
    <!-- Bootstrap 4, DataTables y Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ===== INSTITUTIONAL VARIABLES (igual que comisionesb) ===== */
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

        /* Contenedor principal de la tabla (card) */
        .uc-page-wrapper {
            background: white;
            border-radius: 28px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
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

        /* Botón de crear profesor (estilo coherente) */
        .btn-uc-crear {
            background: var(--verde);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .btn-uc-crear:hover {
            background: #1a6e2c;
            transform: translateY(-2px);
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

        /* Enlaces dentro de la tabla */
        .table a {
            color: var(--azul-rey);
            text-decoration: none;
            font-weight: 500;
        }
        .table a:hover {
            text-decoration: underline;
        }

        /* Botones de acción (iconos) */
        .btn-icon-link {
            background: transparent;
            border: none;
            padding: 0;
            margin: 0 4px;
            transition: transform 0.1s;
        }
        .btn-icon-link:hover {
            transform: scale(1.1);
        }

        /* Alerta para sabático */
        .alerta {
            color: var(--rojo);
            font-weight: bold;
            cursor: help;
        }

        /* Badges estilizados (como en indexprof.php) */
        .badge-estado {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-activo { background: #d4edda; color: #155724; }
        .badge-inactivo { background: #f8d7da; color: #721c24; }
        .badge-cargo { background: #e9ecef; color: #495057; }
        .badge-cargo-admin { background: #1a237e; color: white; }
        .badge-pendiente { background: #f8d7da; color: #721c24; }
        .badge-ok { background: #d4edda; color: #155724; }

        /* Modal (se mantiene el estilo original pero se adapta al nuevo diseño) */
        .modal-header {
            background-color: var(--verde);
            color: white;
            text-align: center;
        }
        .modal-header .close {
            color: white;
            opacity: 1;
        }
        .modal-body {
            padding: 30px;
        }
    </style>
</head>
<body>
<div id="contenido">
    <div class="uc-page-wrapper">
        <div class="uc-card-header">
            <h5 class="uc-card-title">
                <i class="fas fa-chalkboard-user"></i> Consultas por Profesores
            </h5>
            <button id="openModalBtnb" class="btn btn-uc-crear">
                <i class="fas fa-plus"></i> Crear Profesor
            </button>
        </div>
        <div class="uc-table-scroll">
            <table id="tablaUsuarios" class="table table-hover">
                <thead>
                    <tr>
                        <th>Doc.</th>
                        <th>Nombre</th>
                        <th>Vincul.</th>
                        <th>Estado</th>
                        <th>Depto</th>
                        <th>Cargo Admin</th>
                        <th>Pendiente</th>
                        <th title="histórico de Comisiones Académicas">Formación</th>
                        <th title="Crear Comisión o sabático">Crear Comisión</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Consulta SQL con la lógica original (sin resta)
                    $sql = "SELECT
                                t.id_tercero AS id,
                                t.documento_tercero AS Documento,
                                t.nombre_completo AS Nombre,
                                LOWER(t.vincul) AS Vinculación,
                                t.estado AS Estado,
                                t.email,
                                t.fk_depto AS fk_depto,
                                t.escalafon,
                                t.fecha_ingreso,
                                facultad.NOMBREC_FAC AS facultad,
                                d.NOMBRE_DEPTO_CORT AS Nombre_Departamento,
                                t.cargo_admin AS cargo,
                                COUNT(c.evento) AS comisiones_pend,
                                CONCAT(cs.estado, ' - ', cs.tipo_estudio, ' - ', cs.fechaINI, ' - ', cs.vence) AS sabatico
                            FROM
                                tercero t
                            LEFT JOIN
                                deparmanentos d ON t.fk_depto = d.PK_DEPTO
                            LEFT JOIN 
                                facultad ON d.FK_FAC = facultad.PK_FAC
                            LEFT JOIN
                                comision_academica c ON (t.documento_tercero = c.documento AND ((c.reintegrado = 0 OR c.reintegrado IS NULL) AND c.estado <> 'anulada'))
                            LEFT JOIN 
                                comisiones_sabaticos.comisionado cs ON cs.documento = t.documento_tercero 
                                AND cs.estado = 'ACTIVO' 
                                AND CURDATE() BETWEEN cs.fechaINI AND cs.vence
                            GROUP BY
                                t.documento_tercero  
                            ORDER BY t.nombre_completo";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row["id"];
                            $cargo = $row["cargo"];
                            $estado_prof = $row["Estado"];

                            $nombreClass = !is_null($row['sabatico']) ? 'alerta' : '';
                            $nombreTitle = !is_null($row['sabatico']) ? htmlspecialchars($row['sabatico']) : '';

                            // Badge para Estado del profesor
                            $estadoProfBadge = ($estado_prof == 'ac') 
                                ? '<span class="badge-estado badge-activo">Activo</span>' 
                                : '<span class="badge-estado badge-inactivo">Inactivo</span>';

                            // Badge para Cargo Admin
                            $cargoBadge = '';
                            if (empty($cargo) || $cargo == 'No aplica') {
                                $cargoBadge = '<span class="badge-estado badge-cargo">—</span>';
                            } else {
                                $cargoBadge = '<span class="badge-estado badge-cargo-admin">' . htmlspecialchars($cargo) . '</span>';
                            }

                            // Badge para Pendiente
                            $pendienteBadge = '';
                            if ($row["comisiones_pend"] > 0) {
                                $pendienteBadge = '<span class="badge-estado badge-pendiente">' . $row["comisiones_pend"] . ' Comisión(es) Pendientes</span>';
                            } else {
                                $pendienteBadge = '<span class="badge-estado badge-ok">Ok</span>';
                            }

                            echo "<tr>";
                            echo '<td><a href="actualizatercero.php?id=' . $id . '" title="Actualizar tercero">' . $row["Documento"] . '</a></td>';
                            echo '<td><span class="' . $nombreClass . '" title="' . $nombreTitle . '">' . htmlspecialchars($row["Nombre"]) . '</span></td>';
                            echo "<td>" . $row["Vinculación"] . "</td>";
                            echo "<td>" . $estadoProfBadge . "</td>";
                            echo "<td>" . ucfirst(strtolower($row["Nombre_Departamento"])) . " - " . ucfirst(strtolower($row["facultad"])) . "</td>";
                            echo "<td>" . $cargoBadge . "</td>";
                            echo "<td>" . $pendienteBadge . "</td>";
                            
                            // Botón histórico
                            echo "<td class='text-center'>
                                    <a href='indexprof.php?id=" . $row["Documento"] . "&nombre=" . urlencode($row["Nombre"]) . "&depto=" . urlencode($row["Nombre_Departamento"]) . "&cargo=" . urlencode($cargo) . "&kdepto=" . $row["fk_depto"] . "&facultad=" . urlencode($row["facultad"]) . "' class='btn-icon-link' title='Histórico de Comisiones'>
                                        <i class='fas fa-list' style='color: var(--azul-rey); font-size: 22px;'></i>
                                    </a>
                                   </a>
                                  </td>";
                            // Botón nueva comisión
                            echo "<td class='text-center'>
                                    <a href='solicitud_formacion.php?id=" . $row["Documento"] . "' class='btn-icon-link' title='Crear Comisión'>
                                        <i class='fas fa-plus-circle' style='color: var(--verde); font-size: 22px;'></i>
                                    </a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No hay resultados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once('modalnuevoprofesor.php'); ?>

<script>
    // Abrir modal de nuevo profesor usando Bootstrap
    $(document).on('click', '#openModalBtnb', function() {
        $('#myModalb').modal('show');
    });

    // Inicializar DataTable con paginación y menú de registros
    $(document).ready(function() {
        var table = $('#tablaUsuarios').DataTable({
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            "search": { "smart": true },
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
            "order": [],
            "autoWidth": false
        });

        // Ajustar columnas cuando se oculta/muestra el menú lateral
        window.addEventListener('resize', function() {
            if (table) table.columns.adjust().draw();
        });
    });
</script>
</body>
</html>