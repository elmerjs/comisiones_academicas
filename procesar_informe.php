<?php
require 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_informe'];
    $fecha_informe = $_POST['fecha_informe'];
    $folios = $_POST['folios'];

    $sql = "UPDATE comision_academica 
            SET reintegrado = 1, fecha_informe = ?, folios = ? 
            WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $fecha_informe, $folios, $id);
        if ($stmt->execute()) {
            echo "Registro actualizado exitosamente.";
        } else {
            echo "Error al actualizar el registro: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }

    $conn->close();
}
?>
