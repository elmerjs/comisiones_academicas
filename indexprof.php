    <?php
    // Realizar la conexión a la base de datos
    require 'conn.php';
    require('include/headerz.php');
    if(isset($_GET['id'])) {
        // Obtener el valor de la variable "id"
        $doc = $_GET['id'];
    } else {
        // Si no se recibió el parámetro "id" en la URL
        echo "No se proporcionó un ID válido.";
    }

    $depto = $_GET['depto'];
    $nombre = $_GET['nombre'];
    $fk_depto = $_GET['kdepto'];
    $facultad = $_GET['facultad'];

    // Consulta SQL
    $sqlt = "SELECT * FROM TERCERO WHERE tercero.documento_tercero = '$doc';";
    $resultt = mysqli_query($conn, $sqlt);
    $rowt = mysqli_fetch_assoc($resultt);

    $sql = "SELECT 
        comision_academica.id AS id,
        facultad.NOMBREC_FAC AS NOMBREC_FAC,
        deparmanentos.depto_nom_propio AS NOMBRE_DEPTO,
        tercero.nombre_completo AS nombre_completo,
        tercero.documento_tercero AS documento_tercero,
        comision_academica.No_resolucion, 
        comision_academica.fecha_resolucion,
        GROUP_CONCAT(destino.id_ciudad_pais) AS ciudades_visitadas,
        comision_academica.reintegrado,
        comision_academica.folios,
        comision_academica.fecha_informe,
        comision_academica.tipo_estudio AS tipo_estudio,
        comision_academica.fechaINI AS fechaINI,
        comision_academica.vence AS vence,
        comision_academica.estado AS estado,
        comision_academica.evento AS evento,
        comision_academica.fecha_informe,
        comision_academica.link_resolucion, comision_academica.notificado
    FROM 
        facultad
    JOIN 
        deparmanentos ON facultad.PK_FAC = deparmanentos.FK_FAC
    JOIN 
        tercero ON deparmanentos.PK_DEPTO = tercero.fk_depto
    JOIN 
        comision_academica ON tercero.documento_tercero = comision_academica.documento
    LEFT JOIN 
        destino ON comision_academica.id = destino.id_comision
    WHERE 
        tercero.documento_tercero = '$doc'
    GROUP BY
        comision_academica.id,
        facultad.NOMBREC_FAC,
        deparmanentos.depto_nom_propio,
        tercero.nombre_completo,
        tercero.documento_tercero,
        comision_academica.No_resolucion, 
        comision_academica.fecha_resolucion,
        comision_academica.reintegrado,
        comision_academica.tipo_estudio,
        comision_academica.fechaINI,
        comision_academica.vence,
        comision_academica.estado,
        comision_academica.evento,
        comision_academica.fecha_informe, comision_academica.notificado
    ORDER BY 
        comision_academica.vence desc;";

    $result = mysqli_query($conn, $sql);
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profesor: <?php echo $rowt["documento_tercero"]." - ".$rowt["nombre_completo"]; ?></title>
        <!-- Estilos de DataTables con Bootstrap -->
        
    <!-- Estilos CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

        <style>
            .btn-icon-encargo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 6px 10px;
    font-size: 12px;
    border-radius: 5px;
    border: none;
    background-color: #17a2b8;
    color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: background-color 0.3s ease;
    outline: none; /* Elimina el contorno cuando se enfoca */
    box-shadow: none; /* Elimina cualquier sombra */
}

.btn-icon-encargo:hover {
    background-color: #138496;
    color: white;
}
             .container-custom {
            max-width: 90%; /* Ajustar el ancho del contenedor */
            margin: auto; /* Centrar el contenedor */
        }
            .elegible {
                color: green;
            }
              .table td, .table th {
            padding: 0.5rem; /* Espacio adicional en las celdas */
            vertical-align: middle; /* Centrar contenido verticalmente */
        }
            .btn-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                            padding: 6px 10px; /* Aumenta el tamaño del botón */

