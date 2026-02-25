<?php
// Conexión a la base de datos
require 'vendor/autoload.php';
//require 'cn.php';  // Archivo para la conexión a la base de datos

$con = mysqli_connect('localhost', 'root', '', 'comisiones_academicas');

// Verificar conexión
if (!$con) {
    die('Error de conexión: ' . mysqli_connect_error());
}


use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\SimpleType\Jc;
$anioactual = date('Y');

// Crear una nueva instancia de PHPWord


$id = $_GET['id'] ?? null;
$cargo = $_GET['cargo'] ?? null;
$cedula = $_GET['cedula'] ?? null;
$oficio = $_GET['oficio'] ?? null;

// Validar que los parámetros no estén vacíos
if (!$id || !$cargo || !$cedula || !$oficio) {
    die('Error: Faltan datos requeridos.');
}

// Usar consultas preparadas para evitar inyección SQL
$stmt = $con->prepare("SELECT * FROM tercero WHERE documento_tercero = ?");
$stmt->bind_param('s', $cedula);
$stmt->execute();
$resultTercero = $stmt->get_result();

// Verificar si la consulta se ejecutó correctamente
if (!$resultTercero) {
    die('Error en la consulta de datos del tercero: ' . mysqli_error($con));
}

// Verificar si se encontró al tercero
if ($resultTercero->num_rows === 0) {
    echo "<script>alert('Error: El tercero con la cédula proporcionada no existe.'); window.history.back();</script>";
    exit;
}

// Si el tercero existe, obtener sus datos
$tercero = mysqli_fetch_assoc($resultTercero);

// Guardar las variables del tercero con el prefijo 'encargado_'
$encargado_documento_tercero = $tercero['documento_tercero'];//utlizado
$encargado_nombre_completo = $tercero['nombre_completo'];
$encargado_apellido1 = $tercero['apellido1'];//utlizado
$encargado_apellido2 = $tercero['apellido2'];//utlizado
$encargado_nombre1 = $tercero['nombre1'];//utlizado
$encargado_nombre2 = $tercero['nombre2'];//utlizado
$nombre_completo_encargado = strtoupper(trim($encargado_nombre1 . ' ' . $encargado_nombre2 . ' ' . $encargado_apellido1 . ' ' . $encargado_apellido2)); //utlizado
$encargado_fk_depto = $tercero['fk_depto'];
$encargado_vincul = $tercero['vincul'];
$encargado_sexo = $tercero['sexo'];//usado
$el_la_encargado = '';//usado

if ($encargado_sexo == 'M') {//usado
    $el_la_encargado = 'el profesor';
    $identif_encargo = 'identificado';
    $el_encargado= ' el encargado '; 
    $al_profesor = ' al profesor ';
} elseif ($encargado_sexo == 'F') {
    $el_la_encargado = 'la profesora';
    $identif_encargo = 'identificada';
    $el_encargado= ' la encargada '; 
    $al_profesor = ' a la profesora ';

} else {//usado
    $el_la_encargado = 'el (la) profesor(a)';
    $identif_encargo = 'identificado(a)';
    $el_encargado= ' encargado(a) '; 
     $al_profesor = ' al profesor(a) ';

}
$encargado_estado = $tercero['estado'];
$encargado_vinculacion = $tercero['vinculacion'];
$encargado_cargo_admin = $tercero['cargo_admin'];
$encargado_email = $tercero['email'];


$id_res = $id;
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
    
    // Asignación de variables del comisionado
    $res = $row['No_resolucion'];
    $tipo_estudio = $row['tipo_estudio'];//utilizad
    $ubicacion_estudio = '';//utilizad
    $tipores = '';
  $tipo_vice = $row['tipo_vice'];
    $sexo_vice = $row['sexo_vice'];
    $tipo_rector = $row['tipo_rector'];
