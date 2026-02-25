<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $No_resolucion = $_POST['No_resolucion'];
    $fecha_resolucion = $_POST['fecha_resolucion'];
    $documento = $_POST['documento'];
    $tipo_estudio = $_POST['tipo_estudio'];
    $id_ciudad = isset($_POST['id_ciudad']) ? $_POST['id_ciudad'] : null;
    $id_pais = isset($_POST['id_pais']) ? $_POST['id_pais'] : null;
    $ciudad_ext = isset($_POST['ciudad_ext']) ? $_POST['ciudad_ext'] : null;
    $estado = $_POST['estado'];
    $vigencia = $_POST['vigencia'];
    $periodo = $_POST['periodo'];
    $fecha_aval = $_POST['fecha_aval'];
    $duracion_horas = $_POST['duracion_horas'];
    $fechasol = $_POST['fechasol'];
    $organizado_por = $_POST['organizado_por'];
    $tramito = $_POST['tramito'];
    $viaticos = isset($_POST['viaticos']) ? 1 : 0;
    $tiquetes = isset($_POST['tiquetes']) ? 1 : 0;
    $inscripcion = isset($_POST['inscripcion']) ? 1 : 0;
    $cargo_a = $_POST['cargo_a'];
    $valor = $_POST['valor'];
    $numero_cdp = $_POST['numero_cdp'];

    $sql = "INSERT INTO comision_academica (No_resolucion, fecha_resolucion, documento, tipo_estudio, id_ciudad, id_pais, ciudad_ext, estado, vigencia, periodo, fecha_aval, duracion_horas, fechasol, organizado_por, tramito, viaticos, tiquetes, inscripcion, cargo_a, valor, cdp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssiiissssiiissd", $No_resolucion, $fecha_resolucion, $documento, $tipo_estudio, $id_ciudad, $id_pais, $ciudad_ext, $estado, $vigencia, $periodo, $fecha_aval, $duracion_horas, $fechasol, $organizado_por, $tramito, $viaticos, $tiquetes, $inscripcion, $cargo_a, $valor, $numero_cdp);

    if ($stmt->execute()) {
        echo "Comisión académica guardada exitosamente.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
