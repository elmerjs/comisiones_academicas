<?php
// Agregar al inicio del script
$log_file = 'procesamiento.log';
$timestamp = date('Y-m-d H:i:s');
file_put_contents($log_file, "=== INICIANDO PROCESO: $timestamp ===\n", FILE_APPEND);

// Tu código actual...
echo "=========================================\n";
echo "🔄 PROCESADOR CON VERIFICACIÓN ESTRICTA DE LINKS\n";
echo "📅 " . date('Y-m-d H:i:s') . "\n"; 
echo "=========================================\n\n";

// Al final del script
file_put_contents($log_file, "=== PROCESO COMPLETADO: $timestamp ===\n\n", FILE_APPEND);
header('Content-Type: text/plain; charset=utf-8');

// Configuración
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); 
define('DB_NAME', 'comisiones_academicas');
define('SHEET_ID', '1wk4U_df-cYztahf_n_o1zBp_hsI_cOGkGpJOliFxtFc');

function leerGoogleSheets() {
    $url = "https://docs.google.com/spreadsheets/d/" . SHEET_ID . "/export?format=csv";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        return ["error" => "Error HTTP: " . $http_code];
    }
    
    // GUARDAR TEMPORALMENTE Y USAR fgetcsv (más robusto)
    $temp_file = tempnam(sys_get_temp_dir(), 'csv');
    file_put_contents($temp_file, $response);
    
    $datos = [];
    if (($handle = fopen($temp_file, "r")) !== FALSE) {
        while (($fila = fgetcsv($handle, 10000, ",")) !== FALSE) {
            if (count($fila) >= 8 && !empty(trim($fila[0]))) {
                $datos[] = $fila;
            }
        }
        fclose($handle);
    }
    
    unlink($temp_file); // Limpiar archivo temporal
    
    echo "📊 FILAS PROCESADAS CON fgetcsv: " . count($datos) . "\n";
    
    // DEBUG: Mostrar algunas filas
    for ($i = 0; $i < min(10, count($datos)); $i++) {
        if (isset($datos[$i][6]) && strpos($datos[$i][6], 'COLLAZOS') !== false) {
            echo "✅ COLLAZOS ENCONTRADO en fila $i\n";
        }
    }
    
    return $datos;
}
function diagnosticarFilas($datosSheets) {
    echo "🔍 DIAGNÓSTICO DETALLADO DE FILAS:\n";
    echo "==================================\n";
    
    for ($i = 0; $i < count($datosSheets); $i++) {
        $fila = $datosSheets[$i];
        $num_columnas = count($fila);
        
        echo "Fila $i: $num_columnas columnas\n";
        
        // Mostrar contenido de las primeras 8 columnas clave
        for ($j = 0; $j < min(8, $num_columnas); $j++) {
            $valor_limpio = substr(trim($fila[$j]), 0, 30);
            echo "   Col$j: '$valor_limpio'\n";
        }
        
        if ($num_columnas > 8) {
            echo "   ... y " . ($num_columnas - 8) . " columnas más\n";
        }
        
        // Verificar si es fila válida
        if ($i === 0) {
            echo "   👉 ENCABEZADO\n";
        } elseif ($num_columnas >= 8 && !empty($fila[1]) && !empty($fila[4])) {
            echo "   👉 ✅ VÁLIDA para procesar\n";
        } else {
            echo "   👉 ❌ INCOMPLETA o inválida\n";
        }
        
        echo "\n";
    }
    echo "==================================\n\n";
}

function limpiarEvento($evento) {
    if (empty($evento)) return '';
    
    // 1. Convertir a minúsculas para consistencia
    $evento = mb_strtolower($evento, 'UTF-8');
    
    // 2. **NUEVO: Corregir errores comunes de tipeo ANTES de limpiar**
    $evento = str_replace('intemacional', 'internacional', $evento);
    $evento = str_replace('intemacione', 'internacional', $evento);
    $evento = str_replace('intemacionales', 'internacional', $evento);
    
    // 3. **NUEVO: Corregir números romanos mal escritos**
    $evento = preg_replace('/\bll\b/', 'ii', $evento);
    $evento = preg_replace('/\blll\b/', 'iii', $evento);
    $evento = preg_replace('/\biiii\b/', 'iv', $evento);
    
    // 4. Remover saltos de línea y tabs
    $evento = str_replace(["\n", "\r", "\t"], ' ', $evento);
    
    // 5. Remover comillas dobles múltiples
    $evento = preg_replace('/"+/', '"', $evento);
    
    // 6. Remover comillas al inicio y final
    $evento = trim($evento, '" ');
    
    // 7. REMOVER "EVENTO:" Y VARIANTES - CLAVE PARA LA SOLUCIÓN
    $evento = preg_replace('/^(evento\s*:?\s*|el\s+evento\s*:?\s*|las\s+|los\s+|la\s+|el\s+|a\s+|de\s+)/i', '', $evento);
    
    // 8. Remover espacios múltiples
    $evento = preg_replace('/\s+/', ' ', $evento);
    
    // 9. Limpiar nuevamente
    $evento = trim($evento);
    
    return $evento;
}

