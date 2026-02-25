<?php
// Incluye el archivo de conexión a la base de datos
include 'conn.php';

// Verifica si se ha recibido el ID de la comisión
if (isset($_POST['comision_id'])) {
    $comision_id = $_POST['comision_id'];

    // Crea la conexión a la base de datos
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    // Verifica si hay errores en la conexión
    if ($conn->connect_error) {
        echo json_encode(array('error' => 'Conexión fallida: ' . $conn->connect_error));
        exit;
    }

    // Consulta para obtener los datos del informe
    $sql = "SELECT DATE_FORMAT(fecha_informe, '%Y-%m-%d') AS fecha_informe_formateada, folios FROM comision_academica WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $comision_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica si se encontró algún resultado
    if ($result->num_rows > 0) {
        // Obtén los datos del resultado
        $row = $result->fetch_assoc();
        $response = array(
            'fecha_informe_formateada' => $row['fecha_informe_formateada'],
            'folios' => $row['folios']
        );
    } else {
        $response = array(
            'error' => 'No se encontraron datos para la comisión especificada.'
        );
    }

    // Cierra la conexión
    $stmt->close();
    $conn->close();

    // Devuelve la respuesta como JSON
    echo json_encode($response);
} else {
    echo json_encode(array('error' => 'ID de comisión no proporcionado.'));
}
?>
