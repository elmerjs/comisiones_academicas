<?php 
require 'vendor/autoload.php';
require 'cn.php';  // Archivo para la conexión a la base de datos

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\SimpleType\Jc;
$anioactual = date('Y');

// Crear una nueva instancia de PHPWord
$phpWord = new PhpWord();

// Configurar el idioma del documento a español
$phpWord->getSettings()->setThemeFontLang(new Language(Language::ES_ES));

// Añadir una nueva sección con tamaño de oficio y márgenes personalizados
$sectionStyle = array(
    'paperSize' => 'Folio',
    'marginTop' => 3000,  // 5 cm
    'marginLeft' => 1600, // 3 cm
    'marginRight' => 1600, // 3 cm
    'marginBottom' => 2500, // 3 cm
);
$section = $phpWord->addSection($sectionStyle);
$header = $section->addHeader();
$table = $header->addTable();
$table->addRow();
$cell = $table->addCell();
$cell->addText("\n\n\n"); // Tres saltos de línea
$cell->addText("\n\n\n"); // Tres saltos de línea
$cell->addText("\n\n\n"); // Tres saltos de línea

$cell->addText("\n\n\n"); // Tres saltos de línea

$cell->addText(
    'Continuación Resolución Rectoral No.                       de ' . $anioactual,
    array('size' => 9, 'italic' => true),
    array('alignment' => Jc::CENTER, 'spaceAfter' => 0)
);

// Consulta SQL
$id_res=$_GET['id'];
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
WHERE comision_academica.id = '$id_res'
GROUP BY comision_academica.id;";

$resultadores = $con->query($consultares);

