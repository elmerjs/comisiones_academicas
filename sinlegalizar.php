<?php
require('include/headerz.php');

// Conexión a la base de datos usando PDO (ajusta según tu configuración)
$host = 'localhost';
$dbname = 'comisiones_academicas';
$username = 'root';
$password = '';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Consulta SQL para comisiones finalizadas sin informe legalizado
$query = "SELECT 
    ca.id, 
    ca.No_resolucion, 
    ca.fecha_resolucion, 
    ca.documento, 
    t.nombre_completo, 
    ca.tipo_estudio, 
    ca.organizado_por, 
    ca.ciudad_pais, 
    ca.evento, 
    ca.fechaINI, 
    ca.vence, 
    ca.vigencia, 
    ca.periodo, 
    COUNT(n.id_notificar) AS num_notificaciones
FROM 
    comision_academica ca
JOIN 
    tercero t ON t.documento_tercero = ca.documento
LEFT JOIN 
    notificar_informe_pend n ON n.fk_notificar_id_comision = ca.id
WHERE 
    (ca.reintegrado <> 1 OR ca.reintegrado IS NULL)
    AND ca.estado = 'finalizada'
GROUP BY 
    ca.id
ORDER BY 
    ca.vigencia, ca.id";
$stmt = $conn->prepare($query);
$stmt->execute();
$comisiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comisiones Finalizadas · Unicauca</title>
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
            --rojo: #E52724;
            --gris-border: #E9EEF3;
            --shadow-card: 0 12px 28px rgba(0,0,0,0.05);
        }
        body {
            background: #F1F5F9;
            font-family: 'Segoe UI', system-ui;
        }
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
            padding: 6px 20px;
            font-size: 0.8rem;
            font-weight: 600;
            border: none;
        }
        .btn-uc-primary:disabled {
            background: #a0aec0;
            cursor: not-allowed;
        }
        .btn-uc-secondary {
            background: #e9ecef;
            color: #1e293b;
            border-radius: 30px;
            padding: 6px 18px;
            font-size: 0.8rem;
            border: none;
        }
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
        .badge-notif {
            background: var(--azul-cielo);
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .btn-email {
            background: var(--verde);
            color: white;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .btn-email:hover {
            background: #1a6e2c;
            color: white;
            text-decoration: none;
        }
        input[type="checkbox"] {
            transform: scale(1.1);
            cursor: pointer;
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
                <i class="fas fa-envelope-open-text"></i> Comisiones Finalizadas sin Informe
            </h5>
            <button type="button" class="btn btn-uc-secondary" onclick="window.location.href='report_terceros.php'">
                <i class="fas fa-arrow-left"></i> Volver
            </button>
        </div>
        <div class="p-3">
            <form action="email_masivo_i.php" method="POST" id="comisionesForm" onsubmit="return confirmSubmission()">
                <div class="mb-3">
                    <button type="button" id="filterINT" class="filter-btn"><i class="fas fa-map-marker-alt"></i> INT</button>
                    <button type="button" id="filterEXT" class="filter-btn"><i class="fas fa-globe"></i> EXT</button>
                    <button type="button" id="clearFilter" class="filter-btn"><i class="fas fa-list"></i> Limpiar</button>
                </div>

                <div class="table-responsive">
                    <table id="comisionesTable" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>ID</th>
                                <th>No. Resolución</th>
                                <th>Documento</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Destino</th>
                                <th>Evento</th>
                                <th>Fechas</th>
                                <th>Vigencia/Per</th>
                                <th>Notif.</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comisiones as $comision): ?>
                            <tr>
                                <td><input type="checkbox" name="comisiones[]" value="<?= htmlspecialchars($comision['id']) ?>" class="selectRow"></td>
                                <td><?= htmlspecialchars($comision['id']) ?></td>
                                <td title="<?= htmlspecialchars($comision['No_resolucion']) . ' - ' . htmlspecialchars($comision['fecha_resolucion']) ?>">
                                    <?= htmlspecialchars(substr($comision['No_resolucion'], 0, 17)) ?>
                                </td>
                                <td><?= htmlspecialchars($comision['documento']) ?></td>
                                <td title="<?= htmlspecialchars($comision['nombre_completo']) ?>">
                                    <?= htmlspecialchars(mb_strimwidth($comision['nombre_completo'], 0, 20, '...')) ?>
                                </td>
                                <td><?= htmlspecialchars($comision['tipo_estudio']) ?></td>
                                <td title="<?= htmlspecialchars($comision['ciudad_pais']) ?>">
                                    <?= htmlspecialchars(mb_strimwidth($comision['ciudad_pais'], 0, 15, '...')) ?>
                                </td>
                                <td title="<?= htmlspecialchars($comision['evento']) ?>">
                                    <?= htmlspecialchars(mb_substr($comision['evento'], 0, 20)) ?><?= strlen($comision['evento']) > 20 ? '...' : '' ?>
                                </td>
                                <td title="<?= htmlspecialchars($comision['fechaINI'] . ' / ' . $comision['vence']) ?>">
                                    <?= htmlspecialchars($comision['fechaINI']) ?>
                                </td>
                                <td><?= htmlspecialchars($comision['vigencia']) ?>/<?= htmlspecialchars($comision['periodo']) ?></td>
                                <td><span class="badge-notif"><?= htmlspecialchars($comision['num_notificaciones']) ?></span></td>
                                <td>
                                    <a href="enviar_email.php?id=<?= htmlspecialchars($comision['id']) ?>" class="btn-email" onclick="return confirmEmail();">
                                        <i class="fas fa-envelope"></i> Email
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-uc-primary" id="submitBtn" disabled>
                        <i class="fas fa-paper-plane"></i> Enviar emails seleccionados
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#comisionesTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "language": {
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "lengthMenu": "Mostrar _MENU_ registros",
            "search": "Buscar:",
            "paginate": {
                "previous": "Anterior",
                "next": "Siguiente"
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 11] } // Desactivar orden en checkbox y acciones
        ]
    });

    // Filtros por tipo_estudio (columna índice 5)
    $('#filterINT').click(function() {
        table.column(5).search('^INT$', true, false).draw();
    });
    $('#filterEXT').click(function() {
        table.column(5).search('^EXT$', true, false).draw();
    });
    $('#clearFilter').click(function() {
        table.column(5).search('').draw();
    });

    // Seleccionar todos los checkboxes visibles en la página actual
    $('#selectAll').click(function() {
        var isChecked = this.checked;
        $('.selectRow:visible').each(function() {
            this.checked = isChecked;
        });
        toggleSubmitButton();
    });

    // Habilitar/deshabilitar botón según selección
    $(document).on('click', '.selectRow', function() {
        toggleSubmitButton();
    });

    function toggleSubmitButton() {
        var selectedCount = $('.selectRow:visible:checked').length;
        $('#submitBtn').prop('disabled', selectedCount === 0);
    }

    // Confirmación de envío masivo
    window.confirmSubmission = function() {
        var selectedCount = $('.selectRow:checked').length;
        if (selectedCount > 0) {
            return confirm(`¿Está seguro de que desea enviar correos electrónicos para las ${selectedCount} comisiones seleccionadas?`);
        }
        return false;
    };

    window.confirmEmail = function() {
        return confirm('¿Está seguro de que desea enviar este correo electrónico?');
    };
});
</script>
</body>
</html>