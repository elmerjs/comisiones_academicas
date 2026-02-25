<?php
// Incluir archivo de conexión a la base de datos
include('cn.php');

// Obtener los valores pasados por GET
$comisionId = $_GET['comision_id'];
$cedulaTercero = $_GET['cedula_tercero'];

// Verificar si los valores están presentes
if (!$comisionId || !$cedulaTercero) {
    die('Error: Faltan parámetros. comision_id o cedula_tercero no están definidos.');
} else {
    echo "Parámetros recibidos correctamente: comision_id = $comisionId, cedula_tercero = $cedulaTercero<br>";
}

// Verificar conexión a la base de datos
if (!$con) {
    die('Error: No se pudo conectar a la base de datos. ' . mysqli_connect_error());
} else {
    echo "Conexión a la base de datos exitosa.<br>";
}

// Consultar los datos del tercero
$queryTercero = "SELECT fk_depto AS com_acad_depto, vincul AS com_acad_vincul 
                 FROM tercero 
                 WHERE documento_tercero = '$cedulaTercero'";
$resultTercero = mysqli_query($con, $queryTercero);

// Verificar si la consulta se ejecutó correctamente
if (!$resultTercero) {
    die('Error en la consulta de datos del tercero: ' . mysqli_error($con));
} else {
    echo "Consulta de datos del tercero ejecutada correctamente.<br>";
}

// Verificar si se encontró al tercero
if (mysqli_num_rows($resultTercero) == 0) {
    echo "<script>alert('Error: El tercero con la cédula proporcionada no existe.');</script>";
        exit;  // Salir del proceso si no se encuentra el tercero

} else {
    echo "Tercero encontrado.<br>";
}

// Obtener los datos del tercero
$tercero = mysqli_fetch_assoc($resultTercero);

// Consultar los datos de la comisión a clonar
$queryComision = "SELECT `No_resolucion`, `fecha_resolucion`, `tipo_estudio`, `fecha_aval`, 
                         `duracion_horas`, `fechasol`, `organizado_por`, `id_ciudad`, `ciudad_pais`, 
                         `pais`, `tipo_participacion`, `evento`, `nombre_trabajo`, `estado`, 
                         `observacion`, `fechaINI`, `vence`, `vigencia`, `periodo`, `reintegrado`, 
                         `tramito`, `id_rector`, `id_vice`, `reviso`, `justificacion`, 
                         `viaticos`, `tiquetes`, `inscripcion`, `cargo_a`, `valor`, 
                         `cdp`, `tipo_evento`, `link_resolucion`
                  FROM comision_academica
                  WHERE id = $comisionId";
$resultComision = mysqli_query($con, $queryComision);

// Verificar si la consulta de la comisión se ejecutó correctamente
if (!$resultComision) {
    die('Error en la consulta de datos de la comisión: ' . mysqli_error($con));
} else {
    echo "Consulta de datos de la comisión ejecutada correctamente.<br>";
}

// Verificar si se encontró la comisión
if (mysqli_num_rows($resultComision) == 0) {
    die('Error: La comisión con el ID proporcionado no existe.');
} else {
    echo "Comisión encontrada.<br>";
}

// Obtener los datos de la comisión
$comision = mysqli_fetch_assoc($resultComision);

// Insertar un nuevo registro basado en los datos de la comisión original y el tercero
$queryInsert = "INSERT INTO comision_academica (
                    `No_resolucion`, `fecha_resolucion`, `tipo_estudio`, `fecha_aval`, 
                    `duracion_horas`, `fechasol`, `organizado_por`, `id_ciudad`, `ciudad_pais`, 
                    `pais`, `tipo_participacion`, `evento`, `nombre_trabajo`, `estado`, 
                    `observacion`, `fechaINI`, `vence`, `vigencia`, `periodo`, `reintegrado`, 
                    `tramito`, `id_rector`, `id_vice`, `reviso`, `justificacion`, 
                    `viaticos`, `tiquetes`, `inscripcion`, `cargo_a`, `valor`, 
                    `cdp`, `tipo_evento`, `com_acad_depto`, `com_acad_vincul`, `link_resolucion`, `documento`
                ) VALUES (
                    '{$comision['No_resolucion']}', '{$comision['fecha_resolucion']}', '{$comision['tipo_estudio']}', 
                    '{$comision['fecha_aval']}', '{$comision['duracion_horas']}', '{$comision['fechasol']}', 
                    '{$comision['organizado_por']}', '{$comision['id_ciudad']}', '{$comision['ciudad_pais']}', 
                    '{$comision['pais']}', '{$comision['tipo_participacion']}', '{$comision['evento']}', 
                    '{$comision['nombre_trabajo']}', '{$comision['estado']}', '{$comision['observacion']}', 
                    '{$comision['fechaINI']}', '{$comision['vence']}', '{$comision['vigencia']}', 
                    '{$comision['periodo']}', '{$comision['reintegrado']}', '{$comision['tramito']}', 
                    '{$comision['id_rector']}', '{$comision['id_vice']}', '{$comision['reviso']}', 
                    '{$comision['justificacion']}', '{$comision['viaticos']}', '{$comision['tiquetes']}', 
                    '{$comision['inscripcion']}', '{$comision['cargo_a']}', '{$comision['valor']}', 
                    '{$comision['cdp']}', '{$comision['tipo_evento']}', 
                    '{$tercero['com_acad_depto']}', '{$tercero['com_acad_vincul']}', '{$comision['link_resolucion']}', '{$cedulaTercero}'
                )";

// Ejecutar el insert
if (mysqli_query($con, $queryInsert)) {
    echo 'Registro clonado con éxito.<br>';
    $newComisionId = mysqli_insert_id($con); // Obtener el ID de la nueva comisión
} else {
    die('Error al clonar el registro: ' . mysqli_error($con));
}

// Clonar los datos de la tabla `destino`
$queryDestino = "SELECT `id_ciudad_pais`, `ciudad`, `pais`
                 FROM destino
                 WHERE id_comision = $comisionId";
$resultDestino = mysqli_query($con, $queryDestino);

// Verificar si la consulta de destino se ejecutó correctamente
if (!$resultDestino) {
    die('Error en la consulta de destino: ' . mysqli_error($con));
} else {
    echo "Consulta de destino ejecutada correctamente.<br>";
}

// Insertar los datos clonados en la tabla `destino` para la nueva comisión
while ($destino = mysqli_fetch_assoc($resultDestino)) {
    $queryInsertDestino = "INSERT INTO destino (`id_comision`, `id_ciudad_pais`, `ciudad`, `pais`)
                           VALUES ($newComisionId, '{$destino['id_ciudad_pais']}', '{$destino['ciudad']}', '{$destino['pais']}')";
    if (mysqli_query($con, $queryInsertDestino)) {
        echo "Destino clonado con éxito para la nueva comisión.<br>";
    } else {
        die('Error al clonar destino: ' . mysqli_error($con));
    }
}

// Cerrar la conexión
mysqli_close($con);
?>
