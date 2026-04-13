<?php
// 1. Definir ruta absoluta para el log (Solución al Permission Denied)
$log_file = __DIR__ . '/procesamiento.log';
$timestamp = date('Y-m-d H:i:s');

// Intentar escribir el log (el @ silencia el error si aún hubiera problemas de permiso)
@file_put_contents($log_file, "=== INICIANDO PROCESO: $timestamp ===\n", FILE_APPEND);

echo "=========================================\n";
echo "🔄 PROCESADOR CON VERIFICACIÓN ESTRICTA DE LINKS\n";
echo "📅 " . date('Y-m-d H:i:s') . "\n"; 
echo "=========================================\n\n";

// 2. Solo enviar cabeceras si NO es consola (Evita el "Headers already sent")
if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}
// Configuración
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); 
define('DB_NAME', 'comisiones_academicas');
//define('SHEET_ID', '1wk4U_df-cYztahf_n_o1zBp_hsI_cOGkGpJOliFxtFc');este es con ejurado
define('SHEET_ID', '1LIIiWzdr-62cwEbwFhf3DYAPSNAqKa9ezuecgbFntdA');//con comsionesacademicas@
// --- Funciones auxiliares ---
function convertirFechaMySQL($fecha) {
    if (empty($fecha) || $fecha === 'N/D') return null;
    $partes = explode('-', $fecha);
    if (count($partes) !== 3) return null;
    $dia = $partes[0];
    $mes = $partes[1];
    $anio = $partes[2];
    if (!checkdate($mes, $dia, $anio)) return null;
    return "$anio-$mes-$dia";
}

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
    if ($http_code !== 200) return ["error" => "Error HTTP: " . $http_code];

    $temp_file = tempnam(sys_get_temp_dir(), 'csv');
    file_put_contents($temp_file, $response);
    $datos = [];
    if (($handle = fopen($temp_file, "r")) !== FALSE) {
        while (($fila = fgetcsv($handle, 10000, ",")) !== FALSE) {
            if (count($fila) >= 8 && !empty(trim($fila[0]))) $datos[] = $fila;
        }
        fclose($handle);
    }
    unlink($temp_file);
    echo "📊 FILAS PROCESADAS CON fgetcsv: " . count($datos) . "\n";
    return $datos;
}

function diagnosticarFilas($datosSheets) {
    echo "🔍 DIAGNÓSTICO DETALLADO DE FILAS:\n==================================\n";
    for ($i = 0; $i < count($datosSheets); $i++) {
        $fila = $datosSheets[$i];
        $num_columnas = count($fila);
        echo "Fila $i: $num_columnas columnas\n";
        for ($j = 0; $j < min(8, $num_columnas); $j++) {
            echo "   Col$j: '" . substr(trim($fila[$j]), 0, 30) . "'\n";
        }
        if ($num_columnas > 8) echo "   ... y " . ($num_columnas - 8) . " columnas más\n";
        if ($i === 0) echo "   👉 ENCABEZADO\n";
        elseif ($num_columnas >= 8 && !empty($fila[1]) && !empty($fila[4])) echo "   👉 ✅ VÁLIDA para procesar\n";
        else echo "   👉 ❌ INCOMPLETA o inválida\n";
        echo "\n";
    }
    echo "==================================\n\n";
}

function limpiarEvento($evento) {
    if (empty($evento)) return '';
    $evento = mb_strtolower($evento, 'UTF-8');
    $evento = str_replace(['intemacional', 'intemacione', 'intemacionales'], 'internacional', $evento);
    $evento = preg_replace(['/\bll\b/', '/\blll\b/', '/\biiii\b/'], ['ii', 'iii', 'iv'], $evento);
    $evento = str_replace(["\n", "\r", "\t"], ' ', $evento);
    $evento = preg_replace('/"+/', '"', $evento);
    $evento = trim($evento, '" ');
    $evento = preg_replace('/^(evento\s*:?\s*|el\s+evento\s*:?\s*|las\s+|los\s+|la\s+|el\s+|a\s+|de\s+)/i', '', $evento);
    $evento = preg_replace('/\s+/', ' ', $evento);
    return trim($evento);
}