$sexo_rector = $row['sexo_rector'];

        if ($tipo_estudio == 'INT') {//utilizad
            $ubicacion_estudio = 'interior del país';
            $tipores = 'VRA - ' . $res;
            $firma_sol_encargo = $row['nom_propio_vice'];
            
            
            
              if ($tipo_vice == 'propiedad') {
                if ($sexo_vice == 'F') {
                    $cargov = "Vicerrectora Académica";

                } else {
                    $cargov = "Vicerrector Académico";
                }
            } elseif ($tipo_vice == 'Encargado') {
                if ($sexo_vice == 'F') {
                    $cargov = "Vicerrectora Académica (E)";

                } else {
                            $cargov = "Vicerrector Académico (E)";
                }
            } elseif ($tipo_vice == 'delegatario') {
                if ($sexo_vice == 'F') {
                            $cargov = "Vicerrectora Académica (D)";

                } else {
                            $cargov = "Vicerrector Académico (D)";

                }
            }


            

        } elseif ($tipo_estudio == 'EXT') {//utilizad
            $ubicacion_estudio = 'exterior';
            $tipores = $res;
            $firma_sol_encargo = $row['nom_propio_vice'];
            
                  if ($tipo_vice == 'propiedad') {
                if ($sexo_vice == 'F') {
                    $cargov = "Vicerrectora Académica";

                } else {
                    $cargov = "Vicerrector Académico";
                }
            } elseif ($tipo_vice == 'Encargado') {
                if ($sexo_vice == 'F') {
                    $cargov = "Vicerrectora Académica (E)";

                } else {
                            $cargov = "Vicerrector Académico (E)";
                }
            } elseif ($tipo_vice == 'delegatario') {
                if ($sexo_vice == 'F') {
                            $cargov = "Vicerrectora Académica (D)";

                } else {
                            $cargov = "Vicerrector Académico (D)";

                }
            }


            

            
            
            
            
        }    
    
  
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
    $depto_nom_propio = $row['depto_nom_propio'];//utilizado
    // Establecer las variables según el sexo del profesor
    if ($sexo_tercero == 'M') {
        $saludo = "al profesor ";
        $saludode = "del profesor ";
        $adscrito = " adscrito ";//utilizado
        $saludo_el_la = "El profesor";//utilizado
        $identificado = "identificado";    
        $alinteresado = "al interesado";
        $comisionado = "el profesor comisionado";
    } elseif ($sexo_tercero == 'F') {
        $saludo = "a la profesora ";
        $saludode = "de la profesora ";
        $adscrito = " adscrita ";//utilizado
        $saludo_el_la = "La profesora"; //utilizado
        $identificado = "identificada";    
        $alinteresado = "a la interesada";
        $comisionado = "la profesora comisionada";
    } else {
        $saludo = "a el(la) profesor(a)";
        $saludode = "del(a) profesor(a) ";
        $adscrito = " adscrito(a) ";//utilizado
        $saludo_el_la = "El(la) profesor(a)";//utilizado
        $identificado = "identificado(a)";    
        $alinteresado = "a el(la) interesado(a)";    
        $comisionado = "el profesor comisionado";
    }

    // Datos del trabajo y evento
    $trabajo = $row['nombre_trabajo'];
$evento = $row['evento'];//utilziado

    if ($trabajo == null) {
        $coneltrabajo = " como participante ";
    } else {
        $coneltrabajo = " como ponente del trabajo «" . $trabajo . "»";
    }
    
    $organizado_por = $row['organizado_por'];//utiilzado
    $destinos = $row['destinos'];//utilizado
    $justificacion = $row['justificacion'];
    $ellasciudades = $row['num_destinos'];

    if ($ellasciudades == 1) {
        $endestinos = "en la ciudad de ";//utilizado
    } else {
        $endestinos = "en las ciudades de ";
    }

    // Fechas
    $fechainicio1 = $row['fechaINI'];
    $fechaiComoEntero = strtotime($fechainicio1);

