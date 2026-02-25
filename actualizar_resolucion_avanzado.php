<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración BD
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); 
define('DB_NAME', 'comisiones_academicas');

$response = ['success' => false, 'message' => '', 'updated_records' => []];

try {
    // 1. Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    // 2. Obtener y validar JSON
    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);
    
    if (empty($data)) {
        throw new Exception('Datos JSON vacíos', 400);
    }

    $required = ['No_resolucion', 'fecha_resolucion', 'link_resolucion', 'evento_clave'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Campo requerido: $field", 400);
        }
    }

    // 3. Conectar a BD
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        throw new Exception('Error conexión BD', 500);
    }
    $mysqli->set_charset('utf8mb4');

    // 4. ESTRATEGIA DE COINCIDENCIA POR CAPAS
    $updated_ids = [];
    
    // CAPA 1: Coincidencia por CÉDULA + EVENTO (más precisa)
    if (!empty($data['cedulas']) && is_array($data['cedulas'])) {
        foreach ($data['cedulas'] as $cedula) {
            $ids = actualizarPorCedulaYEvento($mysqli, $cedula, $data);
            $updated_ids = array_merge($updated_ids, $ids);
        }
    }
    
    // CAPA 2: Coincidencia por NOMBRES + EVENTO
    if (!empty($data['nombres']) && is_array($data['nombres']) && empty($updated_ids)) {
        foreach ($data['nombres'] as $nombre) {
            $ids = actualizarPorNombreYEvento($mysqli, $nombre, $data);
            $updated_ids = array_merge($updated_ids, $ids);
        }
    }
    
    // CAPA 3: Coincidencia solo por EVENTO (último recurso)
    if (empty($updated_ids)) {
        $ids = actualizarPorEvento($mysqli, $data);
        $updated_ids = array_merge($updated_ids, $ids);
    }

    // 5. Preparar respuesta
    $updated_ids = array_unique($updated_ids);
    
    if (!empty($updated_ids)) {
        $response['success'] = true;
        $response['message'] = "Registros actualizados exitosamente";
        $response['updated_records'] = $updated_ids;
        $response['strategy_used'] = getEstrategiaUsada($data, $updated_ids);
        http_response_code(200);
    } else {
        $response['message'] = "No se encontraron registros coincidentes";
        $response['search_data'] = [
            'evento' => $data['evento_clave'],
            'cedulas' => $data['cedulas'] ?? [],
            'nombres' => $data['nombres'] ?? []
        ];
        http_response_code(200);
    }

    $mysqli->close();

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code($e->getCode() ?: 500);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

// --- FUNCIONES DE COINCIDENCIA ---

function tieneLinkResolucion($mysqli, $documento, $evento) {
    $sql = "SELECT link_resolucion FROM comision_academica 
            WHERE documento = ? AND evento LIKE ? 
            LIMIT 1";
    
    $stmt = $mysqli->prepare($sql);
    $evento_like = "%" . $evento . "%";
    $stmt->bind_param('ss', $documento, $evento_like);
    $stmt->execute();
    $stmt->bind_result($link_existente);
    $stmt->fetch();
    $stmt->close();
    
    return !empty($link_existente);
}

function tieneLinkResolucionPorNombre($mysqli, $nombre, $evento) {
    $sql = "SELECT ca.link_resolucion FROM comision_academica ca
            JOIN tercero t ON ca.documento = t.documento_tercero
            WHERE t.nombre_completo LIKE ? AND ca.evento LIKE ? 
            LIMIT 1";
    
    $stmt = $mysqli->prepare($sql);
    $nombre_like = "%" . $nombre . "%";
    $evento_like = "%" . $evento . "%";
    $stmt->bind_param('ss', $nombre_like, $evento_like);
    $stmt->execute();
    $stmt->bind_result($link_existente);
    $stmt->fetch();
    $stmt->close();
    
    return !empty($link_existente);
}

function tieneLinkResolucionPorEvento($mysqli, $evento) {
    $sql = "SELECT link_resolucion FROM comision_academica 
            WHERE evento LIKE ? 
            AND (link_resolucion IS NOT NULL AND link_resolucion != '')
            LIMIT 1";
    
    $stmt = $mysqli->prepare($sql);
    $evento_like = "%" . $evento . "%";
    $stmt->bind_param('s', $evento_like);
    $stmt->execute();
    $stmt->bind_result($link_existente);
    $stmt->fetch();
    $stmt->close();
    
    return !empty($link_existente);
}

