<?php
// Establecer la conexión con la base de datos principal
require 'conn.php';

// Crear una segunda conexión para la base de datos productividad
$dbname2 = "productividad";
$conn2 = new mysqli($dbhost, $dbuser, $dbpass, $dbname2);

// Verificar la conexión a productividad
if ($conn2->connect_error) {
    die("Conexión fallida a productividad: " . $conn2->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar los datos del formulario
    $documento = $_POST['documento'];
    $id = $_POST['id'];
    $nombre1 = $_POST['nombre1'];
    $nombre2 = $_POST['nombre2'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $escalafon = $_POST['escalafon'];
    $vincul = $_POST['vincul'];
    $vinculacion = $_POST['vinculacion'];
    $depto = $_POST['depto'];
    $cargo = $_POST['cargo'];
    $sexo = $_POST['sexo'];
    $email = $_POST['email'];
    $fecha_ingreso = $_POST['fecha_ingreso'];
    $estado = $_POST['estado'];
    $nombre_completo = $apellido1 . ' ' . $apellido2 . ' ' . $nombre1 . ' ' . $nombre2;

    // Consulta SQL para actualizar en ambas bases de datos
    $sql = "UPDATE tercero SET 
                documento_tercero = '$documento',
                nombre_completo = '$nombre_completo',
                apellido1 = '$apellido1',
                apellido2 = '$apellido2',
                nombre1 = '$nombre1',
                nombre2 = '$nombre2',
                fk_depto = '$depto',
                vincul = '$vincul',
                sexo = '$sexo',
                email = '$email',
                estado = '$estado',
                vinculacion = '$vinculacion',
                cargo_admin = '$cargo',
                escalafon = '$escalafon',
                fecha_ingreso = '$fecha_ingreso'
            WHERE id_tercero = '$id'";

    // Ejecutar la consulta en comisiones_academicas
    if (mysqli_query($conn, $sql)) {
        // Ejecutar la misma consulta en productividad
        if (mysqli_query($conn2, $sql)) {
            // Mostrar mensaje de éxito si ambas consultas son exitosas
            echo '<script>alert("Actualizado con éxito en ambas bases de datos");</script>';
            echo '<script>window.history.go(-2);</script>';
        } else {
            echo "Error al actualizar el registro en productividad: " . mysqli_error($conn2);
        }
    } else {
        echo "Error al actualizar el registro en comisiones_academicas: " . mysqli_error($conn);
    }

    // Cerrar ambas conexiones
    mysqli_close($conn);
    mysqli_close($conn2);
}
?>