// Formatear la fecha en el formato mm_aaaa
$mm_aaaa = date('m_Y', $fechaiComoEntero);
    $fechafin1 = $row['vence'];
    $viaticos = $row['viaticos'];
    $email_profesor = $row['email_profesor'];

    // Para los nombres en el orden: nombre1 nombre2 apellido1 apellido2
    $nombre_profesor = "$nombre1 $nombre2 $apellido1 $apellido2";
    $apellido1p = mb_convert_case(strtolower($row['apellido1']), MB_CASE_TITLE, "UTF-8");
    $apellido2p = mb_convert_case(strtolower($row['apellido2']), MB_CASE_TITLE, "UTF-8");
    $nombre1p = mb_convert_case(strtolower($row['nombre1']), MB_CASE_TITLE, "UTF-8");
    $nombre2p = mb_convert_case(strtolower($row['nombre2']), MB_CASE_TITLE, "UTF-8");

    // Para los nombres en el orden: nombre1 nombre2 apellido1 apellido2
    $nombresOrdenadosp = "$nombre1p $nombre2p $apellido1p $apellido2p";

    // Fechas
    $fechres = date("d/m/Y", strtotime($row['fecha_resolucion']));
    $fecha = $row['fecha_resolucion'];
    $fechaComoEntero = strtotime($fecha);
    $dia = date("d", $fechaComoEntero);
    $mes = date("m", $fechaComoEntero);
    $anio = date("Y", $fechaComoEntero);
    $mesTexto = '';

    switch ($mes) {//utilizado
        case 1: $mesTexto = 'enero'; break;
        case 2: $mesTexto = 'febrero'; break;
        case 3: $mesTexto = 'marzo'; break;
        case 4: $mesTexto = 'abril'; break;
        case 5: $mesTexto = 'mayo'; break;
        case 6: $mesTexto = 'junio'; break;
        case 7: $mesTexto = 'julio'; break;
        case 8: $mesTexto = 'agosto'; break;
        case 9: $mesTexto = 'septiembre'; break;
        case 10: $mesTexto = 'octubre'; break;
        case 11: $mesTexto = 'noviembre'; break;
        case 12: $mesTexto = 'diciembre'; break;
    }
   $fecharesolFormateada = intval($dia) . " de " . $mesTexto . " de " . $anio;//itloz
 
    function obtenerNombreMes($mesNumero) { //usada ok
    $meses = array(
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
        7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    );
    return $meses[$mesNumero];
}

    
        function obtenerFechasFormateadas($fechainicio, $fechafin) {//utilizada
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

    // Uso de las fechas formateadas
    $fechasFormateadas = obtenerFechasFormateadas($fechainicio1, $fechafin1);//utilizado

    
    
    
    
    //otra cosa q meto: 




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
$fecha_letrasB = $dia.' del mes de '.$meses[date($mes)].' de '.$anio;


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
$documento_terc=$row['documento_tercero']; //utilizado
    }

 
// Crear una nueva instancia de PhpWord
$phpWord = new PhpWord();

// Definir dimensiones de una página tamaño carta en twips
$pageWidth = 12240; // 21.59 cm (8.5 pulgadas) en twips
$pageHeight = 15840; // 27.94 cm (11 pulgadas) en twips

// Agregar una sección con tamaño de página y márgenes personalizados
$section = $phpWord->addSection(array(
    'pageSizeW' => $pageWidth, 
    'pageSizeH' => $pageHeight,
    'marginLeft' => 1700,
    'marginRight' => 1700,  // 3 cm a la derecha (en twips)
    'marginTop' => 2268,    // 2 cm arriba (en twips)
    'marginBottom' => 1134,  // 2 cm abajo (en twips)
        "headerHeight" => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1),
    "footerHeight" => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(0)


));
$phpWord->getSettings()->setThemeFontLang(new Language(Language::ES_ES));
    $imgencabezado = 'img/encabezado_generico.png';
    $imgpie = 'img/PIEb.png';

$header = $section->addHeader();
$header->addImage($imgencabezado, array(
    //'width' => 560, // Incrementar el ancho en un 10%
    'height' => 90, // Incrementar el alto en un 10%
    'marginTop' => 5, // Subir la imagen para compensar el espacio de margen superior de 1 cm
   // 'marginRight' => 1700, // Mover la imagen 3 cm más a la derecha (3 cm * 567 twips/cm)
   // 'align' => 'right', // Alinear a la derecha
    'marginLeft' => round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(-0,4.4)),
    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,
        'posHorizontal' => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,

    'wrappingStyle' => 'infront',
    'positioning' => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE

));


