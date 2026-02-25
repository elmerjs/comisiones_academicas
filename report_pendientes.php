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
    <title>Resultados</title>
    <!-- Estilos de DataTables con Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .modal-header {
            background-color: #5cb85c;
            color: white;
            text-align: center;
            font-size: 30px;
        }
        .modal-body {
            padding: 40px 50px;
        }
        .close {
            color: white;
            opacity: 1.0;
        }
        .form-control {
            margin-bottom: 10px;
        }
        .modal-footer {
            text-align: center;
        }
        .title-container {
            text-align: center;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .table td.align-left {
            text-align: left;
        }
        .container-custom {
            width: 90%;
            margin: 0 auto;
        }
        .table-container {
            margin: 20px 20px 20px 0;
        }
        .filter-btn {
            background-color: transparent;
            border: 1px solid lightgrey;
            border-radius: 20px;
            padding: 5px 20px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
        }
        .filter-btn:hover {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <div id="contenido" >
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="title-container">
                        <br><br>
                        <h3 class="title-text">Reporte Informes de Comisión</h3>
                    </div>
                    <div class="table-container">
                        <form id="formActualizar" method="post" action="actualizar_envio_rh.php">
                           
                            <div class="text-left mb-3">
                                <button type="button" id="filtroINT" class="filter-btn">INT</button>
                                <button type="button" id="filtroEXT" class="filter-btn">EXT</button>
                                <button type="button" id="filtroTodos" class="filter-btn">TODOS</button>
                            </div>
                            
                            <table id="tablaUsuarios" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Vigencia</th>
                                        <th>EXT/INT</th>
                                        <th>Doc.</th>
                                        <th>Nombre</th>
                                        <th>No. Resolución</th>
                                        <th>fecha Informe</th>
                                        <th>Folios</th>
                                        <th>Envío RH</th>
                                        <th>Seleccionar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                   if ($anio != 0) {
    $sql = "SELECT 
                comision_academica.id,
                comision_academica.tipo_estudio,
                comision_academica.documento, 
                tercero.nombre_completo, 
                comision_academica.No_resolucion, 
                comision_academica.folios, 
                comision_academica.fecha_informe,
                comision_academica.envio_rh,
                comision_academica.vigencia
            FROM 
                comision_academica 
            JOIN 
                tercero 
            ON 
                comision_academica.documento = tercero.documento_tercero 
            WHERE 
                comision_academica.reintegrado = 1 AND comision_academica.vigencia = $anio";
} else {
    $sql = "SELECT 
                comision_academica.id,
                comision_academica.tipo_estudio,
                comision_academica.documento, 
                tercero.nombre_completo, 
                comision_academica.No_resolucion, 
                comision_academica.folios, 
                comision_academica.fecha_informe,
                comision_academica.envio_rh,
                comision_academica.vigencia
            FROM 
                comision_academica 
            JOIN 
                tercero 
            ON 
                comision_academica.documento = tercero.documento_tercero 
            WHERE 
                comision_academica.reintegrado = 1";
}
                                    $result = mysqli_query($conn, $sql);

                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                              echo '<td class="align-left">' . $row["vigencia"] . '</td>';
                                              echo '<td class="align-left">' . $row["tipo_estudio"] . '</td>';
                                            echo '<td class="align-left">' . $row["documento"] . '</td>';
                                            echo '<td class="align-left">' . $row["nombre_completo"] . '</td>';
                                            echo '<td>' . $row["No_resolucion"] . '</td>';
                                            echo '<td>' . $row["fecha_informe"] . '</td>';

                                            echo '<td>' . $row["folios"] . '</td>';
                                           if ($row["envio_rh"] == 1) {
                                                    echo '<td>&#10003;</td>';  // ✓
                                                } else {
                                                    echo '<td>&#10007;</td>';  // ✗
                                                }
                                            if ($row["envio_rh"] == 0) {
                                                echo '<td><input type="checkbox" class="seleccionable" name="seleccionados[]" value="' . $row["id"] . '"></td>';
                                            } else {
                                                echo '<td><input type="checkbox" disabled></td>';
                                            }
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No hay resultados</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                          <div class="d-flex justify-content-end mb-3">
    <button type="button" id="seleccionarTodos" class="btn btn-secondary" data-checked="false">Seleccionar Todos</button>
    <button type="submit" class="btn btn-primary ml-2">Envío RH</button>
</div>
                        </form>
                    </div>
                    <?php
                    if (isset($_GET['pdf'])) {
                        echo '<div class="alert alert-success mt-3">';
                        echo 'PDF generado exitosamente. <a href="' . $_GET['pdf'] . '" target="_blank">Descargar PDF</a>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#tablaUsuarios').DataTable({
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
                },
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]]
            });

            $('#seleccionarTodos').click(function() {
                var isChecked = $(this).data('checked');
                $('input.seleccionable').prop('checked', !isChecked);
                $(this).data('checked', !isChecked);
                $(this).text(isChecked ? 'Seleccionar Todos' : 'Deseleccionar Todos');
            });

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
