<?php
// Conexión a la base de datos (ajusta los valores según tu configuración)
$servername = "localhost";
$username = "root"; // Cambiar por tu nombre de usuario de MySQL
$password = ""; // Cambiar por tu contraseña de MySQL
$dbname = "comisiones_academicas"; // Cambiar por el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$documento = $_POST['documento'];
$nombre1 = $_POST['nombre1'];
$nombre2 = $_POST['nombre2'];
$apellido1 = $_POST['apellido1'];
$apellido2 = $_POST['apellido2'];
$vincul = $_POST['vincul'];
$escalafon = $_POST['escalafon'];
$vinculacion = $_POST['vinculacion'];
$depto = $_POST['depto'];
$cargo = $_POST['cargo'];
$sexo = $_POST['sexo'];
$email = $_POST['email'];
$fecha_ingreso = $_POST['fecha_ingreso'];
$estado = $_POST['estado'];

$nombre_completo = $apellido1 . ' ' . $apellido2 . ' ' . $nombre1 . ' ' . $nombre2;

// Verificar si el documento ya existe en la base de datos
$queryter = "SELECT * FROM tercero WHERE documento_tercero = '$documento'";
$resultadoter = $conn->query($queryter);

if ($resultadoter->num_rows > 0) {
    // Mostrar alerta si el tercero ya existe
    echo '<script>alert("Tercero ya existe");</script>';
    // Regresar a la página anterior
    echo '<script>window.history.go(-1);</script>';
} else {
    // Construir consulta de inserción
    $sql = "INSERT INTO tercero(documento_tercero, nombre_completo, apellido1, apellido2, nombre1, nombre2, fk_depto, vincul, sexo, estado, vinculacion, cargo_admin, email, escalafon, fecha_ingreso) 
            VALUES ('$documento', '$nombre_completo', '$apellido1', '$apellido2', '$nombre1', '$nombre2', '$depto', '$vincul', '$sexo', '$estado', '$vinculacion', '$cargo', '$email', '$escalafon', '$fecha_ingreso')";

    if ($conn->query($sql) === TRUE) {
        // Mostrar mensaje de éxito
        echo "<script>alert('Creado con éxito');</script>";
        echo '<script>window.location.href = "report_terceros.php";</script>';
    } else {
        // Mostrar mensaje de error con detalles del error
        echo "<script>alert('Error al crear el registro: " . $conn->error . "');</script>";
        echo '<script>window.location.href = "index.php";</script>';
    }
}

// Cerrar conexión
$conn->close();
?>