if ($resultadores->num_rows > 0) {
    $row = $resultadores->fetch_assoc();
    $res = $row['No_resolucion'];
    $tipo_estudio = $row['tipo_estudio'];
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

if (empty($justificacion)) {
    $justificacion = "lo que permitirá impactar de manera positiva en los procesos de formación académica del departamento al cual pertenece";
}$ellasciudades =  $row['num_destinos'];
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
    $texto = ' no genera erogaciones a la Universidad del Cauca, por concepto de viáticos, inscripción o tiquetes.';

    if ($viaticos == 1 || $tiquetes == 1 || $inscripcion == 1) {
        $texto = ' genera erogaciones a la Universidad del Cauca, por concepto de  gastos de ';

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
    
    // Añadir el texto inicial en negrilla
    $section->addText('2.1-4.16', array('bold' => true));

    // Añadir el título centrado
    $section->addText('RESOLUCIÓN RECTORAL No.                          DE  ' . $anio, array('bold' => true, 'size' => 11), array('alignment' => Jc::CENTER,'spaceAfter' => 0));
 // Añadir el título centrado
    $section->addText('(                       )', array('bold' => true, 'size' => 11), array('alignment' => Jc::CENTER));


    // Añadir el primer párrafo
    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
    
    if (in_array($cargo_admin, ['JEFE', 'DECANO', 'DIRECTOR'])) {
    
   $textRun->addText('Por la cual se concede comisión académica al exterior a un Profesor con Cargo Académico Administrativo.', null);

} else {
           $textRun->addText('Por la cual se concede comisión académica al exterior ', null);
           $textRun->addText($saludo, array('bold' => false));    
         $textRun->addText(' ' . $nombre_profesor, array('bold' => true));
        $textRun->addText('.');

}
     // Añadir el segundo párrafo
    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 10);
    $textRun = $section->addTextRun($paragraphStyle);
    
if ($tipo_rector == 'Rector') {
    $textRun->addText($el_la);
        $textRun->addText('RECTOR DE LA UNIVERSIDAD DEL CAUCA', array('bold' => true));
    
                $textRun->addText(' en uso de sus competencias establecidas en el Artículo 23 del Acuerdo Superior 105 de 1993 o Estatuto General de la Universidad del Cauca, modificado por el Acuerdo Superior 025 de 2020, y');

    } else{
       
        $textRun->addText($el_la);
        $textRun->addText( $parrafo2r, array('bold' => true));
        $textRun->addText(' een uso de sus competencias establecidas en el Artículo 23 del Acuerdo Superior 105 de 1993 o Estatuto General de la Universidad del Cauca, modificado por el Acuerdo Superior 025 de 2020,  conforme a la ' . $resol_encargo_rector . ', y');
}
     // Añadir el título centrado
    
    $section->addTextBreak(1);

   
      
// Añadir el título centrado
$section->addText('CONSIDERANDO QUE:', array('bold' => true, 'size' => 11), array('alignment' => Jc::CENTER, 'spaceAfter' => 10));
$section->addTextBreak(1);

    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
     $textRun->addText('El Acuerdo 024 de 1993 establece en su Capítulo XIV las Situaciones Administrativas del empleado público vinculado como profesor, dentro de las cuales, se encuentra la comisión académica, entendida como la asistencia a foros, viajes de estudio, seminarios, congresos, encuentros, cursos o similares en los cuales el profesor representa a la Universidad del Cauca.');
 
     $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
     $textRun->addText('De conformidad con el Artículo 23 del Acuerdo Superior 105 de 1993 o Estatuto General de la Universidad del Cauca, modificado por el Artículo 23 del Acuerdo Superior 025 de 2020, corresponde al Rector autorizar las comisiones académicas al exterior no conducentes a título y no superiores a un año, y presentar al Consejo Superior la solicitud de concesión de comisiones al exterior para empleados administrativos y docentes en ejercicio de cargos administrativos.');
    
      $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
     $textRun->addText('Posteriormente, el Consejo Superior mediante Acuerdo Superior 047 de 2022, delegó en el Rector de la Universidad del Cauca, la función de autorizar las comisiones al exterior de los profesores universitarios que ejercen cargos de administración académica, recomendando la revisión del objeto, pertinencia y financiación de la comisión.');
    
      $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
     $textRun->addText('El Consejo Superior máximo órgano de dirección y gobierno de la Universidad del Cauca, mediante comunicado 2.1-1.39/119 del 20 de diciembre de 2022, recomendó a la Dirección Universitaria prever controles en la autorización de las comisiones, con miras a verificar y articular los objetos de las comisiones académicas con los objetivos misionales y estratégicos, procurando además, por la rigurosidad en la aprobación de apoyos con efecto presupuestal.');
    
    
      $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
     $textRun->addText('Por medio de Resolución Académica N° 025 de 2023 emitida por el Consejo Académico, se delegó en la Vicerrectoría Académica la función prevista en el artículo 73 del Acuerdo 024 de 1993, relativa a la competencia para avalar las comisiones académicas al exterior de los profesores universitarios.');
    
         $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
     $textRun->addText('Según lo expuesto, la concesión y autorización de comisiones académicas al exterior es competencia del Rector, con previo aval del respectivo Consejo de Facultad y de la Vicerrectoría Académica.');
    
     
    
$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
$textRun = $section->addTextRun($paragraphStyle);

$textRun->addText('Mediante la Resolución Rectoral 0901 de 2023, se reglamentaron los requisitos para la autorización y concesión de las comisiones académicas, tanto al interior como al exterior del país para los profesores, conforme a la cual, además, la Rectoría y Vicerrectoría Académica con el propósito de promover los principios universitarios, los objetivos del Estatuto del Profesor en cuanto al mejoramiento del nivel educativo y, con la finalidad de salvaguardar los principios de la función administrativa, atinentes a la celeridad, economía, eficacia, eficiencia y responsabilidad, verifican la articulación de las solicitudes de comisión académica de los profesores, con el Proyecto Educativo del Programa – PEP, Proyecto Educativo Institucional – PEI y con el Plan Desarrollo Institucional – PDI 2023-2027 ', null);
$textRun->addText('“Por una Universidad de Excelencia y Solidaria”', array('italic' => true));
$textRun->addText('.', null);
 
// Añadir el párrafo con variables y estilos
$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
$textRun = $section->addTextRun($paragraphStyle);

$textRun->addText('El Consejo de la ' . $facultad_min . ', en sesión del día ' . $fecha_letras_aval . ', avaló la Comisión Académica al exterior ' . $saludode . ' ', null);
$textRun->addText($nombre_profesor, array('bold' => true));
$textRun->addText(', ', null);
$textRun->addText($identificado . ' con la cédula de ciudadanía número ', null);
$textRun->addText($documento_tercero, array('bold' => true));
$textRun->addText(', ' . $adscrito . ' al departamento de ' . $depto_nom_propio . '.', null);



// Añadir el párrafo con variables y estilos
// Configuración de estilo para justificación
$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
$textRun = $section->addTextRun($paragraphStyle);

// Agregar el texto con formato específico
$textRun->addText('De conformidad con los documentos remitidos por el presidente del Consejo de Facultad, que soportan la solicitud de la comisión, resulta pertinente para la Universidad la asistencia ', null);

$textRun->addText($saludode . ' ', null);
$textRun->addText($nombre_profesor, array('bold' => true));
$textRun->addText(', para participar  ' . $coneltrabajo . ' en ', null);
$textRun->addText($evento, array('italic' => true));
$textRun->addText(', organizado por ' . $organizado_por . ', ' . $endestinos . $destinos . ';' . $justificacion . '.', null);


// Añadir el nuevo párrafo
$textRun = $section->addTextRun($paragraphStyle);
$textRun->addText('En cumplimiento del deber de generar y socializar la ciencia, la cultura en la docencia, la investigación y  la proyección social como fines misionales universitarios, se justifica la autorización de una comisión académica, ' . obtenerFechasFormateadas($fechainicio1, $fechafin1) . ', de conformidad con lo establecido en el artículo 117 del Acuerdo Superior 024 de 1993, para que ');
$textRun->addText($saludo_el_la, null);
$textRun->addText(' ' . $nombre_profesor, array('bold' => true));
$textRun->addText(' participe en la misión académica mencionada.', null);
$textRun = $section->addTextRun($paragraphStyle);
            $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 10);

 $textRun->addText('Por lo expuesto,');  
 

