<?php
require 'conn.php';

// Verificar que se haya enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados por el formulario
    $comision_id = $_POST['comision_id'];
    $numero = $_POST['numero'];
    $nombre = $_POST['nombre'];
    $depto = $_POST['depto'];
    $facultad = $_POST['facultad'];
    $No_resolucion = $_POST['No_resolucion'];
    $fecha_resolucion = $_POST['fecha_resolucion'];
    $tipo_estudio = $_POST['tipo_estudio'];
    $tipo_participacion = $_POST['tipo_participacion'];
    $fechaINI = $_POST['fechaINI'];
    $vence = $_POST['vence'];
    $fecha_aval = $_POST['fecha_aval'];
    $evento = $_POST['evento'];
    $organizado_por = $_POST['organizado_por'];
    $nombre_trabajo = isset($_POST['nombre_trabajo']) ? $_POST['nombre_trabajo'] : null;
    $justificacion = $_POST['justificacion'];
    $duracion_horas = isset($_POST['duracion_horas']) ? $_POST['duracion_horas'] : null;
    $vigencia = $_POST['vigencia'];
    $periodo = $_POST['periodo'];
    $estado = $_POST['estado'];
    $viaticos = isset($_POST['viaticos']) ? 1 : 0;
    $tiquetes = isset($_POST['tiquetes']) ? 1 : 0;
    $inscripcion = isset($_POST['inscripcion']) ? 1 : 0;
    $cargo_a = isset($_POST['cargo_a']) ? $_POST['cargo_a'] : null;
    $cdp = isset($_POST['cdp']) ? $_POST['cdp'] : null;
    $valor = isset($_POST['valor']) ? $_POST['valor'] : null;
    $observacion = isset($_POST['observacion']) ? $_POST['observacion'] : null;
    $rector = $_POST['rector'];
    $vicerrector = $_POST['vicerrector'];
    $reviso = $_POST['reviso'];
    $tramito = $_POST['tramito'];
    $link_resolucion = $_POST['link_resolucion'];
    $modalidad = $_POST['modalidad'];


    // Actualizar los datos de la comisión en la tabla 'comision_academica'
    $query = "UPDATE comision_academica SET 
                No_resolucion = ?, 
                fecha_resolucion = ?, 
                tipo_estudio = ?, 
                tipo_participacion = ?, 
                fechaINI = ?, 
                vence = ?, 
                fecha_aval = ?, 
                evento = ?, 
                organizado_por = ?, 
                nombre_trabajo = ?, 
                justificacion = ?, 
                duracion_horas = ?, 
                vigencia = ?, 
                periodo = ?, 
                estado = ?, 
                viaticos = ?, 
                tiquetes = ?, 
                inscripcion = ?, 
                cargo_a = ?, 
                cdp = ?, 
                valor = ?, 
                observacion = ?, 
                id_rector = ?, 
                id_vice = ?, 
                reviso = ?, 
                tramito = ?,
                link_resolucion = ? ,
 modalidad = ? 
              WHERE id = ?";
   // echo $link_resolucion;
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssssssssiisssisssssssi",
                      $No_resolucion, 
                      $fecha_resolucion, 
                      $tipo_estudio, 
                      $tipo_participacion, 
                      $fechaINI, 
                      $vence, 
                      $fecha_aval, 
                      $evento, 
                      $organizado_por, 
                      $nombre_trabajo, 
                      $justificacion, 
                      $duracion_horas, 
                      $vigencia, 
                      $periodo, 
                      $estado, 
                      $viaticos, 
                      $tiquetes, 
                      $inscripcion, 
                      $cargo_a, 
                      $cdp, 
                      $valor, 
                      $observacion, 
                      $rector, 
                      $vicerrector, 
                      $reviso, 
                      $tramito, 
                      $link_resolucion, $modalidad,
                      $comision_id
                     );
//echo  "vice: ".$vicerrector. "rev: ".$reviso."tramito: ".$tramito."query: ".$query;
    if ($stmt->execute()) {
        // Eliminar los destinos actuales asociados a la comisión
        $delete_destinos_query = "DELETE FROM destino WHERE id_comision = ?";
        $delete_stmt = $conn->prepare($delete_destinos_query);
        $delete_stmt->bind_param("i", $comision_id);
        $delete_stmt->execute();

        // Insertar los nuevos destinos
        $pais_array = $_POST['pais'];
        $ciudad_array = $_POST['ciudad'];

        $insert_destinos_query = "INSERT INTO destino (id_comision, pais, ciudad) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_destinos_query);

        for ($i = 0; $i < count($pais_array); $i++) {
            $pais = $pais_array[$i];
            $ciudad = $ciudad_array[$i];
            $insert_stmt->bind_param("iss", $comision_id, $pais, $ciudad);
            $insert_stmt->execute();
              $query_update = "UPDATE destino SET id_ciudad_pais = CONCAT(ciudad, ' - ', pais) WHERE id_comision = ? AND id_ciudad_pais IS NULL";
                $stmt_update = $conn->prepare($query_update);
                $stmt_update->bind_param("i", $comision_id);
                $stmt_update->execute();
        }

        // Redirigir o mostrar un mensaje de éxito
        header("Location: indexprof.php?id=$numero&nombre=$nombre&depto=$depto&cargo=$cargo&kdepto=$kdepto&facultad=$facultad");
        exit();
    } else {
        echo "Error al actualizar la solicitud: " . $stmt->error;
    }
} else {
    echo "Método de solicitud no permitido.";
}
?>
