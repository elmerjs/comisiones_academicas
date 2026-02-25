<?php
require('include/headerz.php');

// Conexión a la base de datos (ajusta según tu configuración)
$host = 'localhost';
$dbname = 'comisiones_academicas';
$username = 'root';
$password = '';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Consulta SQL
$query = "SELECT 
    comision_academica.id, 
    comision_academica.No_resolucion, 
    comision_academica.fecha_resolucion, 
    comision_academica.documento, 
    tercero.nombre_completo, 
    comision_academica.tipo_estudio, 
    comision_academica.organizado_por, 
    comision_academica.ciudad_pais, 
    comision_academica.evento, 
    comision_academica.fechaINI, 
    comision_academica.vence, 
    comision_academica.vigencia, 
    comision_academica.periodo, 
    COUNT(notificar_informe_pend.id_notificar) AS num_notificaciones
FROM 
    comision_academica
JOIN 
    tercero ON tercero.documento_tercero = comision_academica.documento
LEFT JOIN 
    notificar_informe_pend ON notificar_informe_pend.fk_notificar_id_comision = comision_academica.id
WHERE 
    (reintegrado <> 1  OR reintegrado is null)
    AND comision_academica.estado = 'finalizada' 
  /*  AND vence < CURDATE() - INTERVAL 1 MONTH*/
GROUP BY 
    comision_academica.id
order by vigencia, id";
$stmt = $conn->prepare($query);
$stmt->execute();

// Obtener los resultados
$comisiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comisiones Académicas</title>

    <!-- Estilos de Bootstrap y DataTables -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <!-- jQuery y DataTables Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <style>
   body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }

        .container {
            margin-top: 30px;
            background: #ffffff;
            padding: 25px; /* Incrementado para mayor separación */
            border-radius: 15px; /* Bordes más redondeados */
            border: 1px solid #dee2e6; /* Borde suave */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); /* Más profundidad */
        }

        h4 {
            color: #495057;
            font-weight: bold;
            border-bottom: 2px solid #6c757d;
            padding-bottom: 10px;
        }

        table {
            font-size: 0.9rem;
        }

        table thead {
            background-color: #343a40;
            color: #ffffff;
        }

        table tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        table tbody tr:hover {
            background-color: #e9ecef;
        }

        .btn-sm {
            font-size: 0.8rem;
            padding: 5px 10px;
            margin-right: 5px;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .btn-enviar-email {
            display: inline-block;
            padding: 4px 16px;
            background-color: #4CAF50; /* Verde, puedes cambiarlo */
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none; /* Para eliminar el subrayado del enlace */
            font-weight: bold;
        }

        .btn-enviar-email:hover {
            background-color: #45a049; /* Cambio de color al pasar el mouse */
        }    </style>
</head>
<body>

<div class="container">
    <h4 class="mb-4">Comisiones Académicas Finalizadas</h4>
<form action="email_masivo_i.php" method="POST" id="comisionesForm" onsubmit="return confirmSubmission()">
    <div class="my-3">
    <button type="button" id="filterINT" class="btn btn-secondary">Filtrar INT</button>
    <button type="button" id="filterEXT" class="btn btn-secondary">Filtrar EXT</button>
    <button type="button" id="clearFilter" class="btn btn-secondary">Limpiar Filtro</button>
</div>
        <div class="table-responsive">
            <!-- Tabla de DataTable -->
            <table id="comisionesTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll" /></th> <!-- Columna de checkboxes -->
                        <th>ID</th>
                        <th>No. Resolución</th>
                        <th>Documento</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Destino</th>
                        <th>Evento</th>
                        <th>Fechas</th>
                        <th>Vigencia</th>
                        <th>Emails</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comisiones as $comision): ?>
                        <tr>
                            <td><input type="checkbox" name="comisiones[]" value="<?= htmlspecialchars($comision['id']) ?>" class="selectRow" /></td>
                            <td><?= htmlspecialchars($comision['id']) ?></td>
                            <td title="<?= htmlspecialchars($comision['No_resolucion']) . ' - ' . htmlspecialchars($comision['fecha_resolucion']) ?>">
                                <?= htmlspecialchars(substr($comision['No_resolucion'], 0, 17)) ?>
                            </td>
                            <td><?= htmlspecialchars($comision['documento']) ?></td>
                            <td>
                                <span title="<?= htmlspecialchars($comision['nombre_completo']) ?>">
                                    <?= htmlspecialchars(mb_strimwidth($comision['nombre_completo'], 0, 15, '...')) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($comision['tipo_estudio']) ?></td>
                            <td>
                                <span title="<?= htmlspecialchars($comision['ciudad_pais']) ?>">
                                    <?= htmlspecialchars(mb_strimwidth($comision['ciudad_pais'], 0, 12, '...')) ?>
                                </span>
                            </td>
                            <td>
                                <span title="<?= htmlspecialchars($comision['evento']) ?>">
                                    <?= htmlspecialchars(mb_substr($comision['evento'], 0, 14)) ?><?= strlen($comision['evento']) > 14 ? '...' : '' ?>
                                </span>
                            </td>
                            <td>
                                <span title="<?= htmlspecialchars($comision['fechaINI'] . ' / ' . $comision['vence']) ?>">
                                    <?= htmlspecialchars($comision['fechaINI']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($comision['vigencia']) ?> / <?= htmlspecialchars($comision['periodo']) ?></td>
                            <td><?= htmlspecialchars($comision['num_notificaciones']) ?></td>
                           <td> <a href="enviar_email.php?id=<?= htmlspecialchars($comision['id']) ?>" class="btn-enviar-email" onclick="return confirmEmail();">Email</a> </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Botón para enviar las comisiones seleccionadas -->
        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Enviar emails</button>
    </form>

</div>

<!-- Script para inicializar DataTable y manejar eliminación -->
<script>
       $(document).ready(function() {
            // Inicializa DataTable
            var table = $('#comisionesTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });

            // Filtrar por INT
            $('#filterINT').click(function() {
                table.column(5).search('^INT$', true, false).draw(); // Filtra por 'INT' en la columna Tipo
            });

            // Filtrar por EXT
            $('#filterEXT').click(function() {
                table.column(5).search('^EXT$', true, false).draw(); // Filtra por 'EXT' en la columna Tipo
            });

            // Limpiar filtro
            $('#clearFilter').click(function() {
                table.column(5).search('').draw(); // Elimina el filtro de la columna Tipo
            }); 

        // Seleccionar todos los checkboxes
        $('#selectAll').click(function() {
            var isChecked = this.checked;
            $('.selectRow').each(function() {
                this.checked = isChecked;
            });
            toggleSubmitButton(); // Activa/desactiva el botón de envío
        });

        // Habilitar/deshabilitar botón de envío
        $('.selectRow').click(function() {
            toggleSubmitButton();
        });

        function toggleSubmitButton() {
            const selectedCount = $('.selectRow:checked').length;
            $('#submitBtn').prop('disabled', selectedCount === 0); // Habilitar si hay al menos un checkbox seleccionado
        }
         // Función para confirmar antes de enviar
   
        
        
    });
     function confirmSubmission() {
        const selectedCount = $('.selectRow:checked').length;
        if (selectedCount > 0) {
            return confirm(`¿Está seguro de que desea enviar correos electrónicos para las ${selectedCount} comisiones seleccionadas?`);
        }
        return false; // Por si acaso alguien intenta enviar sin seleccionar.
    }
    function confirmEmail() { return confirm('¿Está seguro de que desea enviar este correo electrónico?'); }
    
    
</script>

</body>
</html>
