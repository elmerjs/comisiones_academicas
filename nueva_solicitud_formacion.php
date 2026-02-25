    <?php
    require 'conn.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recibir y sanitizar los datos del formulario
        $numero = $_POST['numero']; // cc
        
        
          
$consulta_depto_vinc = "select * from tercero  where tercero.documento_tercero = '$numero'";

$resultadodepto_vinc = $conn->query($consulta_depto_vinc); //se requiere para guardar el deparmaeento  y vinculacion en ese momento del profesor

while ($rowdepto_vinc = $resultadodepto_vinc->fetch_assoc()) {
     $vinculacion_actual= $rowdepto_vinc['vincul'];
     $depto_actual = $rowdepto_vinc['fk_depto'];
    
}
        
        
        $nombre = $_POST['nombre']; // no se requiere
        $depto = $_POST['depto']; // no se requiere
        $facultad = $_POST['facultad']; // no se requiere
$evento = htmlspecialchars($_POST['evento'], ENT_QUOTES, 'UTF-8');
if (stripos($evento, 'foro') !== false) {
    $tipo_evento = 'foro'; $tipo_evento = 'foro';
} elseif (stripos($evento, 'visita') !== false) {
    $tipo_evento = 'viaje de estudio';
} elseif (stripos($evento, 'seminario') !== false) {
    $tipo_evento = 'seminario';
} elseif (stripos($evento, 'congreso') !== false) {
    $tipo_evento = 'congreso';
} elseif (stripos($evento, 'encuentro') !== false) {
    $tipo_evento = 'encuentro';
} elseif (stripos($evento, 'curso') !== false) {
    $tipo_evento = 'curso';
} elseif (stripos($evento, 'capacitacion') !== false) {
    $tipo_evento = 'capacitacion';
} elseif (stripos($evento, 'coloquio') !== false) {
    $tipo_evento = 'coloquio';
} elseif (stripos($evento, 'taller') !== false) {
    $tipo_evento = 'taller';
} elseif (stripos($evento, 'conferencia') !== false) {
    $tipo_evento = 'conferencia';
} elseif (stripos($evento, 'estancia') !== false) {
    $tipo_evento = 'estancia';
} elseif (stripos($evento, 'cumbre') !== false) {
    $tipo_evento = 'cumbre';}

else {$tipo_evento = 'similares';}

        $fecha_inicio = $_POST['fecha_inicio']; // fechaINI
        $fecha_fin = $_POST['fecha_fin']; // vence
        $observaciones = $_POST['observaciones'];
        $No_resolucion = $_POST['No_resolucion'];
        $fecha_resolucion = $_POST['fecha_resolucion'];
        $tipo_estudio = $_POST['tipo_estudio']; // INT EXT
        $pais = $_POST['pais'];
        $ciudades = $_POST['ciudad']; // Array de ciudades
        $estado = $_POST['estado'];
        $vigencia = $_POST['vigencia'];
        $periodo = $_POST['periodo'];
        $fecha_aval = $_POST['fecha_aval'];
        $duracion_horas = $_POST['duracion_horas'];
$organizado_por = htmlspecialchars($_POST['organizado_por'], ENT_QUOTES, 'UTF-8');        $tipo_participacion = $_POST['tipo_participacion'];
$nombre_trabajo = htmlspecialchars($_POST['nombre_trabajo'], ENT_QUOTES, 'UTF-8');        $tramito = $_POST['tramito'];
        $viaticos = isset($_POST['viaticos']) ? 1 : 0;
        $tiquetes = isset($_POST['tiquetes']) ? 1 : 0;
        $inscripcion = isset($_POST['inscripcion']) ? 1 : 0;
        $cargo_a = $_POST['cargo_a'];
        $valor = $_POST['valor'];
        $cdp = $_POST['cdp'];
        $justificacion = $_POST['justificacion'];
        $id_rector = $_POST['id_rector'];
        $reviso = $_POST['reviso'];

        $id_vice = $_POST['id_vice'];
 $modalidad = $_POST['modalidad'];
        // Validar los campos requeridos
        if (empty($evento) || empty($fecha_inicio) || empty($fecha_fin) || empty($No_resolucion) || empty($fecha_resolucion) || empty($tipo_estudio) || empty($pais) || empty($ciudades) || empty($estado) || empty($vigencia) || empty($periodo) || empty($tramito)) {
            echo "Por favor complete todos los campos obligatorios.";
            exit();
        }

        // Insertar los datos en la tabla comision_academica
        $query = "INSERT INTO comision_academica (No_resolucion, fecha_resolucion, documento, tipo_estudio, fecha_aval, duracion_horas, fechasol, organizado_por, id_ciudad, ciudad_pais, pais, tipo_participacion, evento, nombre_trabajo, estado, observacion, fechaINI, vence, vigencia, periodo, reintegrado, fecha_informe, folios, tramito, id_rector, id_vice, reviso,justificacion, viaticos, tiquetes, inscripcion, cargo_a, valor, cdp,tipo_evento, com_acad_depto, com_acad_vincul, modalidad)
                  VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssssssssssssssisssssiiissssiss", 
            $No_resolucion, $fecha_resolucion, $numero, $tipo_estudio, $fecha_aval, $duracion_horas, 
            $organizado_por, $ciudades[0], $ciudades[0], $pais[0], $tipo_participacion, $evento, $nombre_trabajo, 
            $estado, $observaciones, $fecha_inicio, $fecha_fin, $vigencia, $periodo, $tramito, $id_rector, 
            $id_vice, $reviso,$justificacion, $viaticos, $tiquetes, $inscripcion, $cargo_a, $valor, $cdp, $tipo_evento,$depto_actual,$vinculacion_actual,$modalidad
        );

        if ($stmt->execute()) {
            // Obtenemos el ID de la comisión recién insertada
            $id_comision = $stmt->insert_id;

            // Ahora insertamos los destinos en la tabla destino
            foreach ($pais as $key => $value) {
                $ciudad = $ciudades[$key];
                $query_destino = "INSERT INTO destino (id_comision, ciudad, pais) VALUES (?, ?, ?)";
                $stmt_destino = $conn->prepare($query_destino);
                $stmt_destino->bind_param("iss", $id_comision, $ciudad, $value);
                $stmt_destino->execute();
             // Actualizar id_ciudad_pais en caso de que sea NULL
                $query_update = "UPDATE destino SET id_ciudad_pais = CONCAT(ciudad, ' - ', pais) WHERE id_comision = ? AND id_ciudad_pais IS NULL";
                $stmt_update = $conn->prepare($query_update);
                $stmt_update->bind_param("i", $id_comision);
                $stmt_update->execute();
                        }

           
echo "<script>
    if (confirm('Solicitud de formación guardada exitosamente. ¿Desea generar el documento?')) {
        if ('$tipo_estudio' == 'EXT') {
            window.location.href = 'resolucion_doc_ext.php?id=$id_comision';
        } else {
            window.location.href = 'resolucion_docb.php?id=$id_comision';
        }

        // Redirigir a 'indexprof.php' después de 1 segundo
        setTimeout(function(){
            window.location.href = 'indexprof.php?id=$numero&nombre=$nombre&depto=$depto&cargo=&kdepto=&facultad=$facultad';
        }, 1000); // 1000 milisegundos = 1 segundo
    } else {
        window.location.href = 'indexprof.php?id=$numero&nombre=$nombre&depto=$depto&cargo=&kdepto=&facultad=$facultad';
    }
</script>";
    } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Método de solicitud no válido.";
    }
    ?>