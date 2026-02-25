<?php
require 'conn.php';
require('include/header.php');
$usuario = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados</title>
    <!-- Estilos de DataTables con Bootstrap -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <!-- Bootstrap CSS -->
    <!-- Font Awesome -->
    <script defer src="https://use.fontawesome.com/releases/v5.5.0/js/all.js" integrity="sha384-GqVMZRt5Gn7tB9D9q7ONtcp4gtHIUEW/yG7h98J7IpE3kpi+srfFyyB/04OV6pG0" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- Custom CSS -->
    <style>
        .dataTables_wrapper .dataTables_info {
            font-size: 1.1em;
            color: #333;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5em 1em;
            margin-left: 2px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            color: #fff !important;
            border: 1px solid #337ab7;
            background-color: #337ab7;
            background: -webkit-linear-gradient(top, #337ab7 0%, #265a88 100%);
            background: -moz-linear-gradient(top, #337ab7 0%, #265a88 100%);
            background: -ms-linear-gradient(top, #337ab7 0%, #265a88 100%);
            background: -o-linear-gradient(top, #337ab7 0%, #265a88 100%);
            background: linear-gradient(to bottom, #337ab7 0%, #265a88 100%);
        }
        table.dataTable {
            width: 100% !important;
        }
        table.dataTable th, table.dataTable td {
            white-space: nowrap;
            text-align: left;
            vertical-align: middle;
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        table.dataTable thead th {
            background-color: #f1f1f1;
            color: #333;
            font-weight: bold;
        }
        table.dataTable tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table.dataTable tbody tr:hover {
            background-color: #f1f1f1;
        }
         /* Ajustar el ancho de las columnas */
    /*th:nth-child(15), td:nth-child(15), /* nombre_trabajo */
    /*th:nth-child(24), td:nth-child(24), /* observacion */
    /*th:nth-child(10), td:nth-child(10), /* evento */
    /*th:nth-child(11), td:nth-child(11)  /* organizado_por */ {
      /*  max-width: 150px; /* Cambia el valor a la medida de tu preferencia */
    /*    overflow: hidden;
      /*  text-overflow: ellipsis;
    }
    </style>
</head>
<body>
    <br><br>
    <div id="contenido">
            <div class="title-container">
                <h3 class="title-text">Comisiones Académicas</h3>
            </div>
            <br><br>
            <div class="table-responsive">
                <table id="tablaUsuarios" class="table table-sm table-bordered table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Doc</th>
                            <th>Periodo</th>
                            <th>INT/EXT</th>
                           <!-- <th>vinculacionr</th> -->
                            <th>facultad</th>
                            <th>Depto</th>
                        <!--    <th>fecha_aval</th> -->
<th style="max-width: 3cm;">Evento</th>
                          <!--  <th>organizado_por</th> -->
                            <th>Destino</th>
                          <!--  <th>paises_concat</th>
                            <th>tipo_participacion</th>
                            <th>nombre_trabajo</th>-->
                            <th>Fechas</th>
                            <!-- <th>duracion_horas</th>
                            <th>email_fac</th>
                            <th>email_tercero</th>-->
                            <th>#Res</th>
                            <th>legalizó</th>
                          <!--   <th>fecha_informe</th>
                            <th>folios</th>
                            <th>observacion</th>--> 
                            <th>tramito</th>
                            <th>Doc</th>
                            <th title="Editar">Edit</th>
                            <th title="Editar">Informe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
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
                                    END AS fecha_formateada
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
                                    paises_concat DESC;";
                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $row["id_comision"] . "</td>";
      
      echo "<td>" . substr($row["nombre_completo"], 0, 20) . "</td>";

                                echo "<td>" . $row["documento_profesor"] . "</td>";
                                echo "<td>" . $row["periodo_academico"] . "</td>";
                                echo "<td>" . $row["tipo_estudio"] . "</td>";
                                echo "<td>" . $row["nombre_fac_min"] . "</td>";
                                echo "<td>" . substr($row["depto_nom_propio"], 0, 15) . "</td>";

echo "<td>" . substr($row["evento"], 0, 30) . "</td>";
                                      echo "<td>" . substr($row["ciudades_concat"], 0, 10) . "</td>";
                                      echo "<td>" . substr($row["fecha_formateada"], 0, 20) . "</td>";
                                echo "<td>" . substr($row["No_resolucion"], 0, 20) . "</td>";

echo "<td>" . ($row["reintegrado"] == 1 ? '✓' : '✗') . "</td>";
                              
                            echo "<td>" . substr($row["tramito"], 0, 6) . "</td>";
    
                                // DocREs button
                                $tipo_estudio = $row['tipo_estudio'];
                                $id_comision = $row['id_comision'];
                                if ($tipo_estudio == 'EXT') {
                                    $link = "resolucion_doc_ext.php?id={$id_comision}";
                                } else {
                                    $link = "resolucion_docb.php?id={$id_comision}";
                                }
                                $icon = '<i class="far fa-file-word"></i>';
                                echo "<td><button type='button' class='btn btn-primary' onclick=\"window.location.href='$link'\">$icon</button></td>";

                                // Edit button
                                $linke = "actualizar_formacion.php?id={$id_comision}";
                                $icone = '<i class="far fa-edit"></i>';
                                echo "<td><button type='button' class='btn btn-primary' onclick=\"window.location.href='$linke'\">$icone</button></td>";
                                  // Edit button
                              
                                echo "<td><button type='button' class='btn btn-primary informe-btn' data-toggle='modal' data-target='#informeModal' data-id='$id_comision'>Informe</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='27'>No results found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        
        <script>
            $(document).ready(function() {
                $('#tablaUsuarios').DataTable({
                    "search": {
                        "smart": true
                    },
                    "language": {
                        "info": "Mostrando de _START_ a _END_ de <span style='color: red; font-weight: bold; font-size: 1.1em;'>_TOTAL_</span> Registros",
                        "infoFiltered": "(filtrados de _MAX_ registros totales)",
                        "lengthMenu": "Mostrar _MENU_ registros",
                        "search": "Buscar:",
                        "paginate": {
                            "previous": "Anterior",
                            "next": "Siguiente"
                        }
                    }
                });
            });
        </script>
    </div>
    
<!-- Modal para el formulario de informe -->
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
                <!-- Contenido del formulario de informe -->
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
  <!-- JavaScript code -->
<script>
    $(document).ready(function() {
        // Capturar el click en el botón "Guardar Cambios"
        $('#guardarCambiosBtn').click(function() {
            // Obtener los valores del formulario
            var comision_id = $('#comision_id').val();
            var fecha_informe = $('#fecha_informe').val();
            var folios = $('#folios').val();
            
            // Mostrar los valores en la consola del navegador
            console.log("Comisión ID: " + comision_id);
            console.log("Fecha Informe: " + fecha_informe);
            console.log("Folios: " + folios);

            // Enviar los datos al script de actualización vía AJAX
            $.ajax({
                url: 'actualizar_solicitud_informe_modal.php',
                type: 'POST',
                data: {
                    comision_id: comision_id,
                    fecha_informe: fecha_informe,
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

    
</body>
</html>