function limpiarNombre($nombre) {
    if (empty($nombre)) return '';
    
    // Remover texto adicional común después del nombre
    $patrones = [
        '/\s+con\s+Cargo\s+Académico.*$/i',
        '/\s+con\s+Cargo\s+Administrativo.*$/i',
        '/\s+-\s+.*$/',
        '/\s+\(.*\)$/',
        '/\s+con\s+.*$/i'
    ];
    
    foreach ($patrones as $patron) {
        $nombre = preg_replace($patron, '', $nombre);
    }
    
    // Limpiar espacios y saltos de línea
    $nombre = str_replace(["\n", "\r", "\t"], ' ', $nombre);
    $nombre = preg_replace('/\s+/', ' ', $nombre);
    $nombre = trim($nombre);
    
    return $nombre;
}
function extraerFrasesClave($evento) {
    if (empty($evento)) return '';
    
    // Frases comunes a buscar
    $frases_clave = [];
    
    // Buscar patrones específicos
    if (preg_match('/(\d+\s+[A-Za-z]+\s+[A-Za-z]+)/', $evento, $matches)) {
        $frases_clave[] = $matches[1]; // Ej: "51 Conferencia Latinoamericana"
    }
    
    if (preg_match('/([A-Z]{4,}\s+\d+)/', $evento, $matches)) {
        $frases_clave[] = $matches[1]; // Ej: "CLEI 2025"
    }
    
    // Si no encuentra patrones específicos, tomar primeras palabras
    if (empty($frases_clave)) {
        $palabras = explode(' ', $evento);
        $frases_clave[] = implode(' ', array_slice($palabras, 0, 4));
    }
    
    return $frases_clave[0];
}
function extraerPalabrasClave($evento) {
    if (empty($evento)) return '';
    
    // Usar el evento ya limpiado (sin "evento:")
    $evento_limpio = limpiarEvento($evento);
    
    $palabras = explode(' ', $evento_limpio);
    $palabras_clave = [];
    
    // Palabras a excluir (artículos, preposiciones)
    $excluir = ['las', 'los', 'la', 'el', 'de', 'en', 'y', 'a', 'con', 'para', 'por', 'del', 'al'];
    
    foreach ($palabras as $palabra) {
        $palabra = trim($palabra);
        if (!in_array(strtolower($palabra), $excluir) && strlen($palabra) > 2) {
            $palabras_clave[] = $palabra;
        }
    }
    
    // Tomar las 4-5 palabras más importantes
    $palabras_clave = array_slice($palabras_clave, 0, 5);
    return implode(' ', $palabras_clave);
}
function buscarDocumentoPorNombreFlexible($nombre, $mysqli) {
    if (empty($nombre)) return null;
    
    $nombre_limpio = limpiarNombre($nombre);
    $palabras = explode(' ', $nombre_limpio);
    $palabras = array_filter($palabras, function($p) { return strlen($p) > 2; });
    
    if (count($palabras) < 2) return null;
    
    // Construir consulta flexible
    $condiciones = [];
    $params = [];
    $types = '';
    
    foreach ($palabras as $palabra) {
        $condiciones[] = "nombre_completo LIKE ?";
        $params[] = "%" . $palabra . "%";
        $types .= 's';
    }
    
    $sql = "SELECT documento_tercero FROM tercero 
            WHERE " . implode(' AND ', $condiciones) . " 
            LIMIT 1";
    
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) return null;
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->bind_result($documento);
    $stmt->fetch();
    $stmt->close();
    
    return $documento;
}
function debugBusquedaNombres($nombre_buscar, $mysqli) {
    echo "=== 🔍 DEBUG BÚSQUEDA NOMBRES ===\n";
    echo "Buscando: '$nombre_buscar'\n";
    
    $sql = "SELECT nombre_completo, documento_tercero 
            FROM tercero 
            WHERE nombre_completo LIKE '%COLLAZOS%' 
            OR nombre_completo LIKE '%CESAR%'
            OR nombre_completo LIKE '%ALBERTO%'
            ORDER BY nombre_completo";
    
    $result = $mysqli->query($sql);
    if ($result && $result->num_rows > 0) {
        echo "Nombres encontrados en BD:\n";
        while ($row = $result->fetch_assoc()) {
            $coincide = similar_text($nombre_buscar, $row['nombre_completo'], $percent);
            echo "   📋 '{$row['nombre_completo']}' -> {$row['documento_tercero']} ($percent% similitud)\n";
        }
    } else {
        echo "❌ No se encontraron nombres similares\n";
    }
    echo "=== FIN DEBUG ===\n\n";
}
function debugCoincidenciaCedula($cedula_sheet, $mysqli) {
    echo "🔍 DEBUG CÉDULA: Buscando '$cedula_sheet%' en BD\n";
    
    // Buscar TODAS las coincidencias por cédula
    $sql = "SELECT documento, evento, No_resolucion, link_resolucion 
            FROM comision_academica 
            WHERE documento LIKE ?";
    
    $stmt = $mysqli->prepare($sql);
    $cedula_like = $cedula_sheet . "%";
    
    $stmt->bind_param('s', $cedula_like);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $coincidencias = 0;
    $coincidencias_sin_link = 0;
    
    while ($row = $result->fetch_assoc()) {
        $coincidencias++;
        $tiene_link = !empty($row['link_resolucion']) && trim($row['link_resolucion']) !== '';
        
        echo "   " . ($tiene_link ? "🔗" : "✅") . " COINCIDENCIA #$coincidencias:\n";
        echo "      📄 Cédula BD: {$row['documento']}\n";
        echo "      📝 Evento BD: '{$row['evento']}'\n";
        echo "      🔗 Link BD: '" . ($row['link_resolucion'] ?: 'VACÍO') . "'\n";
        echo "      🔢 Resolución BD: '" . ($row['No_resolucion'] ?: 'VACÍO') . "'\n";
        
        if (!$tiene_link) {
            $coincidencias_sin_link++;
        }
    }
    
    if ($coincidencias == 0) {
        echo "   ❌ NO HAY COINCIDENCIAS que comiencen con '$cedula_sheet'\n";
    } else {
        echo "   📊 RESUMEN: $coincidencias_sin_link de $coincidencias coincidencias SIN link\n";
    }
    
    $stmt->close();
    return ['total' => $coincidencias, 'sin_link' => $coincidencias_sin_link];
}

