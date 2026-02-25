<?php
require 'conn.php';

if (isset($_GET['pais'])) {
    $pais = $_GET['pais'];

    $query = "SELECT id_ciudad, CIUDAD FROM ciudad WHERE pais = ? order by CIUDAD ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $pais);
    $stmt->execute();
    $result = $stmt->get_result();

    $ciudades = [];
    while ($row = $result->fetch_assoc()) {
        $ciudades[] = ['id' => $row['id_ciudad'], 'nombre' => $row['CIUDAD']];
    }

    echo json_encode(['ciudades' => $ciudades]);
} else {
    echo json_encode(['error' => 'No se proporcionó un país válido.']);
}
?>