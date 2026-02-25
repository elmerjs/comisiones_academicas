<?php 
require 'vendor/autoload.php';
require 'cn.php';  // Archivo para la conexión a la base de datos

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\SimpleType\Jc;

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

// Añadir un encabezado
$header = $section->addHeader();
$table = $header->addTable();
$table->addRow();
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

// Consulta SQL
$no_resolucion = isset($_GET['no_resolucion']) ? $_GET['no_resolucion'] : '';
$fecha_resolucion = isset($_GET['fecha_resolucion']) ? $_GET['fecha_resolucion'] : '';

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
    reviso,
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
    vicerrector.tipo_vice AS tipo_vice,
    revisa.revisa_nom_propio as nom_revisa,

    users.Id AS id_user,
    users.DocUsuario,
    users.Name,
    users.Email AS email_usuario,
        CASE
        WHEN subquery.num_paises = 1 AND subquery.num_destinos = 2 THEN CONCAT(GROUP_CONCAT(destino.ciudad ORDER BY destino.pais SEPARATOR ' y '))
        WHEN subquery.num_paises = 1 THEN CONCAT(GROUP_CONCAT(destino.ciudad ORDER BY destino.pais SEPARATOR ', '))
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
WHERE comision_academica.No_resolucion = '$no_resolucion' and comision_academica.fecha_resolucion = '$fecha_resolucion'
GROUP BY comision_academica.id;";

$resultadores = $con->query($consultares);