function limpiarNombre($nombre) {
    if (empty($nombre)) return '';
    
    // 1. Eliminar todo lo que esté antes de ":" (incluyendo el ":")
    if (strpos($nombre, ':') !== false) {
        $partes = explode(':', $nombre, 2);
        $nombre = trim(end($partes));
    }
    
    // 2. Patrones para limpiar títulos y partes sobrantes
    $patrones = [
        '/\s+con\s+Cargo\s+Académico.*$/i',
        '/\s+con\s+Cargo\s+Administrativo.*$/i',
        '/\s+-\s+.*$/',
        '/\s+\(.*\)$/',
        '/\s+con\s+.*$/i',
        '/^DE\s+LA\s+FACULTAD\s+DE\s+[A-ZÁÉÍÓÚÑ\s]+:?\s*/i',
        '/^DE\s+LA\s+FACULTAD\s+DE\s+.*?:?\s*/i'
    ];
    
    foreach ($patrones as $patron) {
        $nombre = preg_replace($patron, '', $nombre);
    }
    
    // 3. Limpiar espacios y saltos de línea
    $nombre = str_replace(["\n", "\r", "\t"], ' ', $nombre);
    $nombre = preg_replace('/\s+/', ' ', $nombre);
    return trim($nombre);
}

function extraerPalabrasClave($evento) {
    if (empty($evento)) return '';
    $evento_limpio = limpiarEvento($evento);
    $palabras = explode(' ', $evento_limpio);
    $excluir = ['las', 'los', 'la', 'el', 'de', 'en', 'y', 'a', 'con', 'para', 'por', 'del', 'al'];
    $clave = [];
    foreach ($palabras as $p) {
        $p = trim($p);
        if (!in_array(strtolower($p), $excluir) && strlen($p) > 2) $clave[] = $p;
    }
    return implode(' ', array_slice($clave, 0, 5));
}

function buscarDocumentoPorNombreFlexible($nombre, $mysqli) {
    if (empty($nombre)) return null;
    $nombre_limpio = limpiarNombre($nombre);
    $palabras = array_filter(explode(' ', $nombre_limpio), function($p) { return strlen($p) > 2; });
    if (count($palabras) < 2) return null;
    $condiciones = []; $params = []; $types = '';
    foreach ($palabras as $p) {
        $condiciones[] = "nombre_completo LIKE ?";
        $params[] = "%$p%";
        $types .= 's';
    }
    $sql = "SELECT documento_tercero FROM tercero WHERE " . implode(' AND ', $condiciones) . " LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) return null;
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->bind_result($doc);
    $stmt->fetch();
    $stmt->close();
    return $doc;
}

function debugCoincidenciaCedula($cedula_sheet, $mysqli) {
    echo "🔍 DEBUG CÉDULA: Buscando '$cedula_sheet%' en BD\n";
    $sql = "SELECT documento, evento, No_resolucion, link_resolucion FROM comision_academica WHERE documento LIKE ?";
    $stmt = $mysqli->prepare($sql);
    $cedula_like = $cedula_sheet . "%";
    $stmt->bind_param('s', $cedula_like);
    $stmt->execute();
    $result = $stmt->get_result();
    $coincidencias = 0;
    $sin_link = 0;
    while ($row = $result->fetch_assoc()) {
        $coincidencias++;
        $tiene_link = !empty($row['link_resolucion']) && trim($row['link_resolucion']) !== '';
        echo "   " . ($tiene_link ? "🔗" : "✅") . " COINCIDENCIA #$coincidencias:\n";
        echo "      📄 Cédula BD: {$row['documento']}\n";
        echo "      📝 Evento BD: '{$row['evento']}'\n";
        echo "      🔗 Link BD: '" . ($row['link_resolucion'] ?: 'VACÍO') . "'\n";
        if (!$tiene_link) $sin_link++;
    }
    if ($coincidencias == 0) echo "   ❌ NO HAY COINCIDENCIAS\n";
    else echo "   📊 RESUMEN: $sin_link de $coincidencias coincidencias SIN link\n";
    $stmt->close();
    return ['total' => $coincidencias, 'sin_link' => $sin_link];
}