// Configurar la localización a español
setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain.1252');


// Estilo de párrafo justificado con espaciado antes y después en 0
$justifiedStyle = array(
    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH,
    'spaceBefore' => 0,
    'spaceAfter' => 0,
    'size' => 11 // Tamaño de fuente 11
);
// Variante del estilo con pequeño espacio antes
$justifiedStyleSmallTop = array(
    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH,
    'spaceBefore' => 250, // Espaciado antes de 6 pt aprox
    'spaceAfter' => 0,
    'size' => 11
);
// Variante del estilo con pequeño espacio después
$justifiedStyleSmallBottom = array(
    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH,
    'spaceBefore' => 250,
    'spaceAfter' => 250, // Espaciado después de 6 pt aprox
    'size' => 11
);

// Función para formatear el número del documento
function formatDocumentNumber($number) {
    return number_format($number, 0, '', '.');
}
$documento_tercero = formatDocumentNumber($documento_terc); // Formato para el número del documento

// Obtener la fecha actual
$dia = date('j');
$mesNumero = date('n');
$anio = date('Y');

// Usar la función para obtener el nombre del mes en español
$nombreMes = obtenerNombreMes($mesNumero);

// Formatear la fecha completa
$fechaHoy = 'Popayán, ' . $dia . ' de ' . $nombreMes . ' de ' . $anio . '.';

// Añadir la fecha

$section->addText('4-55.6/', array('size' => 11), $justifiedStyle);

$section->addText($fechaHoy, array('size' => 11), $justifiedStyle);

// Añadir las líneas de texto sin espacios extra entre ellas, con espaciado 0
$section->addText('Profesional Especializada', array('size' => 11), $justifiedStyleSmallTop);
$section->addText('SANDRA LILIANA TRUJILLO ORTEGA', array('size' => 11), $justifiedStyle);
$section->addText('División de Gestión del Talento Humano', array('size' => 11), $justifiedStyle);
$section->addText('Universidad del Cauca', array('size' => 11), $justifiedStyle);


// Añadir el asunto y saludo
$section->addText('Asunto: Encargo de funciones '.$cargo.'', array('size' => 11), $justifiedStyleSmallTop);
$section->addText('Cordial saludo,', array('size' => 11), $justifiedStyleSmallBottom);
//$section->addText('', array('size' => 11), $justifiedStyleSmallTop);

    $paragraphStyle = array('alignment' => Jc::BOTH);
     $textRun = $section->addTextRun($paragraphStyle);

 
    
$textRun->addText($saludo_el_la. ' ',  array('size' => 11));
$textRun->addText($nombre_profesor, array('bold' => true,  'size' => 11));
$textRun->addText(', ',  array('size' => 11));
$textRun->addText($identificado . ' con la cédula de ciudadanía número ',  array('size' => 11));
$textRun->addText($documento_tercero, array('bold' => true,  'size' => 11));
$textRun->addText(', ' . $adscrito . ' al departamento de ' . $depto_nom_propio,  array('size' => 11)); 
 $textRun->addText(', participará  '.obtenerFechasFormateadas($fechainicio1, $fechafin1),  array('size' => 11));
 $textRun->addText(' en el evento: ',  array('size' => 11));

$textRun->addText($evento, array('italic' => true,  'size' => 11));



        if ($tipo_estudio == 'INT') {//utilizad
            
$textRun->addText(', organizado por ' . $organizado_por . ', ' . $endestinos . $destinos . ', en tanto le ha sido otorgada comisión académica al '.$ubicacion_estudio.' mediante resolución '.$tipores.' del '.$fecharesolFormateada.'.',  array('size' => 11));
        } else
        {
                     
$textRun->addText(', organizado por ' . $organizado_por . ', ' . $endestinos . $destinos . ', por lo cual ha solicitado comisión académica al '.$ubicacion_estudio,  array('size' => 11)); 
            
        }
