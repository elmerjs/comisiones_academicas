<?php
require 'conn.php';

// Verificar que se haya enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados por el formulario
    $comision_id = $_POST['comision_id'];
    $fecha_informe = $_POST['fecha_informe'];
    $folios = $_POST['folios'];
    $reintegrado = 1;

    // Actualizar los datos de la comisión en la tabla 'comision_academica'
    $query = "UPDATE comision_academica SET 
                fecha_informe = ?, 
                folios = ?, 
                reintegrado = ? 
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siii", $fecha_informe, $folios, $reintegrado, $comision_id);

    if ($stmt->execute()) {
        // Redirigir o mostrar un mensaje de éxito
        header("Location: comisiones.php");
        exit();
    } else {
        echo "Error al actualizar la solicitud: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Método de solicitud no permitido.";
}
?>
