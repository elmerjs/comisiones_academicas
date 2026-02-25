<?php
// Realizar la conexión a la base de datos
require 'conn.php';

// Obtener los datos del formulario
$comisionId = $_POST['comision_id'];
$fechaInforme = $_POST['fecha_informe'];
$folios = $_POST['folios'];

// Verificar si la comisión ya tiene un informe en la base de datos
$sqlVerificar = "SELECT * FROM comision_academica WHERE id = $comisionId";
$resultVerificar = mysqli_query($conn, $sqlVerificar);

if (mysqli_num_rows($resultVerificar) > 0) {
    // Si la comisión ya tiene un informe, actualizar los datos existentes
    $sqlActualizar = "UPDATE comision_academica SET fecha_informe = '$fechaInforme', folios = '$folios' WHERE id = $comisionId";
    if (mysqli_query($conn, $sqlActualizar)) {
        echo "Información del informe actualizada correctamente.";
    } else {
        echo "Error al actualizar la información del informe: " . mysqli_error($conn);
    }
} else {
    // Si la comisión no tiene un informe, insertar los nuevos datos
    $sqlInsertar = "INSERT INTO comision_academica ( reintegrado,fecha_informe, folios) VALUES (1,'$fechaInforme', '$folios') where id = '$comisionId'";
    if (mysqli_query($conn, $sqlInsertar)) {
        echo "Informe guardado correctamente.";
    } else {
        echo "Error al guardar el informe: " . mysqli_error($conn);
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