/*                padding: 6px 12px;*/
                font-size: 12px;
                border-radius: 5px;
                border: none;
                background-color: #007bff;
                color: white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                cursor: pointer;
                transition: background-color 0.3s ease;
                 outline: none; /* Elimina el contorno cuando se enfoca */
        box-shadow: none; /* Elimina cualquier sombra */
            }
            .btn-icon:hover {
                background-color: #0056b3;
            }
            .btn-icon i {
                font-size: 16px;
            }
           .btn-icon-edit {
     display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 6px 10px;
                font-size: 12px;
                border-radius: 5px;
                border: none;
                background-color: #28a745;
                color: white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                cursor: pointer;
                transition: background-color 0.3s ease;
                 outline: none; /* Elimina el contorno cuando se enfoca */
        box-shadow: none; /* Elimina cualquier sombra */
            }

    .btn-icon-edit:hover {
        background-color: #218838; color: white;
    }
               .btn-icon-informe {
     display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 6px 10px;
                font-size: 12px;
                border-radius: 5px;
                border: none;
                background-color: darkorange;
                color: white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                cursor: pointer;
                transition: background-color 0.3s ease;
                 outline: none; /* Elimina el contorno cuando se enfoca */
        box-shadow: none; /* Elimina cualquier sombra */
            }

    .btn-icon-informe:hover {
        background-color: orangered; color: white;
    }
            
  .btn-enviar-email {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 6px 10px;
    font-size: 12px;
    border-radius: 5px;
    border: none;
    background-color: #D93025; /* Rojo característico de Gmail */
    color: #FFFFFF; /* Blanco para el texto y el ícono */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: background-color 0.3s ease;
    outline: none; /* Elimina el contorno cuando se enfoca */
}

.btn-enviar-email:hover {
    background-color: #B1271B; /* Rojo más oscuro al pasar el mouse */
    color: #FFFFFF;
}

.btn-enviar-email i {
    font-size: 16px; /* Tamaño del ícono */
}

        </style>
    </head>
    <body>
        
        
        
        
        <!-- Scripts JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
        <br><br><br><br>
        <div id="contenido">  
        <div class="container container-custom">
                <h2>Profesor : <?php echo $rowt["documento_tercero"]." - ".$rowt["nombre_completo"]; ?></h2>
            <div class="table-responsive">
                    <table id="tablaComisionados" class="table table-condensed" style="width:100%">
                        <thead>
                            <tr>
                                <th>Tipo
                        </th>
                                <th>Resolución</th>
                               <!-- <th>Fecha Resolución</th>-->
                                <th>Evento</th>
                                <!-- <th>Inicio</th>
                                <th>Vence</th>-->
                                <th>Estado</th>
                                <th>Legaliz</th>
                                <th>Ciudad(es)</th>
                                <th>Resol.</th>
                                <th>Editar</th>  <th>notifc</th> 
                                 <th>Informe</th>  
<th >Encargo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Iterar sobre los resultados y mostrarlos en la tabla
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo '<td>' . $row["tipo_estudio"] . '</a></td>';
$enlace = !empty($row["link_resolucion"]) ? htmlspecialchars($row["link_resolucion"]) : '';
$texto = htmlspecialchars($row["No_resolucion"]);

// Muestra el contenido basado en la variable del enlace
if ($enlace) {
    // Si hay un enlace, muestra el texto como un enlace
    echo "<td><a href='$enlace' target='_blank'>$texto</a></td>";
} else {
    // Si no hay enlace, solo muestra el texto
    echo "<td>$texto</td>";
}                            
                            
                                 $evento = substr($row["evento"], 0, 40); // Obtener los primeros 50 caracteres del evento
   $fechaIni = date("d-m-Y", strtotime($row["fechaINI"])); // Convertir fechaINI a formato dd-mm-aaaa