function actualizarPorCedulaYEvento($mysqli, $cedula, $data) {
    $ids = [];
    
    // VERIFICAR PRIMERO SI YA TIENE LINK
    if (tieneLinkResolucion($mysqli, $cedula, $data['evento_clave'])) {
        $ids[] = "cedula_ya_tiene_link_" . $cedula;
        return $ids;
    }
    
    $sql = "UPDATE comision_academica 
            SET No_resolucion = ?, fecha_resolucion = ?, link_resolucion = ?
            WHERE documento = ? AND evento LIKE ? 
            AND (link_resolucion IS NULL OR link_resolucion = '')";
    
    $stmt = $mysqli->prepare($sql);
    $evento_like = "%" . $data['evento_clave'] . "%";
    
    $stmt->bind_param('sssss', 
        $data['No_resolucion'],
        $data['fecha_resolucion'], 
        $data['link_resolucion'],
        $cedula,
        $evento_like
    );
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $ids[] = "cedula_actualizada_" . $cedula;
        } else {
            $ids[] = "cedula_sin_cambios_" . $cedula;
        }
    }
    $stmt->close();
    
    return $ids;
}

function actualizarPorNombreYEvento($mysqli, $nombre, $data) {
    $ids = [];
    
    if (tieneLinkResolucionPorNombre($mysqli, $nombre, $data['evento_clave'])) {
        $ids[] = "nombre_ya_tiene_link_" . $nombre;
        return $ids;
    }
    
    $sql = "UPDATE comision_academica ca
            JOIN tercero t ON ca.documento = t.documento_tercero
            SET ca.No_resolucion = ?, ca.fecha_resolucion = ?, ca.link_resolucion = ?
            WHERE t.nombre_completo LIKE ? AND ca.evento LIKE ? 
            AND (ca.link_resolucion IS NULL OR ca.link_resolucion = '')";
    
    $stmt = $mysqli->prepare($sql);
    $nombre_like = "%" . $nombre . "%";
    $evento_like = "%" . $data['evento_clave'] . "%";
    
    $stmt->bind_param('sssss',
        $data['No_resolucion'],
        $data['fecha_resolucion'],
        $data['link_resolucion'], 
        $nombre_like,
        $evento_like
    );
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $ids[] = "nombre_actualizado_" . $nombre;
        } else {
            $ids[] = "nombre_sin_cambios_" . $nombre;
        }
    }
    $stmt->close();
    
    return $ids;
}

function actualizarPorEvento($mysqli, $data) {
    $ids = [];
    
    if (tieneLinkResolucionPorEvento($mysqli, $data['evento_clave'])) {
        $ids[] = "evento_ya_tiene_link_" . $data['evento_clave'];
        return $ids;
    }
    
    $sql = "UPDATE comision_academica 
            SET No_resolucion = ?, fecha_resolucion = ?, link_resolucion = ?
            WHERE evento LIKE ? 
            AND (No_resolucion IS NULL OR No_resolucion = '')
            AND (link_resolucion IS NULL OR link_resolucion = '')";
    
    $stmt = $mysqli->prepare($sql);
    $evento_like = "%" . $data['evento_clave'] . "%";
    
    $stmt->bind_param('ssss',
        $data['No_resolucion'],
        $data['fecha_resolucion'],
        $data['link_resolucion'],
        $evento_like
    );
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $ids[] = "evento_actualizado_" . $data['evento_clave'];
        } else {
            $ids[] = "evento_sin_cambios_" . $data['evento_clave'];
        }
    }
    $stmt->close();
    
    return $ids;
}

function getEstrategiaUsada($data, $updated_ids) {
    if (!empty($data['cedulas']) && count($updated_ids) > 0) {
        return "coincidencia_cedula_evento";
    } elseif (!empty($data['nombres']) && count($updated_ids) > 0) {
        return "coincidencia_nombre_evento"; 
    } elseif (count($updated_ids) > 0) {
        return "coincidencia_evento";
    } else {
        return "sin_coincidencia";
    }
}
?>