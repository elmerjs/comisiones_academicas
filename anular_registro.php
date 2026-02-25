<?php 
error_log("PHP ejecutado correctamente");

require 'vendor/autoload.php';
require 'cn.php';  // Archivo para la conexión a la base de datos

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\SimpleType\Jc;
$anioactual = date('Y');
if (isset($_GET['comision_id'])) {
    //    var_dump($_GET['comision_id']); // Esto debe mostrar el valor en el console.log
$comi= $_GET['comision_id'];
// Crear una nueva instancia de PHPWord
$phpWord = new PhpWord();

// Configurar el idioma del documento a español
$phpWord->getSettings()->setThemeFontLang(new Language(Language::ES_ES));

// Añadir una nueva sección con tamaño de oficio y márgenes personalizados
$sectionStyle = array(
    'paperSize' => 'Folio',
    'marginTop' => 3000,  // 5 cm
'marginLeft' => 1162, // 2,5 cm
'marginRight' => 1132, // 2,5 cm
'marginBottom' => 1417, // 2,5 cm
);
$section = $phpWord->addSection($sectionStyle);
$header = $section->addHeader();
$table = $header->addTable();
$table->addRow();
$query_selectint = "SELECT tipo_estudio FROM comision_academica WHERE id = $comi";
     $resultint = mysqli_query($con, $query_selectint);
    
    if ($resultint && mysqli_num_rows($resultint) > 0) {
        // Obtener el valor actual de 'id'
        $rowint = mysqli_fetch_assoc($resultint);
        $intext = $rowint['tipo_estudio'];
    }else {
        echo "Error al obtener la observación actual o la comisión no existe.";
    }
if ($intext == 'EXT') {
    
$cell = $table->addCell();
$cell->addText("\n\n\n"); // Tres saltos de línea
$cell->addText("\n\n\n"); // Tres saltos de línea
$cell->addText("\n\n\n"); // Tres saltos de línea

$cell->addText("\n\n\n"); // Tres saltos de línea


}
    
    
    else {
    $cell = $table->addCell(12000); // Ajusta este valor para empujar la imagen hacia la derecha
$cell->addImage(
    'img/encabezadob.png',
    array(
        'width' => 170,
       // 'height' => 120,
        'alignment' => Jc::LEFT
    )
);

// Añadir un pie de página
$footer = $section->addFooter();
$table = $footer->addTable();
$table->addRow();
$cell = $table->addCell(10000); // Ajusta este valor para empujar la imagen más hacia abajo
//$cell->addText(""); // Tres saltos de línea

$cell->addImage(
    'img/PIEb.png',
    array(
        'width' => 470,
      //  'height' => 120,
        'alignment' => Jc::LEFT
    )
);

    
    }
    
    
    

// Consulta SQL
  $comision_id = intval($_GET['comision_id']);
    // Actualizar el estado de la comisión a "anulada"
$medio_comunicacion = isset($_GET['medio_comunicacion']) ? $_GET['medio_comunicacion'] : null;
$razon = isset($_GET['razon']) ? $_GET['razon'] : null;

// Verificar que el ID de la comisión sea válido
// Verificar que el ID de la comisión sea válido
if (!empty($comision_id)) {
    // Obtener la observación actual de la comisión
    $query_select = "SELECT observacion FROM comision_academica WHERE id = $comision_id";
    $result = mysqli_query($con, $query_select);
    
    if ($result && mysqli_num_rows($result) > 0) {
        // Obtener el valor actual de 'observacion'
        $row = mysqli_fetch_assoc($result);
        $observacion_actual = $row['observacion'];

        // Concatenar $medio_comunicacion y $razon a la observación actual
        $nueva_observacion = $observacion_actual;
        if (!empty($observacion_actual)) {
            // Agregar un salto de línea si ya hay algo en la observación actual
            $nueva_observacion .= "\n";
        }
        $nueva_observacion .= "Anulación según: $medio_comunicacion - $razon";

        // Crear la consulta para actualizar el estado y la observación de la comisión
        $query_update = "UPDATE comision_academica 
                         SET estado = 'anulada', observacion = ? 
                         WHERE id = ?";
        
        // Preparar la consulta
        $stmt = mysqli_prepare($con, $query_update);
        if ($stmt) {
            // Vincular los parámetros
            mysqli_stmt_bind_param($stmt, "si", $nueva_observacion, $comision_id);
            
            // Ejec utar la consulta
            if (mysqli_stmt_execute($stmt)) {
                // El estado de la comisión y la observación se han actualizado correctamente
                // No se realiza redirección ni se interrumpe el flujo
            } else {
                echo "Error al actualizar el estado y la observación de la comisión: " . mysqli_stmt_error($stmt);
            }

            // Cerrar la declaración
            mysqli_stmt_close($stmt);
        } else {
            echo "Error al preparar la consulta de actualización: " . mysqli_error($con);
        }
    } else {
        echo "Error al obtener la observación actual o la comisión no existe.";
    }
} else {
    echo "ID de comisión no proporcionado o inválido.";
} 
$consultares = "SELECT 
    No_resolucion,
    comision_academica.id AS id_comision_academica,
    fecha_resolucion,
    documento AS cc_res,
    tipo_estudio,
    fecha_aval,
    duracion_horas,
    fechasol,   
    organizado_por,
    id_ciudad,
    ciudad_pais,
    comision_academica.pais AS pais_basico_c,
    tipo_participacion,
    evento,
    nombre_trabajo,
    comision_academica.estado AS estado_comision,
    observacion,
    fechaINI,
    vence,
    vigencia,
    periodo,
    reintegrado,
    fecha_informe,
    folios,
    comision_academica.reviso,
    
    id_rector,
    id_vice,
    justificacion,
    viaticos,
    tiquetes,
    inscripcion,
    cargo_a,
    valor,
    cdp,
    id_tercero,
    documento_tercero,
    nombre_completo,
    apellido1,
    apellido2,
    nombre1,
    nombre2,
    fk_depto,
    vincul,
    tercero.sexo AS sexo_tercero,
    tercero.estado AS estado_tercero,
    vinculacion AS dedicacion,
    cargo_admin,
    tercero.email AS email_profesor,
    escalafon,
    fecha_ingreso,
    NOMBRE_DEPTO,
    NOMBRE_DEPTO_CORT,
    depto_nom_propio,
    NOMBREC_FAC,
    NOMBREF_FAC,
    nombre_fac_min,
    email_fac,
    rector.rector_cc AS cc_rector,
    rector.rector_nombre AS nombre_rector,
    rector.rector_sexo AS sexo_rector,
    rector.rector_resol_encargo AS resol_encargo_rector,
    tipo_rector,
    vicerrector.vice_cc AS cc_vice,
    vicerrector.vice_nombre AS nombre_vice,
    vicerrector.vice_sexo AS sexo_vice,
    vicerrector.vice_resol_encargo AS res_encargo_vice,
    vicerrector.vice_nom_propio AS nom_propio_vice,
    revisa.revisa_nom_propio as nom_revisa,
    vicerrector.tipo_vice AS tipo_vice,

    users.Id AS id_user,
    users.DocUsuario,
    users.Name,
    users.Email AS email_usuario,
    CASE
        WHEN subquery.num_paises = 1 AND subquery.num_destinos = 2 THEN CONCAT(GROUP_CONCAT(destino.ciudad ORDER BY destino.pais SEPARATOR ' y '), ' - ', MIN(destino.pais))
        WHEN subquery.num_paises = 1 THEN CONCAT(GROUP_CONCAT(destino.ciudad ORDER BY destino.pais SEPARATOR ', '), ' - ', MIN(destino.pais))
        WHEN subquery.num_paises > 1 AND subquery.num_destinos = 2 THEN GROUP_CONCAT(destino.id_ciudad_pais ORDER BY destino.pais SEPARATOR ' y ')
        ELSE GROUP_CONCAT(destino.id_ciudad_pais ORDER BY destino.pais SEPARATOR ', ')
    END AS destinos,
    subquery.num_destinos
FROM comision_academica
JOIN tercero ON comision_academica.documento = tercero.documento_tercero
JOIN deparmanentos ON deparmanentos.PK_DEPTO = tercero.fk_depto
JOIN facultad ON facultad.PK_FAC = deparmanentos.FK_FAC
JOIN destino ON destino.id_comision = comision_academica.id
LEFT JOIN rector ON rector.rector_cc = comision_academica.id_rector
LEFT JOIN vicerrector ON vicerrector.vice_cc = comision_academica.id_vice
LEFT JOIN revisa ON revisa.revisa_nom_propio = comision_academica.reviso

LEFT JOIN users ON users.Name = comision_academica.tramito
LEFT JOIN (
    SELECT 
        id_comision,
        COUNT(DISTINCT pais) AS num_paises,
        COUNT(id_destino) AS num_destinos
    FROM destino
    GROUP BY id_comision
) AS subquery ON subquery.id_comision = comision_academica.id
WHERE comision_academica.id = '$comision_id'
GROUP BY comision_academica.id;";

$resultadores = $con->query($consultares);

if ($resultadores->num_rows > 0) {
    $row = $resultadores->fetch_assoc();
    $res = $row['No_resolucion'];
    $tipo_estudio = $row['tipo_estudio'];
    $tipo_b = ($tipo_estudio === 'EXT') ? 'exterior' : 'interior';
    $res_rec_vra = ($tipo_estudio === 'EXT') ? ' RESOLUCION RECTORAL ' : 'RESOLUCION VRA ';
    $res_rec_vrab = ($tipo_estudio === 'EXT') ? ' Resolución Rectoral ' : 'Resolución VRA ';

        $tipo_vice = $row['tipo_vice'];
    $resol_encargo_rector = $row['resol_encargo_rector'];
    $res_encargo_vice = $row['res_encargo_vice'];
    $rector = $row['nombre_rector'];
    $tipo_rector = $row['tipo_rector'];
    
    $sexo_tercero = $row['sexo_tercero'];
    $cargo_admin = $row['cargo_admin'];
    $nombre1 = $row['nombre1'];
    $nombre2 = $row['nombre2'];
    $apellido1 = $row['apellido1'];
    $apellido2 = $row['apellido2'];
 $elaboro = $row['Name'];
    $autorizo = $row['nom_propio_vice'];
        $reviso = $row['nom_revisa'];

    
    // Establecer las variables según el sexo del profesor
    if ($sexo_tercero == 'M') {
        $saludo = "al profesor ";
        $saludode = "del profesor ";
        $adscrito = " adscrito ";
        $saludo_el_la = "el profesor";
        $identificado = "identificado";    
        $alinteresado = "al interesado";
        $comisionado = "el profesor comisionado";
    } elseif ($sexo_tercero == 'F') {
        $saludo = "a la profesora ";
        $saludode = "de la profesora ";
        $adscrito = " adscrita ";
        $saludo_el_la = "la profesora";
        $identificado = "identificada";    
        $alinteresado = "a la interesada";
        $comisionado = "la profesora comisionada";
    } else {
        $saludo = "a el(la) profesor(a)";
        $saludode = "del(a) profesor(a) ";
        $adscrito = " adscrito(a) ";
        $saludo_el_la = "el(la) profesor(a)";
        $identificado = "identificado(a)";    
        $alinteresado = "a el(la) interesado(a)";    
        $comisionado = "el profesor comisionado";
    }

$trabajo = $row['nombre_trabajo'];

if ($trabajo == null) {
    $coneltrabajo = "";
} else {
     $coneltrabajo = "con el trabajo  «".$trabajo."»";
}
$evento = $row['evento'];
$organizado_por = $row['organizado_por'];
$destinos = $row['destinos'];
$justificacion = $row['justificacion'];


    $ellasciudades =  $row['num_destinos'];
if ($ellasciudades == 1) {
    $endestinos = "en la ciudad de ";
} else {
     $endestinos = "en las ciudades de ";

}
$fechainicio1 = $row['fechaINI'];
$fechafin1 = $row['vence'];
$viaticos= $row['viaticos'];
$email_profesor= $row['email_profesor'];


$tiquetes= $row['tiquetes'];

$inscripcion= $row['inscripcion'];

$cargo_a= $row['cargo_a'];
$cdp= $row['cdp'];
$valor= $row['valor'];
$cargo_admin= $row['cargo_admin'];
$email_fac= $row['email_fac'];


    // Para los nombres en el orden: nombre1 nombre2 apellido1 apellido2
    $nombre_profesor = "$nombre1 $nombre2 $apellido1 $apellido2";
    $apellido1p = mb_convert_case(strtolower($row['apellido1']), MB_CASE_TITLE, "UTF-8");
$apellido2p = mb_convert_case(strtolower($row['apellido2']), MB_CASE_TITLE, "UTF-8");
$nombre1p = mb_convert_case(strtolower($row['nombre1']), MB_CASE_TITLE, "UTF-8");
$nombre2p = mb_convert_case(strtolower($row['nombre2']), MB_CASE_TITLE, "UTF-8");

    // Para los nombres en el orden: nombre1 nombre2 apellido1 apellido2
    $nombresOrdenadosp = "$nombre1p $nombre2p $apellido1p $apellido2p";
//fechas 
    $fechres = date("d/m/Y", strtotime($row['fecha_resolucion'])); $row['fecha_resolucion'];   
$fecha = $row['fecha_resolucion'];
$fechaComoEntero = strtotime($fecha);
$dia = date("d", $fechaComoEntero);
$mes = date("m", $fechaComoEntero);
  
$anio = date("Y", $fechaComoEntero);
$meses = [
                  '01' => 'enero'
                 ,'02' => 'febrero'
                 ,'03' => 'marzo'
                 ,'04' => 'abril'
                 ,'05' => 'mayo'
                 ,'06' => 'junio'
                 ,'07' => 'julio'
                 ,'08' => 'agosto'
                 ,'09' => 'septiembre'
                 ,'10' => 'octubre'
                 ,'11' => 'noviembre'
                 ,'12' => 'diciembre'
                ];

$fecha_letras = $dia.' de '.$meses[date($mes)].' de '.$anio;
$fecha_letrasB = $dia.' deL mes de '.$meses[date($mes)].' de '.$anio;


// Array de los nombres de los días en letras
$dias_en_letras = [
    '1' => 'primero (1)',
    '2' => 'dos (2)',
    '3' => 'tres (3)',
    '4' => 'cuatro (4)',
    '5' => 'cinco (5)',
    '6' => 'seis (6)',
    '7' => 'siete (7)',
    '8' => 'ocho (8)',
    '9' => 'nueve (9)',
    '10' => 'diez (10)',
    '11' => 'once (11)',
    '12' => 'doce (12)',
    '13' => 'trece (13)',
    '14' => 'catorce (14)',
    '15' => 'quince (15)',
    '16' => 'dieciséis (16)',
    '17' => 'diecisiete (17)',
    '18' => 'dieciocho (18)',
    '19' => 'diecinueve (19)',
    '20' => 'veinte (20)',
    '21' => 'veintiún (21)',
    '22' => 'veintidós (22)',
    '23' => 'veintitrés (23)',
    '24' => 'veinticuatro (24)',
    '25' => 'veinticinco (25)',
    '26' => 'veintiséis (26)',
    '27' => 'veintisiete (27)',
    '28' => 'veintiocho (28)',
    '29' => 'veintinueve (29)',
    '30' => 'treinta (30)',
    '31' => 'treinta y un (31)'
];

// Obtener el día en letras
$dia_en_letras = isset($dias_en_letras[$dia]) ? $dias_en_letras[$dia] : 'Día inválido';

// Mostrar el día en formato adecuado
if ($dia == 1) {
    $dia_en_letras = "el día (1) ";
} else {
    $dia_en_letras = "a los " . $dia_en_letras." días ";
}
     
    
    
    
$fecha_dia_mes= $dia.' de '.$meses[date($mes)];
// Variables
$facultad_min = $row['nombre_fac_min'];
$fecha_aval = $row['fecha_aval'];
$fechaComoEnteroaval = strtotime($fecha_aval);
$diaaval = date("d", $fechaComoEnteroaval);
$mesaval = date("m", $fechaComoEnteroaval);
$anioaval = date("Y", $fechaComoEnteroaval);
$fecha_letras_aval = $diaaval.' de '.$meses[date($mesaval)].' de '.$anioaval;
$mes_anio = $meses[date($mesaval)].'-'.$anioaval;

$documento_tercero = $row['documento_tercero'];
$depto_nom_propio = $row['depto_nom_propio'];
    
function obtenerNombreMes($mesNumero) {
    $meses = array(
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
        7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    );
    return $meses[$mesNumero];
}

    function obtenerFechasFormateadas($fechainicio, $fechafin) {
    // Creamos objetos DateTime a partir de las fechas proporcionadas
    $fechaInicioObj = DateTime::createFromFormat('Y-m-d', $fechainicio);
    $fechaFinObj = DateTime::createFromFormat('Y-m-d', $fechafin);

    // Verificamos si las fechas se parsearon correctamente
    if (!$fechaInicioObj || !$fechaFinObj) {
        throw new Exception("Las fechas proporcionadas no son válidas.");
    }

    // Formateamos las fechas manualmente en español
    $diaInicio = $fechaInicioObj->format('j');
    $mesInicio = obtenerNombreMes($fechaInicioObj->format('n'));
    $anioInicio = $fechaInicioObj->format('Y');

    $diaFin = $fechaFinObj->format('j');
    $mesFin = obtenerNombreMes($fechaFinObj->format('n'));
    $anioFin = $fechaFinObj->format('Y');

    // Generamos las diferentes formas de salida según las condiciones
    if ($fechaInicioObj == $fechaFinObj) {
        return "el $diaInicio de $mesInicio de $anioInicio";
    } elseif ($anioInicio !== $anioFin) {
        return "del $diaInicio de $mesInicio de $anioInicio al $diaFin de $mesFin de $anioFin";
    } elseif ($mesInicio !== $mesFin) {
        return "del $diaInicio de $mesInicio al $diaFin de $mesFin de $anioInicio";
    } else {
        return "del $diaInicio al $diaFin de $mesInicio de $anioInicio";
    }
}

    
function formatDocumentNumber($number) {
    return number_format($number, 0, '', '.');
}
$sexo_rector = $row['sexo_rector'];

$sexo_vice = $row['sexo_vice'];

$documento_tercero = formatDocumentNumber($row['documento_tercero']);
    if ($tipo_rector == 'Rector') {
    if ($sexo_rector == 'F') {
        $nombrarrector = "La Rectora ";
        $cargor = "Rectora";
        $el_la = "La ";
        $el_la = "La ";
        $parrafo2r= "RECTORA DE LA UNIVERSIDAD DEL CAUCA";

    } else {
        $nombrarrector = "El Rector ";
        $cargor = "Rector";
        $parrafo2r= "RECTOR DE LA UNIVERSIDAD DEL CAUCA, ";
                $el_la = "El ";

    }
} elseif ($tipo_rector == 'Rector Encargado') {
    if ($sexo_rector == 'F') {
        $nombrarrector = "La Rectora Encargada ";
        $cargor = "Rectora Encargada";
                $el_la = "La ";

         $parrafo2r= "RECTORA ENCARGADA DE LA UNIVERSIDAD DEL CAUCA, ";
    } else {
        $nombrarrector = "El Rector Encargado ";
                $cargor = "Rector Encargado";
        $el_la = "El ";
        $parrafo2r= "RECTOR ENCARGADO DE LA UNIVERSIDAD DEL CAUCA, ";
    }
} elseif ($tipo_rector == 'Rector Delegatario') {
    if ($sexo_rector == 'F') {
        $nombrarrector = "El Rector Delegatario ";
                $cargor = "Rectora Delegataria";
                $el_la = "La ";

$parrafo2r= "RECTORA DELEGATARIA DE LA UNIVERSIDAD DEL CAUCA, ";
    } else {
        $nombrarrector = "El Rector Delegatario ";
       $cargor = "Rector Delegatario";
        $el_la = "El ";
$parrafo2r = "RECTOR DELEGATARIO DE LA UNIVERSIDAD DEL CAUCA, ";
    }
}
    
if ($tipo_vice == 'propiedad') {
    if ($sexo_vice == 'F') {
        $nombrarvice = "La Vicerrectora Académica ";
        $cargov = "Vicerrectora Académica";

    } else {
        $nombrarvice = "El Vicerrector Académico ";
        $cargov = "Vicerrector Académico";
    }
} elseif ($tipo_vice == 'Encargado') {
    if ($sexo_vice == 'F') {
        $nombrarvice = "La Vicerrectora Académica encargada ";
        $cargov = "Vicerrectora Académica (E)";

    } else {
        $nombrarvice = "El Vicerrector Académico encargado ";
                $cargov = "Vicerrector Académico (E)";

    }
} elseif ($tipo_vice == 'delegatario') {
    if ($sexo_vice == 'F') {
        $nombrarvice = "La Vicerrectora Académica delegataria ";
                $cargov = "Vicerrectora Académica (D)";

    } else {
        $nombrarvice = "El Vicerrector Académico delegatario ";
                $cargov = "Vicerrector Académico (D)";

    }
}
    function generarTextoErogaciones($viaticos, $tiquetes, $inscripcion, $cargo_a, $cdp, $valor, $cargo_admin) {
    $texto = ' no genera erogaciones a la Universidad del Cauca, en viáticos, inscripción o tiquetes.';

    if ($viaticos == 1 || $tiquetes == 1 || $inscripcion == 1) {
        $texto = ' genera erogaciones a la Universidad del Cauca, en gastos de ';

        $items = [];
        if ($viaticos == 1) $items[] = 'viáticos';
        if ($tiquetes == 1) $items[] = 'tiquetes';
        if ($inscripcion == 1) $items[] = 'inscripción';

        $count_items = count($items);

        if ($count_items == 3) {
            $last_item = array_pop($items);
            $texto .= implode(', ', $items) . " e $last_item";
        } else {
            $texto .= implode(' y ', $items);
        }

        if (!empty($cargo_a)) {
            $texto .= " con cargo al presupuesto de $cargo_a";
        }

     /*   if (!empty($cargo_admin) && in_array($cargo_admin, ['JEFE', 'DIRECTOR', 'DECANO'])) {
            $texto .= ", de acuerdo con el $cdp por valor de $valor";
        }*/
    }

    // Si inscripción es 1 y solo se han seleccionado viáticos o tiquetes
    if ($inscripcion == 1 && (($viaticos == 1 && $tiquetes == 0) || ($viaticos == 0 && $tiquetes == 1))) {
        // Reemplazar "y" por "e"
        $texto = str_replace(' y ', ' e ', $texto);
    }

    return $texto;
}
    
    $texto_erogaciones = generarTextoErogaciones($viaticos, $tiquetes, $inscripcion, $cargo_a, $cdp, $valor, $cargo_admin);
    if ($intext == 'EXT') {
    // Añadir el texto inicial en negrilla para EXT
    $section->addText('2.1–4.16', array('bold' => true));
} else {
    // Añadir el texto inicial en negrilla para los demás
    $section->addText('4–4.12', array('bold' => true));
}
    // Añadir el título centrado
    $section->addText($res_rec_vra.' No.                          DE  ' . $anio, array('bold' => true, 'size' => 10), array('alignment' => Jc::CENTER,'spaceAfter' => 0));
 // Añadir el título centrado
    $section->addText('(                       )', array('bold' => true, 'size' => 10), array('alignment' => Jc::CENTER));


    // Añadir el primer párrafo
    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
    $textRun = $section->addTextRun($paragraphStyle);
    
   
    
   $textRun->addText('Por la cual se deroga la Resolución No.'.$res.' de '.$anio.', de una comisión académica al '.$tipo_b.'.', null);

   // Añadir el segundo párrafo
    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 10);
    $textRun = $section->addTextRun($paragraphStyle);
    
if ($tipo_estudio == 'EXT') {
    $textRun->addText($el_la);
        $textRun->addText('RECTOR DE LA UNIVERSIDAD DEL CAUCA', array('bold' => true));
    
                $textRun->addText(', en uso de sus competencias establecidas en el Artículo 23 del Acuerdo Superior 105 de 1993 o Estatuto General de la Universidad del Cauca, modificado por el Acuerdo Superior 025 de 2020, y');

    } else{
       
   $textRun->addText('LA VICERRECTORA ACADÉMICA DE LA UNIVERSIDAD DEL CAUCA', array('bold' => true));
    
                $textRun->addText(' en uso de sus competencias establecidas en el Acuerdo Superior 024 de 1993 - Estatuto del profesor de la Universidad del Cauca, principalmente por lo previsto  en el artículo 73, modificado por el artículo quinto del Acuerdo Superior  031 de 2020 , y');
}
     // Añadir el título centrado
    

   
      
// Añadir el título centrado
$section->addText('CONSIDERANDO QUE:', 
    array('bold' => true, 'size' => 10), 
    array('alignment' => Jc::CENTER, 'spaceBefore' => 150, 'spaceAfter' => 150)
);

    
    
// Añadir el párrafo con variables y estilos
$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
$textRun = $section->addTextRun($paragraphStyle);

$textRun->addText('La  ' . $res_rec_vrab .' No.'.$res.' del '.$fecha_letras.', se autorizó una comisión académica al ' . $tipo_b . ', ' . $saludo . ' ', null);
$textRun->addText($nombre_profesor, array('bold' => true));
$textRun->addText(', ', null);
$textRun->addText($identificado . ' con la cédula de ciudadanía número ', null);
$textRun->addText($documento_tercero, array('bold' => true));
$textRun->addText(', ' . $adscrito . ' al departamento de ' . $depto_nom_propio, null);
$textRun->addText(', con el fin de participar  en ', null);
$textRun->addText($evento, array('italic' => true));
$textRun->addText(', organizado por ' . $organizado_por . ', ' . $endestinos . $destinos . '.', null);

// Añadir el párrafo con variables y estilos
// Configuración de estilo para justificación
$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
$textRun = $section->addTextRun($paragraphStyle);

// Agregar el texto con formato específico
$textRun->addText('Mediante '. $medio_comunicacion .' '.$saludo_el_la.' ',null);
    
    $textRun->addText($nombre_profesor, array('bold' => true));

    $textRun->addText(' solicitó la cancelación de la comisión debido a que '.$razon.'.', null);

$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
    $textRun = $section->addTextRun($paragraphStyle);
    
    $textRun->addText('De acuerdo con el parágrafo  del artículo primero de la '.$res_rec_vrab, array('bold' => false));
       
    
     $textRun->addText('La comisión autorizada '. $texto_erogaciones . '.',null);    
    
if (strpos($texto_erogaciones, 'no genera erogaciones a la Universidad del Cauca, en viáticos, inscripción o tiquetes') === false) {

    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
    $textRun = $section->addTextRun($paragraphStyle);

    $textRun->addText(
        'Mediante correo electrónico emitido por la Tesorería de la División de Gestión Financiera de la Universidad del Cauca se informa que ' .$saludo_el_la.' ',null); 
        
            $textRun->addText($nombre_profesor, array('bold' => true));

       $textRun->addText(', no ha solicitado trámite de viáticos, inscripción y/o tiquetes para financiar el objeto de la comisión',null);
}
       $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
    $textRun = $section->addTextRun($paragraphStyle);
  
    
     $textRun->addText('En mérito de lo expuesto, ',null); 




// Añadir el título centrado
$section->addText('RESUELVE:', 
    array('bold' => true, 'size' => 10), 
    array('alignment' => Jc::CENTER, 'spaceBefore' => 150, 'spaceAfter' => 150)
);//$section->addTextBreak(1);


    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
    $textRun = $section->addTextRun($paragraphStyle);
    
    $textRun->addText('ARTÍCULO PRIMERO: ', array('bold' => true));
       
       
     $textRun->addText('Derogar la '. $res_rec_vrab .' No.'.$res.' del '.$fecha_letras.' y en consecuencia cancelar la comisión académica al '.$tipo_b.' '. obtenerFechasFormateadas($fechainicio1, $fechafin1) .', '.$saludo.' ',null); 
        
                       $textRun->addText($nombre_profesor, array('bold' => true));
        $textRun->addText(', ', null);
        $textRun->addText($identificado . ' con la cédula de ciudadanía número ', null);
        $textRun->addText($documento_tercero, array('bold' => true));
        $textRun->addText(', ' . $adscrito . ' al departamento de ' . $depto_nom_propio, null);
        $textRun->addText(', con el fin de participar  en ', null);
        $textRun->addText($evento, array('italic' => true));
        $textRun->addText(', organizado por ' . $organizado_por . ', ' . $endestinos . $destinos . '.', null);
        /* if ($viaticos == 1 || $tiquetes == 1 || $inscripcion == 1) {*/
          $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
    $textRun = $section->addTextRun($paragraphStyle);
     /* $textRun->addText('ARTÍCULO SEGUNDO: ', array('bold' => true));

       $textRun->addText('En caso de hacerse efectivas las erogaciones generadas en la '. $res_rec_vrab. ' '.$res . ' del  '.$fecha_letras. ', éstas deberán ser reintegradas a la Universidad, conforme a las normativas y procedimientos establecidos por la Universidad del Cauca.',null);
          $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
    $textRun = $section->addTextRun($paragraphStyle);
    */
    $textRun->addText('ARTÍCULO SEGUNDO: ', array('bold' => true));
       
    
     $textRun->addText('Enviar copia del presente acto administrativo a la Oficina de Relaciones Interinstitucionales e Internacionales (relacionesinter@unicauca.edu.co), '. $facultad_min . ' ('.$email_fac. '), Vicerrectoría Académica (viceacad@unicauca.edu.co), División de Gestión del Talento Humano (rhumanos@unicauca.edu.co), División de Gestión Financiera (financiera@unicauca.edu.co), al Área de Seguridad y Salud en el Trabajo (saludocu@unicauca.edu.co) y '.$alinteresado.', '.$nombresOrdenadosp.' ('.$email_profesor.').',null);    
        $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
    $textRun = $section->addTextRun($paragraphStyle);         
    $textRun->addText('ARTÍCULO TERCERO: ', array('bold' => true));
       
    
     $textRun->addText(' La presente Resolución rige a partir de su expedición.     
',null);    
             
    /*   }
        else {
            $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
    $textRun = $section->addTextRun($paragraphStyle);
    
    $textRun->addText('ARTÍCULO SEGUNDO: ', array('bold' => true));
       
       
    
     $textRun->addText('Enviar copia del presente acto administrativo a la Oficina de Relaciones Interinstitucionales e Internacionales (relacionesinter@unicauca.edu.co), '. $facultad_min . ' ('.$email_fac. '), División de Gestión del Talento Humano (rhumanos@unicauca.edu.co), División de Gestión Financiera (financiera@unicauca.edu.co), al Área de Seguridad y Salud en el Trabajo (saludocu@unicauca.edu.co) y '.$alinteresado.', '.$nombresOrdenadosp.' ('.$email_profesor.').',null); 
            $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
    $textRun = $section->addTextRun($paragraphStyle);
                $textRun->addText('ARTÍCULO TERCERO: ', array('bold' => true));
       
    
     $textRun->addText(' La presente Resolución rige a partir de su expedición.     
',null);    
            
        }
    
      */ 
  $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 100);
    $textRun = $section->addTextRun($paragraphStyle);
      $textRun->addText('Se expide en Popayán (Cauca), ',null);

      
// Añadir el título centrado
$section->addText('COMUNÍQUESE Y CÚMPLASE,', array('bold' => true, 'size' => 10), array('alignment' => Jc::CENTER, 'spaceAfter' => 10)); 
    
    $section->addTextBreak(1);
$section->addTextBreak(1);

// Añadir el título centrado
    $rector_mayusculas = 'DEIBAR RENE HURTADO HERRERA';//mb_strtoupper($rector, 'UTF-8');
        $vice_mayusculas = 'AIDA PATRICIA GONZALEZ NIEVA';//mb_strtoupper($autorizo, 'UTF-8');
        $cargor = 'RECTOR';
    $cargov = 'VICERRECTORA ACADEMICA';
if ($tipo_estudio == 'EXT') {

    $section->addText($rector_mayusculas, array('bold' => true, 'size' => 10), array('alignment' => Jc::CENTER, 'spaceAfter' => 0));   

    $section->addText($cargor, array('bold' => true, 'size' => 10), array('alignment' => Jc::CENTER, 'spaceAfter' => 0));  
}
    
    else {
        
         $section->addText($vice_mayusculas, array('bold' => true, 'size' => 10), array('alignment' => Jc::CENTER, 'spaceAfter' => 0));   

    $section->addText($cargov, array('bold' => true, 'size' => 10), array('alignment' => Jc::CENTER, 'spaceAfter' => 0));  
        
    }
// Estilos de párrafo
$paragraphStyle = array('alignment' => Jc::LEFT, 'spaceAfter' => 0);

// Crear un TextRun para "Proyectó" y "Revisó" con el mínimo espacio entre ellos
$textRun = $section->addTextRun($paragraphStyle);
if ($tipo_estudio == 'EXT') {
    
    $textRun->addText('Autorizó: '.$autorizo, array('size' => 8, 'italic' => true)); // Aplicar cursiva al texto de "Autorizó"
}
$textRun->addTextBreak(); // Añadir un salto de línea mínimo
$textRun->addText('Revisó: '.$reviso, array('size' => 8, 'italic' => true)); // Aplicar cursiva al texto de "Revisó"
$textRun->addTextBreak(); // Añadir un salto de línea mínimo
    $elaboro_nombre_propio = ucwords(strtolower($elaboro));

$textRun->addText('Proyectó: '.$elaboro_nombre_propio, array('size' => 8, 'italic' => true)); // Aplicar cursiva al texto de "Proyectó"

} else {
    // Añadir mensaje de error si no se encontraron resultados
    $section->addText('No se encontraron resultados para la consulta.', array('alignment' => Jc::CENTER));
}
$filename = "Resolucion ANUL - $tipo_estudio - $apellido1 $nombre1-$mes_anio.docx";

// Encabezados HTTP para la descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');

// Guardar el archivo en formato DOCX y enviarlo al navegador
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('php://output');
 
exit; // Terminar el script para evitar cualquier salida adicional
    
}else {
    echo "No se recibió el ID del registro.";
}mysqli_close($con);

?>