$textRun = $section->addTextRun($paragraphStyle);

   
$textRun->addText($saludo_el_la,  array('size' => 11));
$textRun->addText(' '.$nombre_profesor, array('bold' => true,  'size' => 11));
$textRun->addText(', quien ostenta el cargo académico administrativo:  ',  array('size' => 11));
$textRun->addText($cargo . ', mediante ',  array('size' => 11));
$textRun->addText($oficio, array('size' => 11));
$textRun->addText(', informó que durante las fechas en que realizará su comisión académica, ' . $el_la_encargado.' ',  array('size' => 11)); 
 $textRun->addText($nombre_completo_encargado,  array('bold' => true, 'size' => 11)); 
 $textRun->addText(', '.$identif_encargo.' con cédula de ciudadanía No.'.$encargado_documento_tercero.' sería'.$el_encargado.'de las funciones de '.$cargo,  array('size' => 11)); 
$textRun = $section->addTextRun($paragraphStyle);

   
$textRun->addText('En ese orden de ideas, solicito comedidamente adelantar las actuaciones correspondientes para hacer efectivo el encargo de las funciones de: '.$cargo,  array('size' => 11));
$textRun->addText($al_profesor, array( 'size' => 11));

$textRun->addText($nombre_completo_encargado, array('bold' => true,  'size' => 11));
$textRun->addText(', '.obtenerFechasFormateadas($fechainicio1, $fechafin1).'.', array('size' => 11));
$textRun = $section->addTextRun($paragraphStyle);
$textRun->addText('Universitariamente, ',  array('size' => 11));
//$section->addTextBreak(1); // Inserta otro salto de línea
$textRun = $section->addTextRun($paragraphStyle);

// Añadir las líneas de texto sin espacios extra entre ellas, con espaciado 0
$section->addText(mb_strtoupper($firma_sol_encargo, 'UTF-8'), array('size' => 11), $justifiedStyle);
$section->addText($cargov, array('size' => 11), $justifiedStyle);
$paragraphStyle = array('alignment' => Jc::LEFT, 'spaceAfter' => 0);
$section->addTextBreak(); // Inserta otro salto de línea

$textRun = $section->addTextRun($paragraphStyle);
$textRun->addText('Anexo: un(1) folio', array('size' => 8, 'italic' => true)); // Aplicar cursiva al texto de "Autorizó"
        $elaboro_nombre_propio = ucwords(strtolower($elaboro));
$textRun->addTextBreak(); // Añadir un salto de línea mínimo

$textRun->addText('Proyectó: '.$elaboro_nombre_propio, array('size' => 8, 'italic' => true)); // Aplicar cursiva al texto de "Proyectó"
$textRun->addTextBreak(); // Añadir un salto de línea mínimo
$textRun->addText('Revisó: '.$reviso, array('size' => 8, 'italic' => true)); // Aplicar cursiva al texto de "Revisó"
$textRun->addTextBreak(); // Añadir un salto de línea mínimo



$filename = "Solicitud encargo - $cargo - $apellido1 $nombre1-$mm_aaaa.docx";
     
    $footer = $section->addFooter();
$footer->addImage($imgpie, array(
    'width' => 490, // Ajusta el ancho según sea necesario
    //'height' => 65, // Ajusta el alto según sea necesario
    'marginTop' => 86.7, // Ajusta el margen superior para mover la imagen 2 cm más abajo
    'marginRight' => 1000, // Ajusta el margen derecho según sea necesario
));






// Verificar la conexión
if (!$con) {
    die("Error en la conexión: " . mysqli_connect_error());
}

// Consulta de actualización
$sql = "UPDATE comision_academica 
        SET cargo_academico_admin = '$cargo', 
            cc_encargado = '$cedula', 
            oficio_encargo = '$oficio' 
        WHERE id = $id";

// Ejecutar la consulta
if (mysqli_query($con, $sql)) {
} else {
    echo "Error al actualizar el registro: " . mysqli_error($con);
}

// Cerrar la conexión
mysqli_close($con);



// Encabezados HTTP para la descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');

// Guardar el archivo en formato DOCX y enviarlo al navegador
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('php://output');

exit; // Terminar el script para evitar cualquier salida adicional

?>