// ------------------------------------------------------------------
// Núcleo de actualización
// ------------------------------------------------------------------
function actualizarMySQL($fila) {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) return ["success" => false, "message" => "Error conexión: " . $mysqli->connect_error];

    $no_resolucion      = trim($fila[1] ?? '');
    $fecha_resolucion   = convertirFechaMySQL(trim($fila[2] ?? ''));
    $link_resolucion    = trim($fila[3] ?? '');
    $evento             = trim($fila[4] ?? '');
    $cedulas_str        = trim($fila[5] ?? '');
    $nombres_str        = trim($fila[6] ?? '');
    $estado             = trim($fila[7] ?? '');

    if (empty($no_resolucion) || empty($link_resolucion) || $estado !== 'PENDIENTE') {
        $mysqli->close();
        return ["success" => true, "actualizados" => 0, "message" => "Fila no procesable"];
    }

    $cedulas = [];
    if ($cedulas_str !== 'N/A' && !empty($cedulas_str)) $cedulas = [str_replace('.', '', $cedulas_str)];

    $nombres = [];
    foreach (array_filter(array_map('trim', explode(',', $nombres_str))) as $nombre) {
        $limpio = limpiarNombre($nombre);
        if (!empty($limpio)) $nombres[] = $limpio;
    }

    $actualizados = 0;
    $detalle = [];
    $estrategia = "ninguna";

    $evento_limpio = limpiarEvento($evento);
    $evento_palabras = extraerPalabrasClave($evento_limpio);

    echo "🎯 Evento limpio: $evento_limpio\n🔑 Palabras clave: $evento_palabras\n";

    // ------------------------------------------------------------------
    // Estrategia 0: Rescatar cédula desde tercero si la hoja tiene 'N/A'
    // ------------------------------------------------------------------
    if (empty($cedulas) && !empty($nombres)) {
        echo "⚠️ Cédula 'N/A' detectada. Intentando buscar documento por nombre: {$nombres[0]}...\n";
        $sql_lookup = "SELECT documento_tercero FROM tercero WHERE nombre_completo LIKE ? LIMIT 1";
        $stmt_lookup = $mysqli->prepare($sql_lookup);
        if ($stmt_lookup) {
            $nombre_like = "%{$nombres[0]}%";
            $stmt_lookup->bind_param('s', $nombre_like);
            $stmt_lookup->execute();
            $stmt_lookup->bind_result($doc);
            if ($stmt_lookup->fetch()) {
                $cedulas = [trim($doc)];
                echo "✅ Cédula encontrada en 'tercero': {$cedulas[0]}.\n";
            } else echo "❌ Cédula no encontrada.\n";
            $stmt_lookup->close();
        }
    }
    // ------------------------------------------------------------------
    // Estrategia 1: Por cédula (con evento y fecha cercana)
    // ------------------------------------------------------------------
    if (!empty($cedulas)) {
        foreach ($cedulas as $cedula) {
            if (empty($cedula)) continue;
            echo "🔎 Buscando cédula: $cedula\n";
            $coincidencias = debugCoincidenciaCedula($cedula, $mysqli);
            if ($coincidencias['sin_link'] > 0) {
                // Verificar que la fecha no sea nula
                if ($fecha_resolucion === null) {
                    echo "⚠️  Fecha de resolución no válida. Se omite estrategia de fecha cercana.\n";
                    continue;
                }

                $evento_sin_numero = preg_replace('/^\s*(i|ii|iii|iv|v|vi|vii|viii|ix|x)\s+/i', '', $evento_limpio);
                $evento_sin_numero_like = "%$evento_sin_numero%";

                $sql = "UPDATE comision_academica 
                        SET No_resolucion = ?, fecha_resolucion = ?, link_resolucion = ?
                        WHERE documento LIKE ? 
                          AND tipo_estudio = 'EXT'
                          AND (evento LIKE ? OR evento LIKE ? OR evento LIKE ? OR evento LIKE ?)
                          AND (link_resolucion IS NULL OR link_resolucion = '')
                          AND ABS(DATEDIFF(fecha_resolucion, ?)) <= 30";
                $stmt = $mysqli->prepare($sql);
                if ($stmt) {
                    $cedula_like = $cedula . "%";
                    $evento_like = "%$evento_limpio%";
                    $evento_palabras_like = "%$evento_palabras%";
                    $evento_partial_like = "%" . substr($evento_limpio, 0, 15) . "%";
                    $stmt->bind_param('sssssssss',
                        $no_resolucion, $fecha_resolucion, $link_resolucion,
                        $cedula_like, $evento_like, $evento_palabras_like,
                        $evento_partial_like, $evento_sin_numero_like,
                        $fecha_resolucion);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $actualizados += $stmt->affected_rows;
                        $detalle[] = "Cédula $cedula: " . $stmt->affected_rows . " registros";
                        $estrategia = "cedulas";
                        echo "✅ ACTUALIZADO - Cédula: $cedula ({$stmt->affected_rows})\n";
                    } else {
                        echo "ℹ️  Cédula $cedula: No se pudo actualizar.\n";
                    }
                    $stmt->close();
                } else {
                    echo "❌ Error preparando consulta.\n";
                }
            } else {
                echo "ℹ️  Cédula $cedula: No hay coincidencias SIN link.\n";
            }
        }
    }

    // ------------------------------------------------------------------
    // Estrategia 1b: Por cédula y fecha cercana (ignora evento)
    // ------------------------------------------------------------------
    if ($actualizados == 0 && !empty($cedulas) && $fecha_resolucion !== null) {
        foreach ($cedulas as $cedula) {
            if (empty($cedula)) continue;
            echo "🔎 Buscando por cédula y fecha cercana: $cedula\n";
            $sql = "UPDATE comision_academica 
                    SET No_resolucion = ?, fecha_resolucion = ?, link_resolucion = ?
                    WHERE documento LIKE ? 
                      AND tipo_estudio = 'EXT'
                      AND (No_resolucion NOT LIKE 'R-%' OR No_resolucion IS NULL OR No_resolucion = '')
                      AND (link_resolucion IS NULL OR link_resolucion = '')
                      AND ABS(DATEDIFF(fecha_resolucion, ?)) <= 25";
            $stmt = $mysqli->prepare($sql);
            if ($stmt) {
                $cedula_like = $cedula . "%";
                $stmt->bind_param('sssss', $no_resolucion, $fecha_resolucion, $link_resolucion, $cedula_like, $fecha_resolucion);
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    $actualizados += $stmt->affected_rows;
                    $detalle[] = "Cédula $cedula (fecha cercana): " . $stmt->affected_rows . " registros";
                    $estrategia = "cedula_fecha";
                    echo "✅ ACTUALIZADO - Cédula: $cedula por fecha cercana ({$stmt->affected_rows})\n";
                } else {
                    echo "ℹ️  Cédula $cedula: No se encontraron coincidencias por fecha cercana.\n";
                }
                $stmt->close();
            } else {
                echo "❌ Error preparando consulta de cédula+fecha.\n";
            }
        }
    }

        // ------------------------------------------------------------------
    // Estrategia 2: Por nombre (con evento y fecha cercana)
    // ------------------------------------------------------------------
    if ($actualizados == 0 && !empty($nombres)) {
        foreach ($nombres as $nombre) {
            if (empty($nombre)) continue;
            echo "🔍 Buscando documento para: '$nombre'\n";
            $documento = buscarDocumentoPorNombreFlexible($nombre, $mysqli);
            if ($documento) {
                echo "✅ Documento encontrado: $documento\n";
                
                // Verificar que la fecha no sea nula
                if ($fecha_resolucion === null) {
                    echo "⚠️  Fecha de resolución no válida. Se omite estrategia de fecha cercana.\n";
                    continue;
                }
                
                $evento_sin_numero = preg_replace('/^\s*(i|ii|iii|iv|v|vi|vii|viii|ix|x)\s+/i', '', $evento_limpio);
                $evento_sin_numero_like = "%$evento_sin_numero%";

                $sql = "UPDATE comision_academica 
                        SET No_resolucion = ?, fecha_resolucion = ?, link_resolucion = ?
                        WHERE documento = ?
                          AND tipo_estudio = 'EXT'
                          AND (evento LIKE ? OR evento LIKE ? OR evento LIKE ? OR evento LIKE ?)
                          AND (link_resolucion IS NULL OR link_resolucion = '')
                          AND ABS(DATEDIFF(fecha_resolucion, ?)) <= 30";
                $stmt = $mysqli->prepare($sql);
                if ($stmt) {
                    $evento_like = "%$evento_limpio%";
                    $evento_palabras_like = "%$evento_palabras%";
                    $evento_partial_like = "%" . substr($evento_limpio, 0, 15) . "%";
                    $stmt->bind_param('sssssssss',
                        $no_resolucion, $fecha_resolucion, $link_resolucion,
                        $documento, $evento_like, $evento_palabras_like,
                        $evento_partial_like, $evento_sin_numero_like,
                        $fecha_resolucion);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $actualizados += $stmt->affected_rows;
                        $detalle[] = "Nombre $nombre (doc: $documento): " . $stmt->affected_rows . " registros";
                        $estrategia = "nombres_flexible";
                        echo "✅ ACTUALIZADO - Nombre: $nombre ({$stmt->affected_rows})\n";
                    } else {
                        echo "ℹ️  Nombre $nombre: No se pudo actualizar.\n";
                        // ------------------------------------------------------------------
                        // Subestrategia 2b: Por documento (obtenido por nombre) + fecha cercana
                        // ------------------------------------------------------------------
                        if ($fecha_resolucion !== null) {
                            echo "🔎 Buscando por documento y fecha cercana: $documento\n";
                            $sql2b = "UPDATE comision_academica 
                                      SET No_resolucion = ?, fecha_resolucion = ?, link_resolucion = ?
                                      WHERE documento = ?
                                        AND tipo_estudio = 'EXT'
                                        AND (No_resolucion NOT LIKE 'R-%' OR No_resolucion IS NULL OR No_resolucion = '')
                                        AND (link_resolucion IS NULL OR link_resolucion = '')
                                        AND ABS(DATEDIFF(fecha_resolucion, ?)) <= 25";
                            $stmt2b = $mysqli->prepare($sql2b);
                            if ($stmt2b) {
                                $stmt2b->bind_param('sssss', $no_resolucion, $fecha_resolucion, $link_resolucion, $documento, $fecha_resolucion);
                                if ($stmt2b->execute() && $stmt2b->affected_rows > 0) {
                                    $actualizados += $stmt2b->affected_rows;
                                    $detalle[] = "Nombre $nombre (doc: $documento) por fecha cercana: " . $stmt2b->affected_rows . " registros";
                                    $estrategia = "nombre_fecha";
                                    echo "✅ ACTUALIZADO - Nombre: $nombre por fecha cercana ({$stmt2b->affected_rows})\n";
                                } else {
                                    echo "ℹ️  Documento $documento: No se encontraron coincidencias por fecha cercana.\n";
                                }
                                $stmt2b->close();
                            } else {
                                echo "❌ Error preparando consulta de documento+fecha.\n";
                            }
                        }
                    }
                    $stmt->close();
                } else echo "❌ Error preparando consulta.\n";
            } else echo "❌ No se encontró documento para: '$nombre'\n";
        }
    }

    // ------------------------------------------------------------------
    // Estrategia 3: Solo por evento (último recurso, con máximas restricciones)
    // ------------------------------------------------------------------
    if ($actualizados == 0) {
        // VALIDACIONES ESTRICTAS
        // 1. Evento debe tener al menos 10 caracteres y no ser solo numérico
        $evento_valido = true;
        $evento_limpio_len = strlen($evento_limpio);

        if ($evento_limpio_len < 10) {
            echo "⚠️  Evento demasiado corto ($evento_limpio_len caracteres). Se omite Estrategia 3.\n";
            $evento_valido = false;
        }

        if (is_numeric($evento_limpio)) {
            echo "⚠️  Evento es solo numérico ('$evento_limpio'). Se omite Estrategia 3.\n";
            $evento_valido = false;
        }

        // 2. Fecha debe ser válida
        if ($fecha_resolucion === null) {
            echo "⚠️  Fecha de resolución no válida. Se omite Estrategia 3.\n";
            $evento_valido = false;
        }

        if ($evento_valido) {
            $estrategia = "solo_evento";
            echo "🔄 Estrategia 3 (Solo Evento) activada.\n";
            $evento_sin_numero = preg_replace('/^\s*(i|ii|iii|iv|v|vi|vii|viii|ix|x)\s+/i', '', $evento_limpio);
            $evento_sin_numero_like = "%$evento_sin_numero%";

            $sql = "UPDATE comision_academica 
                    SET No_resolucion = ?, fecha_resolucion = ?, link_resolucion = ?
                    WHERE tipo_estudio = 'EXT'
                      AND (evento LIKE ? OR evento LIKE ? OR evento LIKE ? OR evento LIKE ?)
                      AND (link_resolucion IS NULL OR link_resolucion = '')
                      AND ABS(DATEDIFF(fecha_resolucion, ?)) <= 30
                      LIMIT 5";  // ← LÍMITE MÁXIMO DE REGISTROS POR ACTUALIZACIÓN
            $stmt = $mysqli->prepare($sql);
            if ($stmt) {
                $evento_like = "%$evento_limpio%";
                $evento_palabras_like = "%$evento_palabras%";
                $evento_partial_like = "%" . substr($evento_limpio, 0, 15) . "%";
                $stmt->bind_param('ssssssss',
                    $no_resolucion, $fecha_resolucion, $link_resolucion,
                    $evento_like, $evento_palabras_like, $evento_partial_like, $evento_sin_numero_like,
                    $fecha_resolucion);
                if ($stmt->execute()) {
                    $afectados = $stmt->affected_rows;
                    if ($afectados > 0) {
                        $actualizados += $afectados;
                        $detalle[] = "Evento: " . $afectados . " registros";
                        echo "✅ ACTUALIZADO - Evento: $evento_limpio ($afectados registros)\n";
                        if ($afectados == 5) {
                            echo "   ⚠️  Se alcanzó el límite de 5 registros. Pueden quedar más pendientes.\n";
                        }
                    } else {
                        echo "ℹ️  Evento: No se encontraron registros sin link con fecha cercana.\n";
                    }
                } else {
                    echo "❌ Error ejecutando consulta: " . $stmt->error . "\n";
                }
                $stmt->close();
            } else {
                echo "❌ Error preparando consulta de evento.\n";
            }
        }
    }

    $mysqli->close();
    return [
        "success" => true,
        "actualizados" => $actualizados,
        "estrategia" => $estrategia,
        "detalle" => $detalle,
        "message" => $actualizados > 0 ? "Actualización exitosa" : "No se encontró registro sin link."
    ];
}

