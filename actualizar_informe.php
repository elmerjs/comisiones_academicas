<?php
require 'conn.php';
require('include/header.php');

if(isset($_GET['id'])) {
    $comision_id = $_GET['id'];

    // Consulta para obtener los datos de la comisión basada en el id
    $query = "SELECT * FROM comision_academica WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $comision_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $comision = $result->fetch_assoc();

    if (!$comision) {
        echo "No se encontraron datos para el ID de comisión proporcionado.";
        exit();
    }

    // Consulta para obtener los datos del profesor basado en el documento del profesor
    $doc = $comision['documento'];
    $query_profesor = "SELECT t.documento_tercero, t.nombre_completo, d.depto_nom_propio, f.nombre_fac_min, t.CARGO_ADMIN
                       FROM tercero t
                       LEFT JOIN deparmanentos d ON t.fk_depto = d.PK_DEPTO
                       LEFT JOIN facultad f ON d.FK_FAC = f.PK_FAC
                       WHERE t.documento_tercero = ?";
    $stmt_profesor = $conn->prepare($query_profesor);
    $stmt_profesor->bind_param("s", $doc);
    $stmt_profesor->execute();
    $result_profesor = $stmt_profesor->get_result();
    $profesor = $result_profesor->fetch_assoc();

    if (!$profesor) {
        echo "No se encontraron datos para el documento del profesor proporcionado.";
        exit();
    }
} else {
    echo "No se proporcionó un ID de comisión válido.";
    exit();
}

// Consultas para obtener las listas de rectores, vicerrectores, usuarios y revisores
$rectores_query = "SELECT rector_cc, rector_nombre FROM rector ORDER BY rector_nombre ASC";
$rectores_result = $conn->query($rectores_query);

$vicerrectores_query = "SELECT vice_cc, vice_nombre FROM vicerrector ORDER BY vice_nombre ASC";
$vicerrectores_result = $conn->query($vicerrectores_query);

$user_query = "SELECT name FROM users ORDER BY name ASC";
$user_result = $conn->query($user_query);

$revisa_query = "SELECT revisa_nom_propio FROM revisa ORDER BY revisa_nom_propio ASC";
$revisa_result = $conn->query($revisa_query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Solicitud de Formación</title>


</head>
<body>
    <br><br><br>
<div class="container">
    <h4 class="my-4">Informe Comision</h4>
    <form action="actualizar_solicitud_informe.php" method="post">
        <input type="hidden" name="comision_id" value="<?= $comision_id ?>">

        <div class="row mb-3">
            <div class="col-md-2">
                <label for="numero">CC:</label>
                <input type="text" class="form-control" id="numero" name="numero" value="<?= $profesor['documento_tercero'] ?>" readonly>
            </div>
            <div class="col-md-4">
                <label for="nombre">Nombre del Profesor:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $profesor['nombre_completo'] ?>" readonly>
            </div>
            <div class="col-md-3">
                <label for="depto">Departamento:</label>
                <input type="text" class="form-control" id="depto" name="depto" value="<?= $profesor['depto_nom_propio'] ?>" readonly>
            </div>
            <div class="col-md-3">
                <label for="facultad">Facultad:</label>
                <input type="text" class="form-control" id="facultad" name="facultad" value="<?= $profesor['nombre_fac_min'] ?>" readonly>
            </div>
        </div>
        <br>
        <div class="row mb-3">
           
            <div class="col-md-3">
                <label for="fecha_informe">Fecha Informe:</label>
                <input type="date" class="form-control" id="fecha_informe" name="fecha_informe" value="<?= $comision['fecha_informe'] ?>" required>
            </div>
             <div class="col-md-3">
                <label for="folios">Folios:</label>
                <input type="text" class="form-control" id="folios" name="folios" value="<?= $comision['folios'] ?>" >
            </div>
           
        </div>
        <br>
      
          <br>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>


</body>
</html>
