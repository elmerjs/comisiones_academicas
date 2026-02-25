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
    <title>Resultados</title>
    <!-- Estilos de DataTables con Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
        <link rel="stylesheet" type="text/css" href="../css/bootstrap2.min.css"> 
    <link rel="stylesheet" media="screen" href="css/styles.css">


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
       
        .container-custom {
            width: 90%;
            margin: 0 auto;
        }
         th, td {
        border: 1px solid #ddd;
        padding: 8px;
    }
   th {
        background-color: #060264; /* Fondo azul oscuro */
        color: white; /* Texto blanco */
        text-align: center; /* Centrar el texto */
        border-radius: 5px; /* Bordes redondeados */
        margin: 0 5px; /* Espacio entre columnas */
        padding: 10px 15px; /* Espacio interno */
      /*  display: inline-block; /* Hacer que el espacio entre ellos funcione */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Sombra ligera */
    }
    td {
    background-color: #FCFCFC; /* Fondo gris muy tenue */
    padding: 10px 15px; /* Espacio interno */
    border: 1px solid #f5f5f5; /* Línea blanca entre campos */
}   
        .alerta {
    color: red; /* Color de la alerta, puedes cambiarlo a lo que desees */
    font-weight: bold; /* Puedes añadir otros estilos si lo deseas */
}
    </style>
</head>
    
<body>
    <br><br>
    <div id="contenido" >
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="title-container">
                        <h3 class="title-text">Consultas por Profesores</h3>
                    </div>
                    <br><br>
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
                                <th title="histórico de Comisiones Academicas">Formación</th>
                                <th title="Crear Comision o sabático">Crear Comisión</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                          /*  $sql = "SELECT
                                        t.id_tercero as id,
                                        t.documento_tercero AS Documento,
                                        t.nombre_completo AS Nombre,
                                        LOWER(t.vincul) AS Vinculación,
                                        t.estado AS Estado,
                                        t.email,
                                        t.fk_depto as fk_depto,
                                        t.escalafon,
                                        t.fecha_ingreso,
                                        facultad.NOMBREC_FAC as facultad,
                                        d.NOMBRE_DEPTO_CORT AS Nombre_Departamento,
                                        t.cargo_admin AS cargo,
                                        count(c.evento) as comisiones_pend
                                    FROM
                                        tercero t
                                    LEFT JOIN
                                        deparmanentos d ON t.fk_depto = d.PK_DEPTO
                                    LEFT JOIN 
                                        facultad  on  d.FK_FAC = facultad.PK_FAC
                                    LEFT JOIN
                                       /* comision_academica c ON (t.documento_tercero = c.documento AND (c.reintegrado = 0 OR c.reintegrado IS NULL))*//*
                                    comision_academica c ON (t.documento_tercero = c.documento AND ((c.reintegrado = 0 OR c.reintegrado IS NULL)AND c.estado<>'anulada'))

                                       /* where t.vincul not in ('HORA CATEDRA')*//*

                                    GROUP BY
                                        t.documento_tercero  
                                    ORDER BY c.id DESC";*/
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
    CONCAT(cs.estado, ' - ', cs.tipo_estudio, ' - ', cs.fechaINI, ' - ', cs.vence) AS sabatico,
    MAX(CASE WHEN c.estado = 'Activa' THEN 1 ELSE 0 END) AS tiene_ac  
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
ORDER BY `tiene_ac` DESC";
                            $result = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $id = $row["id"];
                                    $cargo = $row["cargo"];

                                    // Condición para verificar si 'sabatico' no es NULL
                            $nombreClass = !is_null($row['sabatico']) ? 'alerta' : ''; // Asigna la clase 'alerta' si hay un valor en 'sabatico'
                            $nombreTitle = !is_null($row['sabatico']) ? $row['sabatico'] : ''; // Asigna el mensaje de 'sabatico' como título

                                    
                                    echo "<tr>";
                                    echo '<td class="align-left"><a href="actualizatercero.php?id=' . $id . '" title="Actualizar tercero">' . $row["Documento"] . '</a></td>';
    echo '<td class="align-left"><span class="' . $nombreClass . '" title="' . $nombreTitle . '">' . $row["Nombre"] . '</span></td>';
                            
                                    echo "<td>" . $row["Vinculación"] . "</td>";
                                    echo "<td>" . $row["Estado"] . "</td>";
                                    echo "<td>" . ucfirst(strtolower($row["Nombre_Departamento"])) . "-" . ucfirst(strtolower($row["facultad"])) . "</td>";

                                    $fechaIngreso = new DateTime($row["fecha_ingreso"]);
                                    $fechaActual = new DateTime();
                                    $interval = $fechaActual->diff($fechaIngreso);
                                    $years = $interval->y;

                                    echo "<td>" . $row["cargo"] . "</td>";
                                    echo "<td>";
                                        if ($row["comisiones_pend"] > 0) {
                        // Si tiene una activa (tiene_ac == 1), restamos 1 a las comisiones pendientes
                                    $comisiones_pendientes_real = ($row["tiene_ac"] == 1) ? $row["comisiones_pend"] - 1 : $row["comisiones_pend"];

                                    if ($comisiones_pendientes_real > 0) {
                                        echo "<span style='color: red;'>" . $comisiones_pendientes_real . " Comisión(es) Pendientes</span>";
                                    } else {
                                        echo "Ok";
                                    }
                                } else {
                                    echo "Ok";
                                }
                                                   
                                    echo "</td>";                                
                                    echo "<td title='histórico de Comisiones' class='text-center'><a href='indexprof.php?id=" . $row["Documento"] . "&nombre=" . $row["Nombre"] . "&depto=" . $row["Nombre_Departamento"] . "&cargo=" . $cargo . "&kdepto=" . $row["fk_depto"] . "&facultad=" . $row["facultad"] . "'><button class='btn btn-link'><i class='fas fa-list' style='color: #004080; font-size: 24px;'></i></button></a></td>";

                                    echo "<td title='Crear comisión' class='text-center'><a href='solicitud_formacion.php?id=" . $row["Documento"] . "'><button class='btn btn-link'><i class='fas fa-plus-circle' style='color: #800000; font-size: 24px;'></i></button></a></td>";

                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>No hay resultados</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                        <!-- Botón para abrir la ventana modal -->
<button id="openModalBtnb" class="btn btn-primary openModalBtnb" style="background-color: #da1100; border-color: #da0600;"> <i class="fas fa-plus"></i>  Crear Profesor</button>
                </div>
            </div>
        </div>
    </div>
<?php
  require_once('modalnuevoprofesor.php'); // Incluye el archivo modal.php
?>
 
                <script>
// Script para abrir la ventana modal y autocompletar el campo de departamento
    
    $(document).on('click', '#openModalBtnb', function() {
    $('#myModalb').css('display', 'block');
        
        
    // Cerrar modal al hacer clic en la "X"
    $('.close').click(function() {
        $('#myModalb').css('display', 'none');
    });
});

</script>
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
        },
        // Desactivar la opción de ordenamiento por defecto
        "order": []
    });
});
    </script>
</body>
</html>
