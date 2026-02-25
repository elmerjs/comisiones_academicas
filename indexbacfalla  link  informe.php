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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
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
                                    ca.id,
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
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                         $query = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_array($query)) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['nombre_completo']; ?></td>
                                <td><?php echo $row['documento_profesor']; ?></td>
                                <td><?php echo $row['periodo_academico']; ?></td>
                                <td><?php echo $row['tipo_estudio']; ?></td>
                                <td><?php echo $row['nombre_fac_min']; ?></td>
                                <td><?php echo $row['depto_nom_propio']; ?></td>
                                <td><?php echo $row['evento']; ?></td>
                                <td><?php echo $row['ciudades_concat']; ?></td>
                                <td><?php echo $row['fecha_formateada']; ?></td>
                                <td><?php echo $row['No_resolucion']; ?></td>
<td> <?php echo($row["reintegrado"] == 1 ? '✓' : '✗') ; ?></td>
                                <td><?php echo $row['tramito']; ?></td>
                              <?php   
                        $tipo_estudio = $row['tipo_estudio'];
                        $id_comision = $row['id'];
                        if ($tipo_estudio == 'EXT') {
                            $link = "resolucion_doc_ext.php?id={$id_comision}";
                        } else {
                            $link = "resolucion_docb.php?id={$id_comision}";
                        }
                        $icon = '<i class="far fa-file-word"></i>';
                    ?>  
                    <td>
                        <button type='button' class='btn btn-primary' onclick="window.location.href='<?php echo $link; ?>'">
                            <?php echo $icon; ?>
                        </button>
                    </td>

                     <?php  
                $linka = "actualizar_formacion.php?id={$id_comision}";
                $icona = '<i class="far fa-edit"></i>';
            ?>
            <td>
                <button type='button' class='btn btn-primary' onclick="window.location.href='<?php echo $linka; ?>'">
                    <?php echo $icona; ?>
                </button>
            </td>
<td>
                     <button class="btn btn-success btn-sm open-modal" data-id="<?php echo $id_comision; ?>" title="Informe">
                    <span class="glyphicon glyphicon-file"></span>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        
       <!-- Script para DataTables -->
    <script>
    $(document).ready(function() {
        $('#tablaUsuarios').DataTable({
            "order": [[ 0, "desc" ]],
            "lengthMenu": [10, 25, 50, 100],
            "language": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        });

        // Abrir el modal y establecer el ID de la fila
        $('.open-modal').on('click', function() {
            var id = $(this).data('id');
            $('#id_informe').val(id);
            $('#myModal').modal('show');
        });

        // Enviar datos del formulario al servidor
        $('#saveInforme').on('click', function() {
            var formData = $('#informeForm').serialize();
            $.ajax({
                type: 'POST',
                url: 'procesar_informe.php',
                data: formData,
                success: function(response) {
                    $('#myModal').modal('hide');
                    location.reload();  // Recargar la página para actualizar la tabla
                }
            });
        });
    });
    </script>
         <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Actualizar Informe</h4>
                </div>
                <div class="modal-body">
                    <form id="informeForm">
                        <div class="form-group">
                            <label for="fecha_informe">Fecha Informe</label>
                            <input type="date" class="form-control" id="fecha_informe" name="fecha_informe" required>
                        </div>
                        <div class="form-group">
                            <label for="folios">Folios</label>
                            <input type="number" class="form-control" id="folios" name="folios" required>
                        </div>
                        <input type="hidden" id="id_informe" name="id_informe">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="saveInforme">Guardar</button>
                </div>
            </div>
        </div>
    </div>

        
    </div>
</body>
</html>