// ------------------------------------------------------------------
// EJECUCIÓN PRINCIPAL CON FILTRO POR TIMESTAMP
// ------------------------------------------------------------------
try {
    $datosSheets = leerGoogleSheets();
    if (isset($datosSheets['error'])) {
        echo "❌ ERROR: " . $datosSheets['error'] . "\n";
        exit(1);
    }

    if ($datosSheets && count($datosSheets) > 0) {
        diagnosticarFilas($datosSheets);

        // Configurar filtro por fecha (últimos 3 días)
        $limite_dias = 3;
        $fecha_limite = new DateTime();
        $fecha_limite->modify("-$limite_dias days");
        echo "📅 Procesando filas con timestamp posterior a: " . $fecha_limite->format('Y-m-d H:i:s') . "\n\n";

        $totalProcesadas = 0;
        $totalActualizadas = 0;
        $totalSaltadasTimestamp = 0;
        $totalSaltadasEstado = 0;

        for ($i = 1; $i < count($datosSheets); $i++) {
            $fila = $datosSheets[$i];
            if (count($fila) < 8 || empty($fila[1]) || empty($fila[4])) {
                echo "⏭️ Fila $i incompleta o inválida, saltando...\n\n";
                continue;
            }

            // Filtrar por timestamp (columna 0)
            $timestamp_str = trim($fila[0] ?? '');
            $procesar = true;
            if (!empty($timestamp_str)) {
                $timestamp = DateTime::createFromFormat('d/m/Y H:i:s', $timestamp_str);
                if ($timestamp === false) {
                    echo "⚠️  Fila $i: No se pudo interpretar timestamp '$timestamp_str'. Se procesará igual.\n";
                } elseif ($timestamp < $fecha_limite) {
                    echo "⏭️ Fila $i: Timestamp $timestamp_str es anterior a $limite_dias días. Saltando...\n\n";
                    $totalSaltadasTimestamp++;
                    $procesar = false;
                }
            }

            if (!$procesar) continue;

            // Filtrar por estado (columna 7)
            $estado_fila = trim($fila[7] ?? '');
            if ($estado_fila !== 'PENDIENTE') {
                echo "⏭️ Fila $i: Estado = '$estado_fila' (no es PENDIENTE). Saltando...\n\n";
                $totalSaltadasEstado++;
                continue;
            }

            // Procesar la fila
            echo "--- Procesando fila $i ---\n";
            $resultado = actualizarMySQL($fila);

            if ($resultado['success']) {
                $actualizados = $resultado['actualizados'];
                echo "✅ $actualizados registros actualizados\n";
                echo "🎯 Estrategia: {$resultado['estrategia']}\n";
                if (!empty($resultado['detalle'])) {
                    foreach ($resultado['detalle'] as $det) echo "   📝 $det\n";
                }
                $totalActualizadas += $actualizados;
            } else {
                echo "❌ {$resultado['message']}\n";
            }
            $totalProcesadas++;
            echo "\n";
        }

        echo "=========================================\n";
        echo "🎉 RESUMEN EJECUCIÓN:\n";
        echo "📊 Filas totales en Sheet: " . (count($datosSheets)-1) . "\n";
        echo "⏭️ Filas saltadas por antigüedad: $totalSaltadasTimestamp\n";
        echo "⏭️ Filas saltadas por estado no PENDIENTE: $totalSaltadasEstado\n";
        echo "📊 Filas procesadas (válidas y recientes): $totalProcesadas\n";
        echo "✅ Registros actualizados en BD: $totalActualizadas\n";
        echo "🕒 Finalizado: " . date('Y-m-d H:i:s') . "\n";
        echo "=========================================\n";
    } else {
        echo "❌ No se pudieron leer los datos de Google Sheets\n";
    }

    file_put_contents($log_file, "=== PROCESO COMPLETADO: " . date('Y-m-d H:i:s') . " ===\n\n", FILE_APPEND);
} catch (Exception $e) {
    echo "💥 ERROR CRÍTICO: " . $e->getMessage() . "\n";
    file_put_contents($log_file, "=== ERROR: " . $e->getMessage() . " ===\n", FILE_APPEND);
}
?>