function verificarRegistrosEvento($evento_limpio, $evento_palabras, $mysqli) {
    echo "🔍 VERIFICANDO REGISTROS POR EVENTO:\n";
    
    $sql = "SELECT COUNT(*) as total, 
                   SUM(CASE WHEN link_resolucion IS NULL OR link_resolucion = '' THEN 1 ELSE 0 END) as sin_link
            FROM comision_academica 
            WHERE (evento LIKE ? OR evento LIKE ? OR evento LIKE ?)";
    
    $stmt = $mysqli->prepare($sql);
    $evento_like = "%" . $evento_limpio . "%";
    $evento_palabras_like = "%" . $evento_palabras . "%";
    $evento_partial_like = "%" . substr($evento_limpio, 0, 15) . "%";
    
    $stmt->bind_param('sss', $evento_like, $evento_palabras_like, $evento_partial_like);
    $stmt->execute();
    $stmt->bind_result($total, $sin_link);
    $stmt->fetch();
    $stmt->close();
    
    echo "   📊 Total registros encontrados: $total\n";
    echo "   ✅ Registros SIN link: $sin_link\n";
    echo "   ❌ Registros CON link: " . ($total - $sin_link) . "\n";
    
    return ['total' => $total, 'sin_link' => $sin_link];
}

function actualizarMySQL($fila) {
    // Columnas del sheet (ajustadas a la nueva estructura por profesor):
    // 0: Timestamp
    // 1: No_resolucion
    // 2: fecha_resolucion
    // 3: link_resolucion
    // 4: evento_clave
    // 5: Cedula_Profesor (Individual, puede ser 'N/A') <-- NUEVA CLAVE
    // 6: Nombre_Profesor (Individual)
    // 7: Estado
    
    // Conexión a la base de datos
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        return ["success" => false, "message" => "Error de conexión: " . $mysqli->connect_error];
    }
    
    $no_resolucion = trim($fila[1] ?? '');
    $fecha_resolucion = trim($fila[2] ?? '');
    $link_resolucion = trim($fila[3] ?? '');
    $evento = trim($fila[4] ?? '');
    $cedulas_str = trim($fila[5] ?? ''); // Cedula individual (puede ser 'N/A')
    $nombres_str = trim($fila[6] ?? ''); // Nombre individual
    $estado = trim($fila[7] ?? '');
    
    // Verificación de datos mínimos y estado
    if (empty($no_resolucion) || empty($link_resolucion) || $estado !== 'PENDIENTE') {
        $mysqli->close();
        return ["success" => true, "actualizados" => 0, "message" => "Fila no procesable (sin resolución, sin link, o estado no es PENDIENTE)"];
    }

    // Preparación de datos
    $cedulas = [];
    if ($cedulas_str !== 'N/A' && !empty($cedulas_str)) {
        // Si no es 'N/A', tratamos la cédula como individual
        $cedulas = [str_replace('.', '', $cedulas_str)]; 
    }
    
    // Preparación de nombres (aunque solo debería haber uno)
    $nombres_sin_limpiar = array_filter(array_map('trim', explode(',', $nombres_str)));
    $nombres = [];
    foreach ($nombres_sin_limpiar as $nombre) {
        $nombre_limpio = limpiarNombre($nombre);
        if (!empty($nombre_limpio)) {
            $nombres[] = $nombre_limpio;
        }
    }

    $actualizados = 0;
    $detalle = [];
    $estrategia = "ninguna";

    // LIMPIAR EVENTO PARA MEJOR COINCIDENCIA
    // Se asume la existencia de las funciones limpiarEvento y extraerPalabrasClave
    $evento_limpio = limpiarEvento($evento);
    $evento_palabras = extraerPalabrasClave($evento_limpio);

    echo "🎯 Evento limpio: $evento_limpio\n";
    echo "🔑 Palabras clave: $evento_palabras\n";

    // ======================================================================
    // ESTRATEGIA 0: RESCATE (Buscar Cédula por Nombre si la hoja de cálculo devuelve 'N/A')
    // ======================================================================
    if (empty($cedulas) && !empty($nombres)) {
        echo "⚠️ Cédula 'N/A' detectada. Intentando buscar documento por nombre: {$nombres[0]}...\n";
        
        $nombre_a_buscar = $nombres[0]; // Usamos el primer nombre
        // Buscamos el documento en la tabla 'tercero'
        $sql_lookup = "SELECT documento_tercero FROM tercero WHERE nombre_completo LIKE ? LIMIT 1";
        $stmt_lookup = $mysqli->prepare($sql_lookup);
        
        if ($stmt_lookup) {
            // Usamos LIKE con el nombre limpio y completo
            $nombre_like = "%" . $nombre_a_buscar . "%";
            $stmt_lookup->bind_param('s', $nombre_like);
            $stmt_lookup->execute();
            $stmt_lookup->bind_result($documento_encontrado);
            
            if ($stmt_lookup->fetch()) {
                // Sobrescribir la variable $cedulas para que la Estrategia 1 funcione
                $cedulas = [trim($documento_encontrado)];
                echo "✅ Cédula encontrada en 'tercero': {$cedulas[0]}. Re-ejecutando Estrategia 1.\n";
            } else {
                echo "❌ Cédula no encontrada en 'tercero' para '$nombre_a_buscar'. Continuamos con Estrategia 2/3 como respaldo.\n";
            }
            $stmt_lookup->close();
        } else {
             echo "❌ Error preparando consulta de lookup: " . $mysqli->error . "\n";
        }
    }


    // ======================================================================
    // ESTRATEGIA 1: Por cédulas (más precisa, usa la cédula del sheet o la rescatada en Paso 0)
    // ======================================================================
    if (!empty($cedulas)) {
        foreach ($cedulas as $cedula) {
            if (empty($cedula)) continue;

            echo "🔎 Buscando cédula: $cedula\n";
            
            // Verificamos si hay coincidencias sin link para evitar UPDATES innecesarios
            $coincidencias_debug = debugCoincidenciaCedula($cedula, $mysqli); 
            
            if ($coincidencias_debug['sin_link'] > 0) {
                // Preparamos la actualización
                $sql = "UPDATE comision_academica 
                        SET No_resolucion = ?, fecha_resolucion = ?, link_resolucion = ?
                        WHERE documento LIKE ? 
                        AND (evento LIKE ? OR evento LIKE ? OR evento LIKE ?)
                        AND (link_resolucion IS NULL OR link_resolucion = '')";

                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    echo "❌ Error preparando consulta: " . $mysqli->error . "\n";
                    continue;
                }

                $cedula_like = $cedula . "%";
                $evento_like = "%" . $evento_limpio . "%";
                $evento_palabras_like = "%" . $evento_palabras . "%";
                $evento_partial_like = "%" . substr($evento_limpio, 0, 15) . "%";

                $stmt->bind_param('sssssss', 
                    $no_resolucion, 
                    $fecha_resolucion, 
                    $link_resolucion, 
                    $cedula_like,
                    $evento_like, 
                    $evento_palabras_like,
                    $evento_partial_like
                );

                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $actualizados += $stmt->affected_rows;
                        $detalle[] = "Cédula $cedula: " . $stmt->affected_rows . " registros";
                        $estrategia = "cedulas";
                        echo "✅ ACTUALIZADO - Cédula: $cedula ({$stmt->affected_rows} registros)\n";
                    } else {
                        echo "ℹ️  Cédula $cedula: Coincidencias encontradas pero YA TIENEN link o no hay coincidencia de evento.\n";
                    }
                } else {
                    echo "❌ Error ejecutando consulta: " . $stmt->error . "\n";
                }
                $stmt->close();

            } else {
                echo "ℹ️  Cédula $cedula: No hay coincidencias SIN link para actualizar\n";
            }
        }
    }

    // ======================================================================
    // ESTRATEGIA 2: Por nombres (solo se ejecuta si Estrategia 1 falló totalmente)
    // ======================================================================
   // ESTRATEGIA 2: Por nombres con búsqueda flexible