// Añadir el título centrado
$section->addText('RESUELVE:', array('bold' => true, 'size' => 11), array('alignment' => Jc::CENTER,'spaceAfter' => 10)); 
$section->addTextBreak(1);


    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
    
    $textRun->addText('ARTÍCULO PRIMERO: ', array('bold' => true));
       
    
     $textRun->addText('Conceder comisión académica al exterior, '. obtenerFechasFormateadas($fechainicio1, $fechafin1) . ', ',null);
    
$textRun->addText($saludo. ' ', null);
$textRun->addText($nombre_profesor, array('bold' => true));
$textRun->addText(', ', null);
$textRun->addText($identificado . ' con la cédula de ciudadanía número ', null);
$textRun->addText($documento_tercero, array('bold' => true));
$textRun->addText(', ' . $adscrito . ' al departamento de ' . $depto_nom_propio, null); 
 $textRun->addText(', con el fin de participar en el evento ', null);
$textRun->addText($evento, array('italic' => true));
$textRun->addText(', organizado por ' . $organizado_por . ', ' . $endestinos . $destinos . '.', null);

$paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
    
    $textRun->addText('PARÁGRAFO: ', array('bold' => true));
       
    
     $textRun->addText('La comisión autorizada '. $texto_erogaciones . '.',null);    
    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
    
    $textRun->addText('ARTÍCULO SEGUNDO: ', array('bold' => true));
       
    
