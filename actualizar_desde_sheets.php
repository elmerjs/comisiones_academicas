<?php
header('Content-Type: text/html; charset=utf-8');

// Configuración BD
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); 
define('DB_NAME', 'comisiones_academicas');

function actualizarBD($datos) {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        return ["success" => false, "message" => "Error conexión: " . $mysqli->connect_error];
    }
    $mysqli->set_charset('utf8mb4');

    $actualizados = 0;
    $detalle = [];

    // Estrategia 1: Por cédulas
    if (!empty($datos['cedulas'])) {
        $cedulasArray = is_array($datos['cedulas']) ? $datos['cedulas'] : explode(',', $datos['cedulas']);
        
        foreach ($cedulasArray as $cedula) {
            $cedula = trim($cedula);
            if (empty($cedula)) continue;
            
            $sql = "UPDATE comision_academica 
                    SET No_resolucion = ?, fecha_resolucion = ?, link_resolucion = ?
                    WHERE documento = ? AND evento LIKE ?";
            
            $stmt = $mysqli->prepare($sql);
            $evento_like = "%" . $datos['evento_clave'] . "%";
            
            $stmt->bind_param('sssss', 
                $datos['No_resolucion'],
                $datos['fecha_resolucion'],
                $datos['link_resolucion'],
                $cedula,
                $evento_like
            );
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $actualizados++;
                    $detalle[] = "Cédula $cedula: OK";
                } else {
                    $detalle[] = "Cédula $cedula: No encontrada";
                }
            } else {
                $detalle[] = "Cédula $cedula: Error - " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Estrategia 2: Por nombres
    if ($actualizados == 0 && !empty($datos['nombres'])) {
        $nombresArray = is_array($datos['nombres']) ? $datos['nombres'] : explode(',', $datos['nombres']);
        
        foreach ($nombresArray as $nombre) {
            $nombre = trim($nombre);
            if (empty($nombre)) continue;
            
            $sql = "UPDATE comision_academica ca
                    JOIN tercero t ON ca.documento = t.documento_tercero
                    SET ca.No_resolucion = ?, ca.fecha_resolucion = ?, ca.link_resolucion = ?
                    WHERE t.nombre_completo LIKE ? AND ca.evento LIKE ?";
            
            $stmt = $mysqli->prepare($sql);
            $nombre_like = "%" . $nombre . "%";
            $evento_like = "%" . $datos['evento_clave'] . "%";
            
            $stmt->bind_param('sssss',
                $datos['No_resolucion'],
                $datos['fecha_resolucion'],
                $datos['link_resolucion'],
                $nombre_like,
                $evento_like
            );
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $actualizados++;
                    $detalle[] = "Nombre $nombre: OK";
                } else {
                    $detalle[] = "Nombre $nombre: No encontrado";
                }
            } else {
                $detalle[] = "Nombre $nombre: Error - " . $stmt->error;
            }
            $stmt->close();
        }
    }

    $mysqli->close();
    
    return [
        "success" => $actualizados > 0,
        "message" => "Actualizados: $actualizados registros",
        "actualizados" => $actualizados,
        "detalle" => $detalle
    ];
}

// Procesar formulario
if ($_POST) {
    $resultado = actualizarBD($_POST);
    echo "<h3>Resultado:</h3>";
    echo "<p><strong>" . $resultado['message'] . "</strong></p>";
    
    if (!empty($resultado['detalle'])) {
        echo "<h4>Detalle:</h4>";
        echo "<ul>";
        foreach ($resultado['detalle'] as $item) {
            echo "<li>$item</li>";
        }
        echo "</ul>";
    }
    
    echo '<br><a href="'.$_SERVER['PHP_SELF'].'">← Volver</a>';
} else {
    // Mostrar formulario
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Actualizar MySQL desde Sheets</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .form-group { margin: 10px 0; }
            label { display: inline-block; width: 150px; font-weight: bold; }
            input, textarea { width: 400px; padding: 5px; }
            button { padding: 10px 20px; background: #007cba; color: white; border: none; cursor: pointer; }
            button:hover { background: #005a87; }
        </style>
    </head>
    <body>
        <h2>📋 Actualizar MySQL desde Google Sheets</h2>
        <form method="post">
            <div class="form-group">
                <label>No_resolucion:</label>
                <input type="text" name="No_resolucion" required>
            </div>
            
            <div class="form-group">
                <label>fecha_resolucion:</label>
                <input type="date" name="fecha_resolucion" required>
            </div>
            
            <div class="form-group">
                <label>link_resolucion:</label>
                <input type="url" name="link_resolucion" required>
            </div>
            
            <div class="form-group">
                <label>evento_clave:</label>
                <input type="text" name="evento_clave" required>
            </div>
            
            <div class="form-group">
                <label>cedulas:</label>
                <input type="text" name="cedulas" placeholder="Separar por comas: 12345678, 87654321">
            </div>
            
            <div class="form-group">
                <label>nombres:</label>
                <input type="text" name="nombres" placeholder="Separar por comas: JUAN PEREZ, MARIA GARCIA">
            </div>
            
            <button type="submit">🔄 Actualizar MySQL</button>
        </form>
        
        <hr>
        <h3>📊 Cómo usar:</h3>
        <ol>
            <li>Revisa tu <a href="https://sheets.google.com" target="_blank">Google Sheets</a></li>
            <li>Copia los datos de una fila PENDIENTE</li>
            <li>Pega aquí y haz clic en Actualizar</li>
            <li>En Sheets, cambia el estado a "ACTUALIZADO"</li>
        </ol>
    </body>
    </html>
    ';
}
?>