<?php
// Verificar si se recibieron los datos del formulario
if(isset($_POST['comision_id']) && isset($_POST['fecha_informe']) && isset($_POST['folios'])) {
    // Obtener los datos del formulario
    $comision_id = $_POST['comision_id'];
    $fecha_informe = $_POST['fecha_informe'];
    $folios = $_POST['folios'];
    $reintegrado = 1;

    // Mostrar las variables y el SQL
    echo "Variables a guardar:<br>";
    echo "Comisión ID: $comision_id<br>";
    echo "Fecha Informe: $fecha_informe<br>";
    echo "Folios: $folios<br>";
    echo "Reintegrado: $reintegrado<br><br>";

    echo "SQL a ejecutar:<br>";
    echo "UPDATE comision_academica SET fecha_informe = '$fecha_informe', folios = '$folios', reintegrado = '$reintegrado' WHERE id = '$comision_id';<br><br>";

    // Incluir el archivo de conexión a la base de datos
    require 'conn.php';

    // Preparar la consulta para actualizar los datos de la comisión en la tabla 'comision_academica'
    $query = "UPDATE comision_academica SET 
                fecha_informe = ?, 
                folios = ?, 
                reintegrado = ? 
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);

    // Vincular los parámetros de la consulta
    $stmt->bind_param("siii", $fecha_informe, $folios, $reintegrado, $comision_id);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Mostrar mensaje de éxito
        echo "Datos actualizados correctamente.<br>";
    } else {
        // Si la ejecución de la consulta falla, mostrar un mensaje de error
        echo "Error al actualizar los datos de la comisión: " . $conn->error . "<br>";
    }

    // Cerrar la conexión a la base de datos
    $stmt->close();
    $conn->close();

    // Esperar antes de redirigir
    echo "Redirigiendo en 5 segundos...";
    header("refresh:5; url=comisiones.php?mensaje=Datos actualizados correctamente");
    exit();
} else {
    // Si no se recibieron los datos del formulario, redirigir a la página de inicio
    header("Location: comisiones.php");
    exit();
}
?>
