<?php
// Realizar la conexión a la base de datos
require 'conn.php';
require('include/headerz.php');

// Actualizar estados vencidos (comisiones que ya terminaron)
$sqlu = "UPDATE comision_academica SET estado = 'finalizada' WHERE estado = 'Activa' AND vence < NOW()";
$conn->query($sqlu);

if(isset($_GET['id'])) {
    $doc = $_GET['id'];
} else {
    echo "No se proporcionó un ID válido.";
    exit;
}

$depto = $_GET['depto'] ?? '';
$nombre = $_GET['nombre'] ?? '';
$fk_depto = $_GET['kdepto'] ?? '';
$facultad = $_GET['facultad'] ?? '';

// Consulta datos del profesor
$sqlt = "SELECT * FROM tercero WHERE documento_tercero = '$doc'";
$resultt = mysqli_query($conn, $sqlt);
$rowt = mysqli_fetch_assoc($resultt);

// Consulta comisiones del profesor
$sql = "SELECT 
            comision_academica.id AS id,
            facultad.NOMBREC_FAC AS NOMBREC_FAC,
            deparmanentos.depto_nom_propio AS NOMBRE_DEPTO,
            tercero.nombre_completo AS nombre_completo,
            tercero.documento_tercero AS documento_tercero,
            comision_academica.No_resolucion, 
            comision_academica.fecha_resolucion,
            GROUP_CONCAT(destino.ciudad) AS ciudades_visitadas,
            comision_academica.reintegrado,
            comision_academica.folios,
            comision_academica.fecha_informe,
            comision_academica.tipo_estudio,
            comision_academica.fechaINI,
            comision_academica.vence,
            comision_academica.estado,
            comision_academica.evento,
            comision_academica.link_resolucion,
            comision_academica.notificado
        FROM 
            facultad
        JOIN deparmanentos ON facultad.PK_FAC = deparmanentos.FK_FAC
        JOIN tercero ON deparmanentos.PK_DEPTO = tercero.fk_depto
        JOIN comision_academica ON tercero.documento_tercero = comision_academica.documento
        LEFT JOIN destino ON comision_academica.id = destino.id_comision
        WHERE tercero.documento_tercero = '$doc'
        GROUP BY comision_academica.id
        ORDER BY comision_academica.vence DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profesor: <?php echo htmlspecialchars($rowt["documento_tercero"]." - ".$rowt["nombre_completo"]); ?></title>
    <!-- Bootstrap 4, DataTables, Font Awesome -->
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

        .uc-page-wrapper {
            background: white;
            border-radius: 28px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
            margin: 0 20px 2rem 20px;
            transition: all 0.2s;
        }

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

        .btn-uc-outline {
            border-radius: 40px;
            padding: 6px 16px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
            border: 1px solid var(--gris-border);
            background: white;
            color: var(--azul-oscuro);
        }
        .btn-uc-outline:hover {
            background: var(--azul-cielo);
            color: white;
            border-color: var(--azul-cielo);
        }

        .btn-uc-primary {
            background: var(--verde);
            color: white;
            border: none;
            border-radius: 40px;
            padding: 6px 20px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: 0.2s;
        }
        .btn-uc-primary:hover {
            background: #1a6e2c;
            transform: translateY(-2px);
        }

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
            font-size: 0.8rem;
            white-space: nowrap;
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
        .uc-table-scroll {
            overflow-x: auto;
            width: 100%;
        }

        .btn-icon-sm {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            font-size: 0.7rem;
            font-weight: 500;
            border-radius: 30px;
            border: none;
            transition: all 0.2s;
            background: #F1F5F9;
            color: #1E293B;
        }
        .btn-icon-sm i {
            font-size: 0.85rem;
        }
        .btn-icon-sm:hover {
            transform: translateY(-2px);
            filter: brightness(0.95);
        }
        .btn-word { background: #2B579A; color: white; }
        .btn-word:hover { background: #1e3e6e; color: white; }
        .btn-edit { background: #28a745; color: white; }
        .btn-edit:hover { background: #1e7e34; color: white; }
        .btn-informe { background: #fd7e14; color: white; }
        .btn-informe:hover { background: #e36209; color: white; }
        .btn-encargo { background: #17a2b8; color: white; }
        .btn-encargo:hover { background: #138496; color: white; }
        .btn-email { background: #D93025; color: white; }
        .btn-email:hover { background: #b11f1a; color: white; }

        /* Badges para estados de la comisión */
        .badge-estado {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge-activa { background: #d4edda; color: #155724; }
        .badge-proxima { background: #fff3cd; color: #856404; }
        .badge-finalizada { background: #cfe2ff; color: #004085; }
        .badge-anulada { background: #f8d7da; color: #721c24; }
        
        /* Badges para Legalización */
        .badge-pendiente { background: #f8d7da; color: #721c24; }
        .badge-ok { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
<div id="contenido">
    <div class="uc-page-wrapper">
        <div class="uc-card-header">
            <h5 class="uc-card-title">
                <i class="fas fa-chalkboard-user"></i>
                <?php echo htmlspecialchars($rowt["documento_tercero"]." - ".$rowt["nombre_completo"]); ?>
            </h5>
            <div>
                <button type="button" class="btn btn-uc-outline" onclick="window.location.href='report_terceros.php'">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
                <a href="solicitud_formacion.php?id=<?php echo urlencode($rowt['documento_tercero']); ?>" class="btn btn-uc-primary">
                    <i class="fas fa-plus-circle"></i> Crear comisión
                </a>
            </div>
        </div>
        <div class="uc-table-scroll">
            <table id="tablaComisionados" class="table table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Resolución</th>
                        <th>Evento / Fechas</th>
                        <th>Estado</th>
                        <th>Legaliz</th>
                        <th>Ciudad(es)</th>
                        <th>Resol.</th>
                        <th>Editar</th>
                        <th>Notif.</th>
                        <th>Informe</th>
                        <th>Encargo</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $fecha_hoy = date('Y-m-d');
                while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['id'];
                    $tipo = $row['tipo_estudio'];
                    $enlace = !empty($row["link_resolucion"]) ? htmlspecialchars($row["link_resolucion"]) : '';
                    $texto_res = htmlspecialchars($row["No_resolucion"]);
                    
                    $fechaIni = date("d-m-Y", strtotime($row["fechaINI"]));
                    $vence = date("d-m-Y", strtotime($row["vence"]));
                    $evento_resumido = substr($row["evento"], 0, 50);
                    $title_evento = htmlspecialchars($row["evento"]);
                    
                    $linkWord = ($tipo == 'EXT') ? "resolucion_doc_ext_b.php?id={$id}" : "resolucion_docc.php?id={$id}";
                    $linkEdit = "actualizar_formacion.php?id={$id}";
                    
                    // Legalización (reintegrado)
                    $legaliz = ($row["reintegrado"] != '1' && $row["estado"] != "anulada") ? "PENDIENTE" : "OK";
                    $legalizBadge = ($legaliz == "PENDIENTE") 
                        ? '<span class="badge-estado badge-pendiente">PENDIENTE</span>' 
                        : '<span class="badge-estado badge-ok">OK</span>';
                    
                    // --- CÁLCULO DEL ESTADO VISUAL ---
                    $estado_db = $row["estado"];
                    $fecha_ini_db = $row["fechaINI"];
                    $estado_visual = "";
                    $badge_class = "";
                    
                    if ($estado_db == "Activa") {
                        if ($fecha_ini_db > $fecha_hoy) {
                            $estado_visual = "Próxima";
                            $badge_class = "badge-proxima";
                        } else {
                            $estado_visual = "Activa";
                            $badge_class = "badge-activa";
                        }
                    } elseif ($estado_db == "finalizada") {
                        $estado_visual = "Finalizada";
                        $badge_class = "badge-finalizada";
                    } elseif ($estado_db == "anulada") {
                        $estado_visual = "Anulada";
                        $badge_class = "badge-anulada";
                    } else {
                        $estado_visual = ucfirst($estado_db);
                        $badge_class = "badge-activa";
                    }
                    
                    // Notificación (solo para INT y Activa visual)
                    $notifHtml = '';
                    if ($row["tipo_estudio"] === "INT" && $estado_db === "Activa") {
                        if ($row["notificado"] == 1) {
                            $notifHtml = '<span class="badge-estado badge-ok">OK</span>';
                        } else {
                            if (empty($row["link_resolucion"])) {
                                $notifHtml = '<button class="btn-icon-sm btn-email" onclick="alert(\'No se ha incluido un link del PDF de la resolución\')"><i class="fas fa-envelope"></i> Notif.</button>';
                            } else {
                                $notifHtml = '<a href="comunicar_comision_int.php?id='.$id.'" class="btn-icon-sm btn-email" onclick="return confirmEmail();"><i class="fas fa-envelope"></i> Notif.</a>';
                            }
                        }
                    } else {
                        $notifHtml = '--';
                    }
                    
                    // Consulta datos de encargo
                    $query_encrg = "SELECT cargo_academico_admin, cc_encargado, oficio_encargo FROM comision_academica WHERE id = {$id}";
                    $result_encrg = mysqli_query($conn, $query_encrg);
                    $cargo = null;
                    $cedula = null;
                    $oficio = null;
                    if ($result_encrg && mysqli_num_rows($result_encrg) > 0) {
                        $row_encrg = mysqli_fetch_assoc($result_encrg);
                        $cargo = $row_encrg['cargo_academico_admin'];
                        $cedula = $row_encrg['cc_encargado'];
                        $oficio = $row_encrg['oficio_encargo'];
                    }
                    
                    echo "<tr>";
                    echo "<td>{$tipo}</td>";
                    // Resolución con enlace
                    if ($enlace) {
                        echo "<td><a href='{$enlace}' target='_blank' style='color:var(--azul-rey);'>{$texto_res}</a></td>";
                    } else {
                        echo "<td>{$texto_res}</td>";
                    }
                    echo "<td title='{$title_evento}'>{$evento_resumido}... {$fechaIni} - {$vence}</td>";
                    echo "<td><span class='badge-estado {$badge_class}'>{$estado_visual}</span></td>";
                    echo "<td>{$legalizBadge}</td>";
                    echo "<td>" . htmlspecialchars($row["ciudades_visitadas"]) . "</td>";
                    // Botón word resolución
                    echo "<td><button class='btn-icon-sm btn-word' onclick=\"window.location.href='{$linkWord}'\"><i class='fas fa-file-word'></i> Word</button></td>";
                    // Editar
                    echo "<td><button class='btn-icon-sm btn-edit' onclick=\"window.location.href='{$linkEdit}'\"><i class='fas fa-edit'></i> Editar</button></td>";
                    // Notificación
                    echo "<td>{$notifHtml}</td>";
                    // Informe
                    echo "<td><button class='btn-icon-sm btn-informe' data-toggle='modal' data-target='#informeModal' data-id='{$id}'><i class='fas fa-file-signature'></i> Informe</button></td>";
                    // Encargo
                    echo "<td>";
                    if ($estado_db == "Activa") {
                        echo "<button class='btn-icon-sm btn-encargo' onclick='solicitarEncargo({$id})'><i class='fas fa-file-signature'></i> Solicitar</button>";
                    }
                    if (!is_null($cargo)) {
                        echo "<button class='btn-icon-sm btn-encargo mt-1' onclick=\"window.location.href='oficio_encargo.php?id={$id}&cargo={$cargo}&cedula={$cedula}&oficio={$oficio}'\"><i class='fas fa-redo-alt'></i> Reimpr.</button>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de informe -->
<div class="modal fade" id="informeModal" tabindex="-1" role="dialog" aria-labelledby="informeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Informe de Comisión</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" name="comision_id" id="comision_id">
                    <div class="form-group">
                        <label>Fecha Informe:</label>
                        <input type="date" class="form-control" id="fecha_informe" required>
                    </div>
                    <div class="form-group">
                        <label>Folios:</label>
                        <input type="text" class="form-control" id="folios">
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

<script>
function solicitarEncargo(id) {
    var cargo = prompt('Cargo:');
    if (!cargo) { alert('Debe ingresar el cargo.'); return; }
    var cedula = prompt('C.C. del profesor encargado:');
    if (!cedula) { alert('Debe ingresar la cédula.'); return; }
    var oficio = prompt('Oficio o formato anexo (ej. PM-FO-4-FOR – 20 del 20 de septiembre de 2024):');
    if (!oficio) { alert('Debe ingresar el oficio.'); return; }
    window.location.href = 'oficio_encargo.php?id=' + id + '&cargo=' + encodeURIComponent(cargo) + '&cedula=' + encodeURIComponent(cedula) + '&oficio=' + encodeURIComponent(oficio);
}

function confirmEmail() {
    return confirm('¿Está seguro de que desea enviar este correo electrónico? (recuerde cargar el link de la resolución en drive)');
}

$(document).ready(function() {
    var table = $('#tablaComisionados').DataTable({
        "paging": true,
        "searching": false,
        "info": false,
        "autoWidth": false
    });

    window.addEventListener('resize', function() {
        if (table) table.columns.adjust().draw();
    });

    $('#informeModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var comisionId = button.data('id');
        var modal = $(this);
        modal.find('#comision_id').val(comisionId);
        $.ajax({
            url: 'obtener_datos_informe.php',
            type: 'POST',
            data: { comision_id: comisionId },
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    if (!data.error) {
                        modal.find('#fecha_informe').val(data.fecha_informe_formateada);
                        modal.find('#folios').val(data.folios);
                    }
                } catch(e) { console.error(e); }
            }
        });
    });

    $('#guardarCambiosBtn').click(function() {
        var comisionId = $('#comision_id').val();
        var fechaInforme = $('#fecha_informe').val();
        var folios = $('#folios').val();
        $.ajax({
            url: 'actualizar_solicitud_informe_modal.php',
            type: 'POST',
            data: {
                comision_id: comisionId,
                fecha_informe: fechaInforme,
                folios: folios
            },
            success: function() {
                $('#informeModal').modal('hide');
                location.reload();
            }
        });
    });
});
</script>
</body>
</html>