<?php
// diagnostico_parra.php
header('Content-Type: text/plain; charset=utf-8');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); 
define('DB_NAME', 'comisiones_academicas');

function diagnosticoParra() {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    $cedula = '80065947';
    $evento_sheet = "Visita al proyecto Fondecyt de iniciación\n#11240575: \"\"Desarrollo profesional de profesores que inician su carrera\ndocente en contextos escolares rurales, migrantes e indígenas\"";
    
    echo "=== DIAGNÓSTICO ALDO PARRA ===\n";
    echo "Cédula: $cedula\n";
    echo "Evento Sheet: " . str_replace("\n", "\\n", $evento_sheet) . "\n\n";
    
    // Buscar en BD
    $sql = "SELECT id, evento, No_resolucion, link_resolucion 
            FROM comision_academica 
            WHERE documento = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $cedula);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Registros en BD:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']} | Evento: {$row['evento']} | Resolución: {$row['No_resolucion']} | Link: " . ($row['link_resolucion'] ? 'SI' : 'NO') . "\n";
    }
    $stmt->close();
}

diagnosticoParra();
?>