if ($resultadores->num_rows > 0) {
    $row = $resultadores->fetch_assoc();
    $tipo_estudio = $row['tipo_estudio'];

    $res = $row['No_resolucion'];
    $tipo_vice = $row['tipo_vice'];
    $res_encargo_vice = $row['res_encargo_vice'];
    $nombre_vice = $row['nombre_vice'];
    $sexo_tercero = $row['sexo_tercero'];
    $cargo_admin = $row['cargo_admin'];
    $nombre1 = $row['nombre1'];
    $nombre2 = $row['nombre2'];
    $apellido1 = $row['apellido1'];
    $apellido2 = $row['apellido2'];
 $elaboro = $row['Name'];
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

$trabajo = htmlspecialchars($row['nombre_trabajo'], ENT_QUOTES, 'UTF-8');

if ($trabajo == null) {
    $coneltrabajo = "";
    $parrafoini_trabajo = " en la medida que su participación en este evento de gran proyección nacional, tendrá la oportunidad de compartir sus conocimientos y adquirir nuevos, compartir su experiencia académica y profesional, fortalecer lazos interinstitucionales y dar visibilidad a su Programa y al Alma Máter";
} else {
     $coneltrabajo = "; en la medida en su participación se dará en calidad de ponente con el trabajo  ".$trabajo;
    $parrafoini_trabajo= " en la medida que su participación se dará en calidad de ponente con el trabajo «".$trabajo. "», siendo este un evento de gran proyección nacional, que le permitirá compartir sus conocimientos y adquirir nuevos, compartir su experiencia académica y profesional, fortalecer lazos interinstitucionales y dar visibilidad a su Programa y al Alma Máter";
}
$evento = htmlspecialchars($row['evento'], ENT_QUOTES, 'UTF-8');
$organizado_por = htmlspecialchars($row['organizado_por'], ENT_QUOTES, 'UTF-8');
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
    $apellido1p = ucfirst(strtolower($row['apellido1']));
    $apellido2p = ucfirst(strtolower($row['apellido2']));
    $nombre1p = ucfirst(strtolower($row['nombre1']));
    $nombre2p = ucfirst(strtolower($row['nombre2']));

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
$documento_tercero = $row['documento_tercero'];
$depto_nom_propio = $row['depto_nom_propio'];
$mes_anio = $meses[date($mesaval)].'-'.$anioaval;
   
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

$sexo_vice = $row['sexo_vice'];

$documento_tercero = formatDocumentNumber($row['documento_tercero']);
if ($tipo_vice == 'propiedad') {
    if ($sexo_vice == 'F') {
        $nombrarvice = "la Vicerrectora Académica ";
        $cargov = "Vicerrectora Académica";

    } else {
        $nombrarvice = "el Vicerrector Académico ";
        $cargov = "Vicerrector Académico";
    }
} elseif ($tipo_vice == 'Encargado') {
    if ($sexo_vice == 'F') {
        $nombrarvice = "la Vicerrectora Académica encargada ";
        $cargov = "Vicerrectora Académica (E)";

    } else {
        $nombrarvice = "el Vicerrector Académico encargado ";
                $cargov = "Vicerrector Académico (E)";

    }
} elseif ($tipo_vice == 'delegatario') {
    if ($sexo_vice == 'F') {
        $nombrarvice = "la Vicerrectora Académica delegataria ";
                $cargov = "Vicerrectora Académica (D)";

    } else {
        $nombrarvice = "el Vicerrector Académico delegatario ";
                $cargov = "Vicerrector Académico (D)";

    }
}
    function generarTextoErogaciones($viaticos, $tiquetes, $inscripcion, $cargo_a, $cdp, $valor, $cargo_admin) {
    $texto = ' no genera erogaciones a la Universidad del Cauca, en viáticos, inscripción o tiquetes';

    if ($viaticos == 1 || $tiquetes == 1 || $inscripcion == 1) {
        $texto = ' podrá generar erogaciones a la Universidad del Cauca, por concepto de ';

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
/*
        if (!empty($cargo_admin) && in_array($cargo_admin, ['JEFE', 'DIRECTOR', 'DECANO'])) {
            $texto .= ", de acuerdo al $cdp por valor de $valor";
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
    
    // Añadir el texto inicial en negrilla
    $section->addText('4-4.12', array('bold' => true,'size' => 12));

    // Añadir el título centrado
    $section->addText('RESOLUCIÓN VRA N°' . $res.' DE  ' . $anio, array('bold' => true, 'size' => 12), array('alignment' => Jc::CENTER,'spaceAfter' => 0));
 // Añadir el título centrado
    $section->addText('(' . $fecha_dia_mes.')', array('bold' => true, 'size' => 12), array('alignment' => Jc::CENTER));


    // Añadir el primer párrafo
$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
$textRun = $section->addTextRun($paragraphStyle);
$textRun->addText('Por la cual se autoriza una comisión académica en el territorio nacional ', array('size' => 12));

// Añadir el texto inicial

$resultadores->data_seek(0);  // Reinicia el puntero de resultados

// Inicializamos arrays para almacenar nombres de profesoras y profesores
$nombres_profesoras = [];
$nombres_profesores = [];

// Ciclo para agregar los nombres en los arrays correspondientes
while ($row = $resultadores->fetch_assoc()) {
    $nombre1 = $row['nombre1'];
    $nombre2 = $row['nombre2'];
    $apellido1 = $row['apellido1'];
    $apellido2 = $row['apellido2'];
    $nombre_completo = trim("$nombre1 $nombre2 $apellido1 $apellido2");

    if ($row['sexo_tercero'] == 'F') {
        $nombres_profesoras[] = $nombre_completo;
    } else if ($row['sexo_tercero'] == 'M') {
        $nombres_profesores[] = $nombre_completo;
    }
}

// Generar las frases correspondientes según el número de profesoras y profesores
if (count($nombres_profesoras) > 0 && count($nombres_profesores) > 0) {
    if (count($nombres_profesoras) == 1 && count($nombres_profesores) == 1) {
        // Un profesor y una profesora
        $textRun->addText('a la profesora ', array('size' => 12));
        $textRun->addText($nombres_profesoras[0], array('bold' => true, 'size' => 12));
        $textRun->addText(' y el profesor ', array('size' => 12));
        $textRun->addText($nombres_profesores[0], array('bold' => true, 'size' => 12));
    } else if (count($nombres_profesoras) > 1 && count($nombres_profesores) == 1) {
        // Varias profesoras y un profesor
        $todas_profesoras = implode(', ', array_slice($nombres_profesoras, 0, -1)) . ' y ' . end($nombres_profesoras);
        $textRun->addText('a las profesoras ', array('size' => 12));
        $textRun->addText($todas_profesoras, array('bold' => true, 'size' => 12));
        $textRun->addText(' y el profesor ', array('size' => 12));
        $textRun->addText($nombres_profesores[0], array('bold' => true, 'size' => 12));
    } else if (count($nombres_profesoras) == 1 && count($nombres_profesores) > 1) {
        // Una profesora y varios profesores
        $todos_profesores = implode(', ', array_slice($nombres_profesores, 0, -1)) . ' y ' . end($nombres_profesores);
        $textRun->addText('a la profesora ', array('size' => 12));
        $textRun->addText($nombres_profesoras[0], array('bold' => true, 'size' => 12));
        $textRun->addText(' y los profesores ', array('size' => 12));
        $textRun->addText($todos_profesores, array('bold' => true, 'size' => 12));
    } else {
        // Varias profesoras y varios profesores
        $todas_profesoras = implode(', ', array_slice($nombres_profesoras, 0, -1)) . ' y ' . end($nombres_profesoras);
        $todos_profesores = implode(', ', array_slice($nombres_profesores, 0, -1)) . ' y ' . end($nombres_profesores);
        $textRun->addText('a las profesoras ', array('size' => 12));
        $textRun->addText($todas_profesoras, array('bold' => true, 'size' => 12));
        $textRun->addText(' y los profesores ', array('size' => 12));
        $textRun->addText($todos_profesores, array('bold' => true, 'size' => 12));
    }
} else if (count($nombres_profesoras) > 0) {
    // Solo profesoras
    if (count($nombres_profesoras) > 1) {
        $todas_profesoras = implode(', ', array_slice($nombres_profesoras, 0, -1)) . ' y ' . end($nombres_profesoras);
        $textRun->addText('a las profesoras ', array('size' => 12));
        $textRun->addText($todas_profesoras, array('bold' => true, 'size' => 12));
    } else {
        $textRun->addText('a la profesora ', array('size' => 12));
        $textRun->addText($nombres_profesoras[0], array('bold' => true, 'size' => 12));
    }
} else if (count($nombres_profesores) > 0) {
    // Solo profesores
    if (count($nombres_profesores) > 1) {
        $todos_profesores = implode(', ', array_slice($nombres_profesores, 0, -1)) . ' y ' . end($nombres_profesores);
        $textRun->addText('los profesores ', array('size' => 12));
        $textRun->addText($todos_profesores, array('bold' => true, 'size' => 12));
    } else {
        $textRun->addText('el profesor ', array('size' => 12));
        $textRun->addText($nombres_profesores[0], array('bold' => true, 'size' => 12));
    }
}

// Añadir el punto final
$textRun->addText('.');


    
    
    
    // Añadir el segundo párrafo
    $paragraphStyle = array('alignment' => Jc::BOTH, 'size' => 12,'spaceAfter' => 0);
    $textRun = $section->addTextRun($paragraphStyle);
    
    if ($tipo_vice == 'propiedad') {
        
        $textRun->addText('La ',array('size' => 12));
        $textRun->addText('VICERRECTORA ACADÉMICA DE LA UNIVERSIDAD DEL CAUCA', array('bold' => true,'size' => 12));
        $textRun->addText(', en uso de competencias establecidas en el Acuerdo Superior 024 de 1993 - Estatuto del Profesor de la Universidad del Cauca, principalmente conforme a lo previsto en el artículo 73, modificado por el artículo quinto del Acuerdo Superior 031 de 2020, y',array('size' => 12));
    $textRun = $section->addTextRun($paragraphStyle);
        

    } elseif ($tipo_vice == 'Encargado') {
        $textRun->addText('El ',array('size' => 12));
        $textRun->addText('VICERRECTOR ACADÉMICO ENCARGADO DE LA UNIVERSIDAD DEL CAUCA', array('bold' => true,'size' => 12));
        $textRun->addText(', en uso de competencias establecidas en el Acuerdo Superior 024 de 1993 - Estatuto del Profesor de la Universidad del Cauca, principalmente conforme a lo previsto en el artículo 73, modificado por el artículo quinto del Acuerdo Superior 031 de 2020, y conforme a la ' . $res_encargo_vice . ', y',array('size' => 12));
    } elseif ($$tipo_vice == 'delegatario') {
        $textRun->addText('EL RECTOR DELEGATARIO DE LA UNIVERSIDAD DEL CAUCA', array('bold' => true,'size' => 12));
        $textRun->addText(', en uso de sus competencias previstas mediante el Acuerdo 105 de 1993, y conforme a la ' . $res_encargo_vice . ', y',array('size' => 12));
    }
     // Añadir el título centrado
        $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 140,'size' => 12);
   

    
   

// Añadir el título centrado
$section->addText('CONSIDERANDO QUE:', array('bold' => true, 'size' => 12), array('alignment' => Jc::CENTER, 'spaceAfter' => 240));

    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150,'size' => 12);
    $textRun = $section->addTextRun($paragraphStyle);
     $textRun->addText('El Acuerdo 024 de 1993 establece en su Capítulo XIV las Situaciones Administrativas del empleado público vinculado como profesor, dentro de las cuales, se encuentra la comisión académica, entendida como la asistencia a foros, viajes de estudio, seminarios, congresos, encuentros, cursos o similares en los cuales el profesor representa a la Universidad del Cauca.',array('size' => 12));
 
     $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150,'size' => 12);
    $textRun = $section->addTextRun($paragraphStyle);
     $textRun->addText('El Consejo Superior, máximo órgano de dirección y gobierno de la Universidad del Cauca, mediante comunicado 2.1-1.39/119 del 20 de diciembre de 2022, recomendó a la Dirección Universitaria prever controles en la autorización de las comisiones, con miras a verificar y articular los objetos de las comisiones académicas con los objetivos misionales y estratégicos, procurando, además, la rigurosidad en la aprobación de apoyos con efecto presupuestal.',array('size' => 12));
$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150,'size' => 12);
$textRun = $section->addTextRun($paragraphStyle);

$textRun->addText('La Rectoría y Vicerrectoría Académica con el propósito de promover los principios universitarios, los objetivos del Estatuto del Profesor en cuanto al mejoramiento del nivel educativo y, con la finalidad de salvaguardar los principios de la función administrativa, atinentes a la celeridad, economía, eficacia, eficiencia y responsabilidad, verifica la articulación de las solicitudes de comisión académica de los profesores, con el Proyecto Educativo del Programa – PEP, Proyecto Educativo Institucional – PEI y con el Plan Desarrollo Institucional – PDI 2023-2027 ',array('size' => 12));
$textRun->addText('“Por una Universidad de Excelencia y Solidaria”', array('italic' => true,'size' => 12));
$textRun->addText('.',array('size' => 12));
       
  $textRun = $section->addTextRun($paragraphStyle);
     $textRun->addText('Conforme con lo dispuesto en el parágrafo del artículo 73 del Acuerdo Superior 024 de 1993, modificado por el artículo quinto del Acuerdo Superior 031 de 2020, corresponde a la Vicerrectora Académica autorizar y conceder a los profesores, mediante acto administrativo, comisiones académicas que se desarrollen en el territorio nacional y tengan como finalidad la capacitación, viajes de estudio, asistencia a foros, seminarios, congresos, encuentros, cursos o similares en los cuales el profesor represente a la Universidad del Cauca, previo concepto del Consejo de Facultad.',array('size' => 12));  
     $textRun = $section->addTextRun($paragraphStyle);

  $textRun->addText('Continuación RESOLUCIÓN VRA Nº '.$res.' ('.$fecha_letras.') ', array('italic' => true, 'size' => 9));
      
  $textRun = $section->addTextRun($paragraphStyle);
     $textRun->addText('Mediante la Resolución Rectoral 0901 de 2023, se reglamentaron los requisitos para la autorización y concesión de las comisiones académicas al interior y al exterior del país de los profesores.',array('size' => 12));  
 


// Añadir el párrafo con variables y estilos
$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
$textRun = $section->addTextRun($paragraphStyle);

$textRun->addText('El Consejo de la ' . $facultad_min . ', en sesión del día ' . $fecha_letras_aval . ', avaló la Comisión Académica en el territorio nacional a ', array('size' => 12));
$resultadores->data_seek(0);

// Inicializamos arrays para almacenar los nombres, cédulas y departamentos
$nombres_profesoras = [];
$nombres_profesores = [];
$departamentos = [];

// Ciclo para agregar los nombres y cédulas en los arrays correspondientes
while ($row = $resultadores->fetch_assoc()) {
    $nombre1 = $row['nombre1'];
    $nombre2 = $row['nombre2'];
    $apellido1 = $row['apellido1'];
    $apellido2 = $row['apellido2'];
    $nombre_completo = trim("$nombre1 $nombre2 $apellido1 $apellido2");
    $documento_tercero = formatDocumentNumber($row['documento_tercero']);
    $profesor_con_cedula = $nombre_completo . ' identificad' . ($row['sexo_tercero'] == 'F' ? 'a' : 'o') . ' con cédula de ciudadanía número ' . $documento_tercero;

    // Agrupamos por género
    if ($row['sexo_tercero'] == 'F') {
        $nombres_profesoras[] = $profesor_con_cedula;
    } else {
        $nombres_profesores[] = $profesor_con_cedula;
    }

    // Almacenamos el departamento
    $departamento = $row['depto_nom_propio'];
    if (!in_array($departamento, $departamentos)) {
        $departamentos[] = $departamento;
    }
}

// Generar las frases correspondientes según el número de profesoras y profesores
if (count($nombres_profesoras) > 0 && count($nombres_profesores) > 0) {
    if (count($nombres_profesoras) == 1 && count($nombres_profesores) == 1) {
        // Un profesor y una profesora
        $textRun->addText('la profesora ', array('size' => 12));
        $textRun->addText($nombres_profesoras[0], array('bold' => true, 'size' => 12));
        $textRun->addText(' y el profesor ', array('size' => 12));
        $textRun->addText($nombres_profesores[0], array('bold' => true, 'size' => 12));
    } else if (count($nombres_profesoras) > 1 && count($nombres_profesores) == 1) {
        // Varias profesoras y un profesor
        $todas_profesoras = implode(', ', array_slice($nombres_profesoras, 0, -1)) . ' y ' . end($nombres_profesoras);
        $textRun->addText('las profesoras ', array('size' => 12));
        $textRun->addText($todas_profesoras, array('bold' => true, 'size' => 12));
        $textRun->addText(' y el profesor ', array('size' => 12));
        $textRun->addText($nombres_profesores[0], array('bold' => true, 'size' => 12));
    } else if (count($nombres_profesoras) == 1 && count($nombres_profesores) > 1) {
        // Una profesora y varios profesores
        $todos_profesores = implode(', ', array_slice($nombres_profesores, 0, -1)) . ' y ' . end($nombres_profesores);
        $textRun->addText('la profesora ', array('size' => 12));
        $textRun->addText($nombres_profesoras[0], array('bold' => true, 'size' => 12));
        $textRun->addText(' y los profesores ', array('size' => 12));
        $textRun->addText($todos_profesores, array('bold' => true, 'size' => 12));
    } else {
        // Varias profesoras y varios profesores
        $todas_profesoras = implode(', ', array_slice($nombres_profesoras, 0, -1)) . ' y ' . end($nombres_profesoras);
        $todos_profesores = implode(', ', array_slice($nombres_profesores, 0, -1)) . ' y ' . end($nombres_profesores);
        $textRun->addText('las profesoras ', array('size' => 12));
        $textRun->addText($todas_profesoras, array('bold' => true, 'size' => 12));
        $textRun->addText(' y los profesores ', array('size' => 12));
        $textRun->addText($todos_profesores, array('bold' => true, 'size' => 12));
    }
} else if (count($nombres_profesoras) > 0) {
    // Solo profesoras
    if (count($nombres_profesoras) > 1) {
        $todas_profesoras = implode(', ', array_slice($nombres_profesoras, 0, -1)) . ' y ' . end($nombres_profesoras);
        $textRun->addText('las profesoras ', array('size' => 12));
        $textRun->addText($todas_profesoras, array('bold' => true, 'size' => 12));
    } else {
        $textRun->addText('la profesora ', array('size' => 12));
        $textRun->addText($nombres_profesoras[0], array('bold' => true, 'size' => 12));
    }
} else if (count($nombres_profesores) > 0) {
    // Solo profesores
    if (count($nombres_profesores) > 1) {
        $todos_profesores = implode(', ', array_slice($nombres_profesores, 0, -1)) . ' y ' . end($nombres_profesores);
        $textRun->addText('los profesores ', array('size' => 12));
        $textRun->addText($todos_profesores, array('bold' => true, 'size' => 12));
    } else {
        $textRun->addText('el profesor ', array('size' => 12));
        $textRun->addText($nombres_profesores[0], array('bold' => true, 'size' => 12));
    }
}

// Añadir la lista de departamentos al final
$departamentos_texto = implode(', ', array_slice($departamentos, 0, -1)) . ' y ' . end($departamentos);
$textRun->addText(' adscritos ' . (count($departamentos) > 1 ? 'a los departamentos de ' : 'al departamento de ') . $departamentos_texto . '.', array('size' => 12));

// Añadir el punto final
$textRun->addText('.');

// Añadir el párrafo con variables y estilos
$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150, 'size' => 12);
$textRun = $section->addTextRun($paragraphStyle);

// Añadir la primera parte del texto, incluyendo el evento
$textRun->addText('De conformidad con los documentos remitidos por el presidente del Consejo de Facultad, que soportan la solicitud de la comisión, resulta pertinente para la Universidad la asistencia de los profesores relacionados en el acápite anterior, al evento "', array('size' => 12));
$textRun->addText($evento, array('italic' => true, 'size' => 12));
$textRun->addText('", organizado por ' . $organizado_por . ', ' . $endestinos . $destinos . ' ', array('size' => 12));

// Inicializamos un array para almacenar trabajos y profesores
$trabajos_profesores = [];

// Ciclo para agregar trabajos y profesores
$resultadores->data_seek(0);
while ($row = $resultadores->fetch_assoc()) {
    $nombre1 = $row['nombre1'];
    $nombre2 = $row['nombre2'];
    $apellido1 = $row['apellido1'];
    $apellido2 = $row['apellido2'];
    $nombre_profesor = trim("$nombre1 $nombre2 $apellido1 $apellido2"); // Concatenamos los nombres y apellidos

    $trabajo = $row['nombre_trabajo'];
    $sexo = $row['sexo_tercero']; // Obtenemos el sexo del profesor (M o F)

    // Creamos la cadena con el nombre del profesor
    $profesor_info = ['nombre' => $nombre_profesor, 'sexo' => $sexo];

    // Si el trabajo no es null, lo agregamos al array de trabajos y profesores
    if ($trabajo) {
        if (!isset($trabajos_profesores[$trabajo])) {
            $trabajos_profesores[$trabajo] = [];
        }
        $trabajos_profesores[$trabajo][] = $profesor_info; // Guardamos el array asociativo
    }
}

// Verificar si hay al menos un trabajo antes de agregar el texto "en la medida en que sus participaciones se darán en calidad de ponente"
if (!empty($trabajos_profesores)) {
    $textRun->addText('en la medida en que sus participaciones se darán en calidad de ponentes de los trabajos: ', array('size' => 12));
}

// Agregar los detalles de los trabajos
$primer_trabajo = true;  // Variable para asegurarnos de que el trabajo solo se mencione una vez por grupo de profesores
foreach ($trabajos_profesores as $trabajo => $profesores) {
    // Contamos el número de profesores y determinamos el texto a usar
    $profesor_count = count($profesores);
    
    // Si hay más de un profesor
    if ($profesor_count > 1) {
        $profesores_nombres = array_map(function($profesor) {
            return $profesor['nombre'];
        }, $profesores);
        $profesores_texto = implode(', ', array_slice($profesores_nombres, 0, -1)) . ' y ' . end($profesores_nombres);
        $texto_profesor = ', de los profesores ';
    } else {
        // Solo hay un profesor
        $profesor_sexo = $profesores[0]['sexo'];
        $profesores_texto = $profesores[0]['nombre'];
        $texto_profesor = ($profesor_sexo == 'F') ? ', de la profesora ' : ', del profesor ';
    }

    // Añadir el texto del trabajo y los profesores
    if (!$primer_trabajo) {
        $textRun->addText(', ', array('size' => 12));
    }

    // Separar la parte del texto normal y el nombre del profesor en negrita
    $textRun->addText($trabajo . ' ' . $texto_profesor, array('size' => 12));

    // Añadir el nombre de los profesores en negrita
    $textRun->addText($profesores_texto, array('bold' => true, 'size' => 12));
    
    $primer_trabajo = false;  // Solo para la primera iteración
}

// Añadir el texto final para todos los profesores solo una vez
$textRun->addText('. Siendo este un evento de gran proyección nacional, que le permitirá a los profesores compartir conocimientos y experiencias académicas y profesionales, fortalecer lazos interinstitucionales y dar visibilidad a su programa y al Alma Máter.', array('size' => 12));



 
 
    
// Añadir el texto introductorio una sola vez
$textRun = $section->addTextRun($paragraphStyle);
$textRun->addText('En cumplimiento del deber de generar y socializar la ciencia, la cultura en la docencia, la investigación y la proyección social como fines misionales universitarios, de conformidad con lo establecido en el artículo 117 del Acuerdo Superior 024 de 1993, ' . $nombrarvice . ' encuentra justificada la autorización de la Comisión Académica en el territorio nacional a ', array('size' => 12));

// Inicializar arrays para agrupar profesores por rango de fechas
$fechas_profesores = [];

// Ciclo para agrupar los profesores por rango de fechas
$resultadores->data_seek(0);
while ($row = $resultadores->fetch_assoc()) {
    $nombre1 = $row['nombre1'];
    $nombre2 = $row['nombre2'];
    $apellido1 = $row['apellido1'];
    $apellido2 = $row['apellido2'];
    $nombre_profesor = trim("$nombre1 $nombre2 $apellido1 $apellido2"); // Concatenamos los nombres y apellidos
    $sexo = $row['sexo_tercero']; // Obtenemos el sexo del profesor (M o F)

    $fechainicio1 = $row['fechaINI'];
    $fechafin1 = $row['vence'];

    // Formateamos las fechas
    $fechas_formateadas = obtenerFechasFormateadas($fechainicio1, $fechafin1);

    // Añadimos el profesor al array correspondiente al rango de fechas
    if (!isset($fechas_profesores[$fechas_formateadas])) {
        $fechas_profesores[$fechas_formateadas] = ['F' => [], 'M' => []]; // Inicializamos el array para cada rango
    }
    // Agrupamos por sexo
    $fechas_profesores[$fechas_formateadas][$sexo][] = $nombre_profesor;
}

// Añadir el texto para cada grupo de profesores y sus fechas
foreach ($fechas_profesores as $rango_fechas => $profesores) {
    $profesores_femeninos = $profesores['F'];
    $profesores_masculinos = $profesores['M'];

    // Si hay más de una profesora
    if (count($profesores_femeninos) > 1) {
        $textRun->addText('las profesoras ', array('size' => 12));
        foreach (array_slice($profesores_femeninos, 0, -1) as $profesora) {
            $textRun->addText($profesora, array('size' => 12, 'bold' => true));
            $textRun->addText(', ', array('size' => 12));
        }
        $textRun->addText(end($profesores_femeninos), array('size' => 12, 'bold' => true));
        $textRun->addText(' y ', array('size' => 12));
    } elseif (count($profesores_femeninos) === 1) {
        $textRun->addText('la profesora ', array('size' => 12));
        $textRun->addText($profesores_femeninos[0], array('size' => 12, 'bold' => true));
        $textRun->addText(' ', array('size' => 12));
    }

    // Si hay más de un profesor
    if (count($profesores_masculinos) > 1) {
        $textRun->addText('y los profesores ', array('size' => 12));
        foreach (array_slice($profesores_masculinos, 0, -1) as $profesor) {
            $textRun->addText($profesor, array('size' => 12, 'bold' => true));
            $textRun->addText(', ', array('size' => 12));
        }
        $textRun->addText(end($profesores_masculinos), array('size' => 12, 'bold' => true));
        $textRun->addText(' ', array('size' => 12));
    } elseif (count($profesores_masculinos) === 1) {
        $textRun->addText('y el profesor ', array('size' => 12));
        $textRun->addText($profesores_masculinos[0], array('size' => 12, 'bold' => true));
        $textRun->addText(' ', array('size' => 12));
    }

    // Añadir el rango de fechas
    $textRun->addText($rango_fechas, array('size' => 12));
    $textRun->addText('. ', array('size' => 12));
}



    
    
    

$leftParagraphStyle = array('alignment' => Jc::LEFT, 'spaceAfter' => 150);
$textRun = $section->addTextRun($leftParagraphStyle);

// Añadir el texto "Por lo expuesto," con tamaño 12
$textRun->addText('En mérito de lo expuesto,', array('size' => 12));
 

    
    
    
    
     $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 140,'size' => 12);
   
    
   

$section->addText('RESUELVE:', array('bold' => true, 'size' => 12), array('alignment' => Jc::CENTER));   


    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150,'size' => 12);
    $textRun = $section->addTextRun($paragraphStyle);
    
    
    

  // Añadir el encabezado del artículo
$textRun->addText('ARTÍCULO PRIMERO: ', array('bold' => true, 'size' => 12));
// Añadir el texto introductorio para la comisión académica con el rango de fechas justo después de "territorio nacional"
    
    
$textRun->addText('Autorizar comisión académica en el territorio nacional, ', array('size' => 12));

// Inicializar arrays para agrupar profesores por rango de fechas
$fechas_profesores = [];
$resultadores->data_seek(0);
// Inicializar array para almacenar departamentos únicos
$departamentos = [];

// Ciclo para agrupar los profesores por rango de fechas
while ($row = $resultadores->fetch_assoc()) {
    $nombre1 = $row['nombre1'];
    $nombre2 = $row['nombre2'];
    $apellido1 = $row['apellido1'];
    $apellido2 = $row['apellido2'];
    $nombre_profesor = trim("$nombre1 $nombre2 $apellido1 $apellido2");

    $fechainicio1 = $row['fechaINI'];
    $fechafin1 = $row['vence'];

    // Formateamos las fechas
    $fechas_formateadas = obtenerFechasFormateadas($fechainicio1, $fechafin1);

    // Formateamos el número de documento
    $documento_tercero = formatDocumentNumber($row['documento_tercero']);

    // Determinamos si es 'identificado' o 'identificada' según el género
    $identificado = ($row['sexo_tercero'] == 'M') ? 'identificado' : 'identificada';

    // Creamos la cadena del profesor con nombre y cédula
    $profesor = $nombre_profesor . ', ' . $identificado . ' con cédula de ciudadanía número ' . $documento_tercero;

    // Añadimos el profesor al array del rango de fechas correspondiente
    if (!isset($fechas_profesores[$fechas_formateadas])) {
        $fechas_profesores[$fechas_formateadas] = [];
    }
    $fechas_profesores[$fechas_formateadas][] = $profesor;

    // Añadimos el departamento al array si no está ya presente
    $departamento = $row['depto_nom_propio'];
    if (!in_array($departamento, $departamentos)) {
        $departamentos[] = $departamento;
    }
}

// Inicializar variables para el texto de los profesores
$profesores_texto_final = '';

// Separar profesores según género y generar el texto final para cada rango de fechas
foreach ($fechas_profesores as $rango_fechas => $profesores) {
    // Inicializar arrays para nombres de profesoras y profesores dentro del ciclo
    $nombres_profesoras = [];
    $nombres_profesores = [];

    // Agregar el rango de fechas
    $profesores_texto_final .= $rango_fechas . ', ';

    // Separar profesores según género
    foreach ($profesores as $profesor) {
        if (strpos($profesor, 'identificada') !== false) {
            $nombres_profesoras[] = $profesor;
        } else {
            $nombres_profesores[] = $profesor;
        }
    }

    // Generar el texto final con el formato requerido
    $profesores_texto = '';
    if (count($nombres_profesoras) > 0 && count($nombres_profesores) > 0) {
        if (count($nombres_profesoras) == 1) {
            $todas_profesoras = 'a la profesora ' . $nombres_profesoras[0];
        } else {
            $todas_profesoras = 'a las profesoras ' . implode(', ', array_slice($nombres_profesoras, 0, -1)) . ' y ' . end($nombres_profesoras);
        }

        if (count($nombres_profesores) == 1) {
            $todos_profesores = 'el profesor ' . $nombres_profesores[0];
        } else {
            $todos_profesores = 'los profesores ' . implode(', ', array_slice($nombres_profesores, 0, -1)) . ' y ' . end($nombres_profesores);
        }

        $profesores_texto .= $todas_profesoras . ' y ' . $todos_profesores;

    } elseif (count($nombres_profesoras) > 0) {
        if (count($nombres_profesoras) == 1) {
            $profesores_texto .= 'a la profesora ' . $nombres_profesoras[0];
        } else {
            $profesores_texto .= 'a las profesoras ' . implode(', ', $nombres_profesoras);
        }

    } elseif (count($nombres_profesores) > 0) {
        if (count($nombres_profesores) == 1) {
            $profesores_texto .= 'el profesor ' . $nombres_profesores[0];
        } else {
            $profesores_texto .= 'los profesores ' . implode(', ', $nombres_profesores);
        }
    }

    // Añadir el texto de los profesores al texto final
    $profesores_texto_final .= $profesores_texto . ' ';
}

// Verificar si hay un solo departamento o varios
if (count($departamentos) > 1) {
    // Múltiples departamentos
    $departamentos_texto = 'adscritos a los Departamentos de ' . implode(', ', array_slice($departamentos, 0, -1)) . ' y ' . end($departamentos);
} else {
    // Un solo departamento
    $departamentos_texto = 'adscritos al Departamento de ' . $departamentos[0];
}

// Ahora añadir el texto final de los profesores con sus departamentos
$texto_final = $profesores_texto_final . $departamentos_texto . ' de la ' . $facultad_min;

// Agregar el texto final al documento
$textRun->addText($texto_final . ', con el fin de participar en el evento "', array('size' => 12));

// Agregar el nombre del evento en cursiva
$textRun->addText($evento, array('italic' => true, 'size' => 12));

// Continuar con el texto del organizador y el destino
$textRun->addText('", organizado por ' . $organizado_por . ', en ' . $destinos . '.', array('size' => 12));

    $textRun = $section->addTextRun($paragraphStyle);
 $textRun->addTextBreak(); // Añade un salto de línea

  
  $textRun->addText('Continuación RESOLUCIÓN VRA Nº '.$res.' ('.$fecha_letras.') ', array('italic' => true, 'size' => 9));
    
// $textRun->addText(', con el fin de participar en el evento "' . $evento . '", organizado por ' . $organizado_por . ', ' . $endestinos . $destinos . '.',array('size' => 12));
$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
    
// Inicializar un array para almacenar los párrafos
$parrafos = [];

// Inicializar un array para almacenar las condiciones
$condiciones = [];

// Suponiendo que $resultadores es el resultado de una consulta a la base de datos con los profesores
$resultadores->data_seek(0);
while ($row = $resultadores->fetch_assoc()) {
    $nombre_profesor = trim($row['nombre1'] . ' ' . $row['nombre2'] . ' ' . $row['apellido1'] . ' ' . $row['apellido2']);
    $viaticos = $row['viaticos'];
    $tiquetes = $row['tiquetes'];
    $inscripcion = $row['inscripcion'];
    $cargo_a = $row['cargo_a'];
    $cargo_admin = $row['cargo_admin'];
    $cdp = $row['cdp'];
    $valor = $row['valor'];

    // Generar el texto de erogaciones
    $texto_erogaciones = generarTextoErogaciones($viaticos, $tiquetes, $inscripcion, $cargo_a, $cdp, $valor, $cargo_admin);

    // Crear un identificador único para las condiciones
    $identificador_condiciones = "$viaticos|$tiquetes|$inscripcion|$cargo_a";

    // Agrupar los profesores con las mismas condiciones
    if (!isset($condiciones[$identificador_condiciones])) {
        $condiciones[$identificador_condiciones] = [
            'texto' => $texto_erogaciones,
            'profesores' => []
        ];
    }
    $condiciones[$identificador_condiciones]['profesores'][] = $nombre_profesor;
}

// Generar párrafos
$contador = 1;
foreach ($condiciones as $grupo) {
    $profesores_texto = implode(', ', array_slice($grupo['profesores'], 0, -1));
    if (count($grupo['profesores']) > 1) {
        $profesores_texto .= ' y ' . end($grupo['profesores']);
    } else {
        $profesores_texto = $grupo['profesores'][0];
    }

    // Añadir el texto en negrilla solo para "PARÁGRAFO X:"
    $parrafos[] = array(
        'negrilla' => "PARÁGRAFO $contador: ", // Parte en negrilla
        'texto' => "La comisión autorizada " . $grupo['texto'] . ", para " . $profesores_texto . '.'
    );
    $contador++;
}

// Añadir los párrafos al documento
foreach ($parrafos as $parrafo) {
    // Añadir "PARÁGRAFO X:" en negrilla
   
    $textRun->addText($parrafo['negrilla'], array('bold' => true, 'size' => 12));
    
    // Añadir el resto del texto
    $textRun->addText($parrafo['texto'], array('size' => 12));
$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
    // Agregar un salto de línea después de cada párrafo
}


//termina paragrafoerogacoines
   
    
    $textRun->addText('ARTÍCULO SEGUNDO: ', array('bold' => true,'size' => 12));
       
    
if ($cargo_admin == "DECANO") {
    $textRun->addText('Como deber especial, el Decano comisionado rendirá a la Vicerrectoría Académica, un informe escrito dentro de los diez (10) días siguientes al vencimiento de la comisión. En el informe se presentarán los aspectos pertinentes a los objetivos específicos de la unidad académica y generales de la Institución.',array('size' => 12));
} else {
    $textRun->addText('Como deber especial los profesores rendirán a la Decanatura de su Facultad con copia en físico a esta Vicerrectoría, un informe escrito dentro de los diez (10) días siguientes al vencimiento de la comisión. En el informe se presentarán los aspectos pertinentes a los objetivos específicos de la unidad académica y generales de la Institución, de lo cual, se dejará constancia en el Acta de Reunión de Departamento.',array('size' => 12));
}

    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
    
    $textRun->addText('PARÁGRAFO: ', array('bold' => true,'size' => 12));
       
    
     $textRun->addText('La Administración Universitaria se abstendrá de tramitar nueva Comisión Académica a los profesores de no satisfacer los requerimientos estipulados en la presente Resolución, por cuyo cumplimiento velará la Decanatura de su respectiva Facultad.',array('size' => 12)); 
    
    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
    
    $textRun->addText('ARTÍCULO TERCERO: ', array('bold' => true,'size' => 12));
       
    
//enviar copia
    
    // Inicializar array para almacenar los nombres y correos de los profesores
$profesores_interesados = [];

// Ciclo para recorrer todos los profesores y obtener sus nombres y correos
$resultadores->data_seek(0);
while ($row = $resultadores->fetch_assoc()) {
    // Convertir a nombre propio y manejar la Ñ correctamente
    $apellido1p = ucfirst(mb_strtolower($row['apellido1'], 'UTF-8'));
    $apellido2p = ucfirst(mb_strtolower($row['apellido2'], 'UTF-8'));
    $nombre1p = ucfirst(mb_strtolower($row['nombre1'], 'UTF-8'));
    $nombre2p = ucfirst(mb_strtolower($row['nombre2'], 'UTF-8'));

    // Formatear los nombres ordenados
    $nombresOrdenadosp = trim("$nombre1p $nombre2p $apellido1p $apellido2p");

    // Obtener el correo del profesor
    $email_profesor = $row['email_profesor'];

    // Añadir el nombre completo y correo al array de interesados
    $profesores_interesados[] = "$nombresOrdenadosp ($email_profesor)";
}


// Unir los nombres de los profesores con comas y "y" antes del último
if (count($profesores_interesados) > 1) {
    $texto_profesores_interesados = implode(', ', array_slice($profesores_interesados, 0, -1)) . ' y ' . end($profesores_interesados);
} else {
    $texto_profesores_interesados = $profesores_interesados[0];
}

// Añadir el texto final al documento
$textRun->addText('Enviar copia del presente acto administrativo a la Oficina de Relaciones Interinstitucionales e Internacionales (relacionesinter@unicauca.edu.co), ' . $facultad_min . ' (' . $email_fac . '), División de Gestión del Talento Humano (rhumanos@unicauca.edu.co), División de Gestión Financiera (financiera@unicauca.edu.co), Vicerrectoría Administrativa (viceadm@unicauca.edu.co), al Área de Seguridad y Salud en el Trabajo (saludocu@unicauca.edu.co) y a los interesados: ' . $texto_profesores_interesados . '.', array('size' => 12));

//termina eviar copia
    
  $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
      $textRun->addText('Se expide en Popayán (Cauca), '. $dia_en_letras . 'del mes de '.$meses[date($mes)].' de '.$anio.'.',array('size' => 12));

      $section->addTextBreak(1);

// Añadir el título centrado
$section->addText('COMUNÍQUESE Y CÚMPLASE,', array('bold' => true, 'size' => 12), array('alignment' => Jc::CENTER, 'spaceAfter' => 10)); 
      
    $section->addTextBreak(1);
$section->addTextBreak(1);

    
// Añadir el título centrado
    $section->addText($nombre_vice, array('bold' => true, 'size' => 12), array('alignment' => Jc::CENTER, 'spaceAfter' => 0));   

    $section->addText($cargov, array('bold' => false, 'size' => 12), array('alignment' => Jc::CENTER));   

// Estilos de párrafo
$paragraphStyle = array('alignment' => Jc::LEFT, 'spaceAfter' => 0);

// Crear un TextRun para "Proyectó" y "Revisó" con el mínimo espacio entre ellos
$textRun = $section->addTextRun($paragraphStyle);
$textRun->addText('Proyectó: '.$elaboro, array('size' => 8));
$textRun->addTextBreak(); // Añadir un salto de línea mínimo
$textRun->addText('Revisó: '.$reviso, array('size' => 8));


} else {
    // Añadir mensaje de error si no se encontraron resultados
    $section->addText('No se encontraron resultados para la consulta.', array('alignment' => Jc::CENTER));
}
// Convertir la fecha a formato de PHP
$fecha_formateada = date('m_Y', strtotime($fechainicio1)); // Formatear a mm_aaaa

// Generar el nombre del archivo

$filename = "Resolucion grup - $tipo_estudio-$destinos-$fecha_formateada.docx";
// Encabezados HTTP para la descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');

// Guardar el archivo en formato DOCX y enviarlo al navegador
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('php://output');

exit; // Terminar el script para evitar cualquier salida adicional
?>