if ($cargo_admin == "DECANO") {
    $textRun->addText('Como deber especial, el Decano comisionado rendirá a la Vicerrectoría Académica, un informe escrito dentro de los diez (10) días siguientes al vencimiento de la comisión. En el informe se presentarán los aspectos pertinentes a los objetivos específicos de la unidad académica y generales de la Institución.', null);
} else {
    $textRun->addText('Como deber especial, ' . $comisionado . ' rendirá a la Decanatura de su Facultad con copia a esta Vicerrectoría, un informe escrito dentro de los diez (10) días siguientes al vencimiento de la comisión. En el informe se presentarán los aspectos pertinentes a los objetivos específicos de la unidad académica y generales de la Institución, de lo cual se dejará constancia en el Acta de Reunión de Departamento.', null);
}

    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
    
    $textRun->addText('PARÁGRAFO: ', array('bold' => true));
       
    
     $textRun->addText('La Administración Universitaria se abstendrá de tramitar nueva Comisión Académica '. $saludo . 'de no satisfacer los requerimientos estipulados en la presente Resolución, por cuyo cumplimiento velará la Decanatura de su respectiva Facultad.',null); 
    
    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
    
    $textRun->addText('ARTÍCULO TERCERO: ', array('bold' => true));
       
    
     $textRun->addText('Enviar copia del presente acto administrativo a la Oficina de Relaciones Interinstitucionales e Internacionales (relacionesinter@unicauca.edu.co), '. $facultad_min . ' ('.$email_fac. '), División de Gestión del Talento Humano (rhumanos@unicauca.edu.co), División de Gestión Financiera (financiera@unicauca.edu.co), al Área de Seguridad y Salud en el Trabajo (saludocu@unicauca.edu.co) y '.$alinteresado.', '.$nombresOrdenadosp.' ('.$email_profesor.').',null);
    
  $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150);
    $textRun = $section->addTextRun($paragraphStyle);
      $textRun->addText('Se expide en Popayán (Cauca), ',null);

      
// Añadir el título centrado
$section->addText('COMUNÍQUESE Y CÚMPLASE,', array('bold' => true, 'size' => 11), array('alignment' => Jc::CENTER, 'spaceAfter' => 10)); 
    
    $section->addTextBreak(1);
$section->addTextBreak(1);
$section->addTextBreak(1);

// Añadir el título centrado
    $rector_mayusculas = mb_strtoupper($rector, 'UTF-8');

    $section->addText($rector_mayusculas, array('bold' => true, 'size' => 11), array('alignment' => Jc::CENTER, 'spaceAfter' => 0));   

    $section->addText($cargor, array('bold' => true, 'size' => 11), array('alignment' => Jc::CENTER, 'spaceAfter' => 0));  

// Estilos de párrafo
$paragraphStyle = array('alignment' => Jc::LEFT, 'spaceAfter' => 0);

// Crear un TextRun para "Proyectó" y "Revisó" con el mínimo espacio entre ellos
$textRun = $section->addTextRun($paragraphStyle);
$textRun->addText('Autorizó: '.$autorizo, array('size' => 8, 'italic' => true)); // Aplicar cursiva al texto de "Autorizó"
$textRun->addTextBreak(); // Añadir un salto de línea mínimo
$textRun->addText('Revisó: '.$reviso, array('size' => 8, 'italic' => true)); // Aplicar cursiva al texto de "Revisó"
$textRun->addTextBreak(); // Añadir un salto de línea mínimo
    $elaboro_nombre_propio = ucwords(strtolower($elaboro));

$textRun->addText('Proyectó: '.$elaboro_nombre_propio, array('size' => 8, 'italic' => true)); // Aplicar cursiva al texto de "Proyectó"

} else {
    // Añadir mensaje de error si no se encontraron resultados
    $section->addText('No se encontraron resultados para la consulta.', array('alignment' => Jc::CENTER));
}
$filename = "Resolucion - $tipo_estudio - $apellido1 $nombre1-$mes_anio.docx";

// Encabezados HTTP para la descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');

// Guardar el archivo en formato DOCX y enviarlo al navegador
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('php://output');

exit; // Terminar el script para evitar cualquier salida adicional
?>