$vence = date("d-m-Y", strtotime($row["vence"])); // Convertir vence a formato dd-mm-aaaa

    echo "<td><span title='{$row["evento"]}'>{$evento}... {$fechaIni} - {$vence}</span></td>";
                                
                                echo "<td>" . $row["estado"] . "</td>";
                                echo "<td>";
                                echo ($row["reintegrado"] != '1' && $row["estado"] != "anulada") ? "PENDIENTE" : "OK";

                                echo "</td>";

                                $tipo_estudio = $row['tipo_estudio'];
                                $id = $row['id'];
                                $link = ($tipo_estudio == 'EXT') ? "resolucion_doc_ext.php?id={$id}" : "resolucion_docb.php?id={$id}";
                                $linkb = ($tipo_estudio == 'EXT') ? "resolucion_doc_ext.php?id={$id}" : "resolucion_docc.php?id={$id}";

                                $iconDoc = '<i class="fas fa-file-word"></i>';
                                $iconEdit = '<i class="fas fa-edit"></i>';

                                echo "<td>" . $row["ciudades_visitadas"] . "</td>";
                                echo "<td><button type='button' class='btn btn-icon' title = 'genere el documento word de la resolución' onclick=\"window.location.href='$linkb'\">$iconDoc </button>
                                
                                </td>";
                                echo "<td><button type='button' class='btn-icon-edit' title = 'abra el formulario para editar los datos de la comisión' onclick=\"window.location.href='actualizar_formacion.php?id={$id}'\">$iconEdit </button></td>";
                                // Icono de legalización
    $iconInforme = '<i class="fas fa-file-signature"></i>'; // Un ícono para un informe firmado
    
// Suponiendo que $row contiene los datos de la fila actual
if ($row["tipo_estudio"] === "INT" && $row["estado"] === "Activa") {
    if ($row["notificado"] == 1) {
        // Si $row["notificado"] es igual a 1
        echo '<td style="text-align: center;">OK</td>';
    } else {
        // Si $row["notificado"] no es igual a 1
     // Si $row["notificado"] no es igual a 1
        $link_resolucion = $row["link_resolucion"];
        if (empty($link_resolucion)) {
            // Si $row["link_resolucion"] está vacío
            echo '<td style="text-align: center;">
                    <a href="#"
                       class="btn-enviar-email"
                       onclick="alert(\'No se ha incluido un link del PDF de la resolución para la comisión\'); return false;">
                       <i class="fas fa-envelope"></i>
                    </a>
                  </td>';
        } else {
            // Si $row["link_resolucion"] no está vacío
            echo '<td style="text-align: center;vertical-align: middle">
                    <a href="comunicar_comision_int.php?id=' . htmlspecialchars($id) . '"
                       class="btn-enviar-email"
                       onclick="return confirmEmail();">
                       <i class="fas fa-envelope"></i>
                    </a>
                  </td>';
        }
    }
} else {
    // Si $row["tipo_estudio"] no es "INT" o $row["estado"] no es "Activa"
    echo '<td style="text-align: center;">--</td>';
}                           echo "<td class='text-center'><button type='button' class='btn-icon-informe' title = 'incluya aquí datos del informe entregado por el comisionado' data-toggle='modal' data-target='#informeModal' data-id='{$id}'>$iconInforme</button></td>";
$linkEncargo = "oficio_encargo.php?id={$id}";
$iconEncargo = '<i class="fas fa-file-signature"></i>'; // Icono para oficio de solicitud de encargo


                                
                                
$query_encrg = "SELECT cargo_academico_admin, cc_encargado, oficio_encargo 
          FROM comision_academica 
          WHERE id = {$id}";

$result_encrg = mysqli_query($conn, $query_encrg); // Asumiendo que tienes una conexión a la base de datos

// Verificar si la consulta fue exitosa y si hay un resultado
if ($result_encrg && mysqli_num_rows($result_encrg) > 0) {
    $row_encrg = mysqli_fetch_assoc($result_encrg);
    
    // Asignar los valores a variables
    $cargo = $row_encrg['cargo_academico_admin'];
    $cedula = $row_encrg['cc_encargado'];
    $oficio = $row_encrg['oficio_encargo'];
    
     // Solo mostrar el botón si cargo_academico_admin no es NULL
   echo "<td>
        <div style='display: flex; gap: 10px;'> <!-- Flexbox para alinear los botones -->";

if ($row["estado"] == "Activa") {
    // Mostrar el botón de solicitud solo si el estado es "Activa"
    echo "<button type='button' class='btn-icon-encargo' title='Si ostenta un cargo administrativo, genere word de la solicitud de encargo para RRHH' onclick='solicitarEncargo({$id})'>
            {$iconEncargo} Solicitar Encargo
          </button>";
}

if (!is_null($cargo)) {
    // Botón de reimpresión, con el mismo estilo y en línea
    echo "<button type='button' class='btn-icon-encargo' title='Descargar oficio previamente solicitado'
           onclick=\"window.location.href='oficio_encargo.php?id={$id}&cargo={$cargo}&cedula={$cedula}&oficio={$oficio}'\">
            <i class='fas fa-redo-alt'></i> Reimpr.Encargo
          </button>";
}

echo "</div></td>";
} else {
    // Si no se encuentra el registro o la consulta falla
}
                                
                                
                                
                                
                                
                                
                                