if ($actualizados == 0 && !empty($nombres)) {
    foreach ($nombres as $nombre) {
        if (empty($nombre)) continue;
        
        echo "🔍 Buscando documento para: '$nombre'\n";
        
        // BUSCAR DOCUMENTO CON BÚSQUEDA FLEXIBLE
        $documento_encontrado = buscarDocumentoPorNombreFlexible($nombre, $mysqli);
        
        if ($documento_encontrado) {
            echo "✅ Documento encontrado: $documento_encontrado para '$nombre'\n";
            
            // ACTUALIZAR usando el documento encontrado
            $sql = "UPDATE comision_academica 
                    SET No_resolucion = ?, fecha_resolucion = ?, link_resolucion = ?
                    WHERE documento = ?
                    AND (evento LIKE ? OR evento LIKE ? OR evento LIKE ?)
                    AND (link_resolucion IS NULL OR link_resolucion = '')";
            
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "❌ Error preparando consulta: " . $mysqli->error . "\n";
                continue;
            }
            
            $evento_like = "%" . $evento_limpio . "%";
            $evento_palabras_like = "%" . $evento_palabras . "%";
            $evento_partial_like = "%" . substr($evento_limpio, 0, 15) . "%";
            
            $stmt->bind_param('sssssss',
                $no_resolucion,
                $fecha_resolucion,
                $link_resolucion,
                $documento_encontrado,
                $evento_like,
                $evento_palabras_like,
                $evento_partial_like
            );
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $actualizados += $stmt->affected_rows;
                    $detalle[] = "Nombre $nombre (doc: $documento_encontrado): " . $stmt->affected_rows . " registros";
                    $estrategia = "nombres_flexible";
                    echo "✅ ACTUALIZADO - Nombre: $nombre ({$stmt->affected_rows} registros)\n";
                } else {
                    echo "ℹ️  Nombre $nombre: No se pudo actualizar (ya tiene link o no coincide evento)\n";
                }
            } else {
                echo "❌ Error ejecutando consulta: " . $stmt->error . "\n";
            }
            $stmt->close();
        } else {
            echo "❌ No se encontró documento para: '$nombre'\n";
            
            // DEBUG: Ver qué nombres hay en la BD similares
            $sql_debug = "SELECT nombre_completo, documento_tercero FROM tercero 
                         WHERE nombre_completo LIKE '%COLLAZOS%' 
                         OR nombre_completo LIKE '%CESAR%' 
                         LIMIT 5";
            $result = $mysqli->query($sql_debug);
            if ($result && $result->num_rows > 0) {
                echo "   🔍 Nombres similares en BD:\n";
                while ($row = $result->fetch_assoc()) {
                    echo "      👤 '{$row['nombre_completo']}' -> {$row['documento_tercero']}\n";
                }
            }
        }
    }
}
    
    // ======================================================================
    // ESTRATEGIA 3: Solo por evento (ÚLTIMO RECURSO y solo si solo hay un profesor en la fila)
    // Se asume que el evento es muy único.
    // ======================================================================
    if ($actualizados == 0) {
        $estrategia = "solo_evento";
        echo "🔄 Estrategia 3 (Solo Evento) activada.\n";
        
        // Buscamos solo por coincidencia de evento
        $sql = "UPDATE comision_academica 
                SET No_resolucion = ?, fecha_resolucion = ?, link_resolucion = ?
                WHERE evento LIKE ? 
                AND (link_resolucion IS NULL OR link_resolucion = '')";
        
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            echo "❌ Error preparando consulta de evento: " . $mysqli->error . "\n";
        } else {
            $evento_like = "%" . $evento_limpio . "%";

            $stmt->bind_param('ssss', 
                $no_resolucion, 
                $fecha_resolucion, 
                $link_resolucion, 
                $evento_like
            );

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $actualizados += $stmt->affected_rows;
                    $detalle[] = "Evento: " . $stmt->affected_rows . " registros";
                    echo "✅ ACTUALIZADO - Evento: $evento_limpio ({$stmt->affected_rows} registros)\n";
                }
            } else {
                echo "❌ Error ejecutando consulta de evento: " . $stmt->error . "\n";
            }
            $stmt->close();
        }
    }
    
    // Si se actualizó, también actualizamos el estado en Sheets (No se hace aquí, se hace en el bucle principal)
    
    $mysqli->close();

    return [
        "success" => true, 
        "actualizados" => $actualizados, 
        "estrategia" => $estrategia, 
        "detalle" => $detalle,
        "message" => $actualizados > 0 ? "Actualización exitosa" : "No se encontró registro sin link para actualizar."
    ];
}


