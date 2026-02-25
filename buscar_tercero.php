<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $documento = $_POST['documento'];

    // Realizar consulta para buscar el tercero según el documento ingresado
    // Aquí debes ejecutar la consulta SQL correspondiente y obtener los datos del tercero
    // Luego, devolver los datos del tercero en formato JSON
    $response = array(
        'nombre_completo' => 'Nombre del tercero obtenido de la base de datos',
        // Otros datos del tercero obtenidos de la base de datos
    );

    echo json_encode($response);
}
?>