echo "<script>
function solicitarEncargo(id) {
    // Pedir los datos
    var cargo = prompt('Cargo:');
    if (cargo == null || cargo == '') {
        alert('Debe ingresar el cargo.');
        return; // Detiene el proceso si no hay input
    }
    
    var cedula = prompt('C.C. del profesor encargado:');
    if (cedula == null || cedula == '') {
        alert('Debe ingresar la cédula.');
        return; // Detiene el proceso si no hay input
    }

    var oficio = prompt('Oficio o formato anexo (ej. PM-FO-4-FOR – 20 del 20 de septiembre de 2024):');
    if (oficio == null || oficio == '') {
        alert('Debe ingresar el oficio o formato anexo.');
        return; // Detiene el proceso si no hay input
    }

    // Redirigir con los parámetros ingresados
    window.location.href = 'oficio_encargo.php?id=' + id + '&cargo=' + encodeURIComponent(cargo) + '&cedula=' + encodeURIComponent(cedula) + '&oficio=' + encodeURIComponent(oficio);
}
</script>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- Botón para retroceder -->
                <button type="button" class="btn btn-secondary" onclick="window.location.href='report_terceros.php'">Volver</button>
                <a href="solicitud_formacion.php?id=<?php echo $rowt['documento_tercero']; ?>" class="btn btn-primary" title="Crear comisión">
                    Crear comisión
                </a>
            </div>    
        </div>
        



        <!-- JavaScript de jQuery -->
        <!-- JavaScript de DataTables con Bootstrap -->
        <script>
        $(document).ready(function() {
            $('#tablaComisionados').DataTable({
                "paging": true,
                "searching": false,
                "info": false
            });
        });
        </script>
                <!-- Modal -->
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
    </body>
    </html>

   
<script>
$(document).ready(function() {
    $('#informeModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var comisionId = button.data('id');
        var modal = $(this);
        modal.find('#comision_id').val(comisionId);
        
        // Mostrar el ID de la comisión en la consola
        console.log("Comisión ID en modal: " + comisionId);
        
        // Realizar una llamada AJAX para obtener los datos del informe
        $.ajax({
            url: 'obtener_datos_informe.php',
            type: 'POST',
            data: {
                comision_id: comisionId
            },
            success: function(response) {
                console.log("Respuesta del servidor: ", response); // Verificar el contenido de la respuesta

                // Intentar parsear la respuesta JSON
                try {
                    var data = JSON.parse(response);
                    console.log("Datos parseados: ", data);

                    if (data.error) {
                        console.error("Error en datos: ", data.error);
                    } else {
                        modal.find('#fecha_informe').val(data.fecha_informe_formateada);
                        modal.find('#folios').val(data.folios);
                    }
                } catch (e) {
                    console.error("Error al parsear JSON: ", e);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error AJAX: ", xhr.responseText);
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
        console.log("Guardando cambios:");
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
                console.log("Respuesta del servidor al guardar cambios: ", response);

                // Cerrar el modal después de actualizar los datos
                $('#informeModal').modal('hide');

                // Recargar la página para reflejar los cambios (opcional)
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error("Error AJAX al guardar cambios: ", xhr.responseText);
            }
        });
    }); 
});
        function confirmEmail() { return confirm('¿Está seguro de que desea enviar este correo electrónico?(recuerde cargar el link de la resolucón en drive)'); }

</script>
        