// EJECUCIÓN PRINCIPAL
echo "=========================================\n";
echo "🔄 PROCESADOR CON VERIFICACIÓN ESTRICTA DE LINKS\n";
echo "📅 " . date('Y-m-d H:i:s') . "\n";
echo "=========================================\n\n";

try {
    $datosSheets = leerGoogleSheets();

    if (isset($datosSheets['error'])) {
        echo "❌ ERROR: " . $datosSheets['error'] . "\n";
        exit(1);
    }

    if ($datosSheets && count($datosSheets) > 0) {
        // EJECUTAR DIAGNÓSTICO DETALLADO
        diagnosticarFilas($datosSheets);
        
        $totalProcesadas = 0;
        $totalActualizadas = 0;
        
        echo "📊 Filas en Sheet: " . count($datosSheets) . "\n\n";
        
        for ($i = 1; $i < count($datosSheets); $i++) {
           
            $fila = $datosSheets[$i];
            
            if (count($fila) < 8 || empty($fila[1]) || empty($fila[4])) {
                echo "⏭️ Fila $i incompleta o inválida, saltando...\n\n";
                continue;
            }
            
            echo "--- Procesando fila $i ---\n";
            $resultado = actualizarMySQL($fila);
            
            if (isset($resultado['success']) && $resultado['success']) {
                $actualizados = isset($resultado['actualizados']) ? $resultado['actualizados'] : 0;
                $estrategia = isset($resultado['estrategia']) ? $resultado['estrategia'] : 'desconocida';
                
                echo "✅ $actualizados registros actualizados\n";
                echo "🎯 Estrategia: $estrategia\n";
                
                if (isset($resultado['detalle']) && !empty($resultado['detalle'])) {
                    foreach ($resultado['detalle'] as $det) {
                        echo "   📝 $det\n";
                    }
                }
                $totalActualizadas += $actualizados;
            } else {
                $message = isset($resultado['message']) ? $resultado['message'] : 'Error desconocido';
                echo "❌ $message\n";
            }
            
            $totalProcesadas++;
            echo "\n";
        }
        
        echo "=========================================\n";
        echo "🎉 RESUMEN EJECUCIÓN:\n";
        echo "📊 Filas procesadas: $totalProcesadas\n";
        echo "✅ Registros actualizados: $totalActualizadas\n";
        echo "🕒 Finalizado: " . date('Y-m-d H:i:s') . "\n";
        echo "=========================================\n";
        
    } else {
        echo "❌ No se pudieron leer los datos de Google Sheets\n";
    }
    
} catch (Exception $e) {
    echo "💥 ERROR CRÍTICO: " . $e->getMessage() . "\n";
}