<?php
//require('force_justify.php');
require('fpdf186/fpdf.php');
$id_res=$_GET['id'];
//require('force_justify.php');
class PDF extends FPDF
{

    // Cabecera de página

    function Header()
{
    // Arial bold 15
    global $res;
    global $fechres;
    $this->SetFont('Arial','I',8);
    // Movernos a la derecha
    $this->Cell(-1);
   //     $this->Image('img/certificado2020.png',1,8,210);
    // Título
     if ($this->PageNo() != 1)   
    $this->Cell(20,30,utf8_decode('Continuación de Resolución '.$res.' de '.$fechres),0,0,'J');
        
    // Salto de línea
    $this->Ln(20);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página 
    $this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'C');
    //$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}
require 'cn.php';




//$pdf = new PDF('P', 'mm', 'Legal');
$pdf = new PDF('P','mm',array(216,330));
$pdf->AliasNbPages();
$pdf->SetMargins(30, 30 , 30);
$pdf->SetAutoPageBreak(true,40); 
$pdf->AddPage();



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
    vicerrector.vice_resol_encago AS res_encargo_vice,
    vicerrector.vice_nom_propio AS nom_propio_vice,
    vicerrector.tipo_vice AS tipo_vice,

    users.Id AS id_user,
    users.DocUsuario,
    users.Name,
    users.Email AS email_usuario,
    -- Aquí agregamos la lógica condicional
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
LEFT JOIN users ON users.Name = comision_academica.tramito
LEFT JOIN (
    SELECT 
        id_comision,
        COUNT(DISTINCT pais) AS num_paises,
        COUNT(id_destino) AS num_destinos
    FROM destino
    GROUP BY id_comision order by destino.pais asc
) AS subquery ON subquery.id_comision = comision_academica.id
WHERE comision_academica.id = '$id_res'
GROUP BY comision_academica.id;";



//$resultado = $mysqli  -> query($consulta);
$resultadores = $con->query($consultares);

      $row = $resultadores -> fetch_assoc();
   $res = $row['No_resolucion'];
   global $vice;
    $vice = $row['nombre_rector'];
 global $elaboro;
    $elaboro = $row['Name'];
 global $reviso;
    $reviso = $row['nombre_vice'];
    global $sexo;
    $sexo = $row['sexo_vice'];
switch ($row['sexo_vice']) {
    case "F":
        $cargov ="Vicerrectora Académica";
        break;
    case "M":
        $cargov ="Vicerrector Académico";;
        break;
   
}

$tipo_vice = $row['tipo_vice'];
$rector = $row['nombre_rector'];
$tipo_rector = $row['tipo_rector'];
$autorizo =$row['nom_propio_vice']; //aplica para exterio firma rector  autorz vice
    
    
   /* $pdf ->Cell(25,5,$row['numero_resolucion'],1,0,'L',0);*/
   
  $fechres = date("d/m/Y", strtotime($row['fecha_resolucion'])); $row['fecha_resolucion'];   
$fecha = $row['fecha_resolucion'];
$fechaComoEntero = strtotime($fecha);
$dia = date("d", $fechaComoEntero);
$mes = date("m", $fechaComoEntero);
$anio = date("Y", $fechaComoEntero);
setlocale(LC_TIME, 'es_ES.UTF-8');  // Configurar el locale a español

$fecha_aval = $row['fecha_aval'];  // Supongamos que esta fecha es '2024-02-03'

$fecha_aval = $row['fecha_aval'];
$fechaComoEnteroaval = strtotime($fecha_aval);
$diaaval = date("d", $fechaComoEnteroaval);
$mesaval = date("m", $fechaComoEnteroaval);
$anioaval = date("Y", $fechaComoEnteroaval);

$facultad_min = $row['nombre_fac_min'];
$depto_nom_propio = $row['depto_nom_propio'];

$documento_tercero = $row['documento_tercero'];

$pdf->SetFont('Arial','',10);

$pdf ->Multicell(160 ,5,'2.1-4.16',0,'L');
 $pdf->Ln(3);
$pdf->SetFont('Arial','B',11);
$resol = $row['No_resolucion'];
//$nombre_profesor = $row['nombre_completo'];
$al_profe_a = $row['sexo_tercero'];
$tipo_estudio = $row['tipo_estudio'];
if ($al_profe_a == 'M') {
    $saludo = "al profesor ";
    $saludode = "del profesor ";
    $adscrito = " adscrito ";
    $saludo_el_la = "el profesor";
    $identificado = "identificado";    
    $alinteresado = "al interesado";
$comisionado = "el profesor comisionado";


} elseif ($al_profe_a == 'F') {
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
     $coneltrabajo = "con el trabajo: ".$trabajo;
}
$evento = $row['evento'];
$organizado_por = $row['organizado_por'];
$destinos = $row['destinos'];
$justificacion = $row['justificacion'];
$ellasciudades =  $row['num_destinos'];
if ($ellasciudades == 1) {
    $endestinos = "la ciudad de ";
} else {
     $endestinos = "en las ciudades de : ";

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
$apellido1 = $row['apellido1'];
$apellido2 = $row['apellido2'];
$nombre1 = $row['nombre1'];
$nombre2 = $row['nombre2'];

// Para los nombres en el orden: nombre1 nombre2 apellido1 apellido2
$nombre_profesor = "$nombre1 $nombre2 $apellido1 $apellido2";
$apellido1p = ucfirst(strtolower($row['apellido1']));
$apellido2p = ucfirst(strtolower($row['apellido2']));
$nombre1p = ucfirst(strtolower($row['nombre1']));
$nombre2p = ucfirst(strtolower($row['nombre2']));

// Para los nombres en el orden: nombre1 nombre2 apellido1 apellido2
$nombresOrdenadosp = "$nombre1p $nombre2p $apellido1p $apellido2p";
$reviso = $row['reviso'];
$resol_encargo_rector= $row['resol_encargo_rector'];


$pdf ->Multicell(160 ,5,utf8_decode('RESOLUCIÓN RECTORAL No.'.$resol.' DE '.$anio),0,'C');


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
$fecha_letras_aval = $diaaval.' de '.$meses[date($mesaval)].' de '.$anioaval;

// trabajr con infofechas inicio fin


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

function obtenerNombreMes($mesNumero) {
    $meses = array(
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
        7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    );
    return $meses[$mesNumero];
}


//funcion prespuestos. 

function generarTextoErogaciones($viaticos, $tiquetes, $inscripcion, $cargo_a, $cdp, $valor, $cargo_admin) {
    $texto = ' no genera erogaciones a la Universidad del Cauca, en viáticos, inscripción o tiquetes.';

    if ($viaticos == 1 || $tiquetes == 1 || $inscripcion == 1) {
        $texto = ' genera erogaciones a la Universidad del Cauca, en ';

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

        if (!empty($cargo_admin) && in_array($cargo_admin, ['JEFE', 'DIRECTOR', 'DECANO'])) {
            $texto .= ", de acuerdo al $cdp por valor de $valor";
        }
    }

    // Si inscripción es 1 y solo se han seleccionado viáticos o tiquetes
    if ($inscripcion == 1 && (($viaticos == 1 && $tiquetes == 0) || ($viaticos == 0 && $tiquetes == 1))) {
        // Reemplazar "y" por "e"
        $texto = str_replace(' y ', ' e ', $texto);
    }

    return $texto;
}

$texto_erogaciones = generarTextoErogaciones($viaticos, $tiquetes, $inscripcion, $cargo_a, $cdp, $valor, $cargo_admin);

$pdf ->Multicell(160 ,5,utf8_decode($dia).' de '.utf8_decode($meses[date($mes)])  ,0,'C');
 $pdf->Ln(3);
$pdf->SetFont('Arial','',10);

if (in_array($cargo_admin, ['JEFE', 'DECANO', 'DIRECTOR'])) {
    $pdf->Multicell(160, 5, utf8_decode('Por la cual se concede comisión académica al exterior a un Profesor con Cargo Académico Administrativo.'), 0, 'J');
} else {
    $pdf->Multicell(160, 5, utf8_decode('Por la cual se concede comisión académica al exterior ' . $saludo . ' ' . $nombre_profesor), 0, 'J');
}
 $pdf->Ln(3);
if ($tipo_rector == 'Rector') {
    $pdf->Multicell(160, 5, utf8_decode('EL RECTOR DE LA UNIVERSIDAD DEL CAUCA, en uso de sus competencias previstas mediante el Acuerdo 105 de 1993'), 0, 'J');
} elseif ($tipo_rector == 'Rector Encargado') {
    $pdf->Multicell(160, 5, utf8_decode('EL RECTOR ENCARGADO DE LA UNIVERSIDAD DEL CAUCA, en uso de sus competencias previstas mediante el Acuerdo 105 de 1993, y conforme a la ' . $resol_encargo_rector . '.'), 0, 'J');
} elseif ($tipo_rector == 'Rector delegatario') {
    $pdf->Multicell(160, 5, utf8_decode('EL RECTOR DELEGATARIO DE LA UNIVERSIDAD DEL CAUCA, en uso de sus competencias previstas mediante el Acuerdo 105 de 1993, y conforme a la ' . $resol_encargo_rector . '.'), 0, 'J');
}
 $pdf->Ln(3);

$pdf->SetFont('Arial','B',11);
$pdf ->Multicell(160 ,5,utf8_decode('C O N S I D E R A N D O   Q U E:'),0,'C');
$pdf->SetFont('Arial','',10);
 $pdf->Ln(3);

$pdf ->Multicell(160 ,5,utf8_decode('El Acuerdo 024 de 1993 reglamentario del Estatuto del Profesor, establece en su Capítulo XIV las Situaciones Administrativas del empleado público vinculado como profesor, dentro de las cuales, se encuentra la comisión académica, entendida como la asistencia a foros, viajes de estudio, seminarios, congresos, encuentros, cursos o similares en los cuales el profesor representa a la Universidad del Cauca.'),0,'J');
 $pdf->Ln(3);
 $pdf ->Multicell(160 ,5,utf8_decode('El Consejo Superior máximo órgano de dirección y gobierno de la Universidad del Cauca, mediante comunicado 2.1-1.39/119 del 20 de diciembre de 2022, recomendó a la Dirección Universitaria prever controles en la autorización de las comisiones, con miras a verificar y articular los objetos de las comisiones académicas con los objetivos misionales y estratégicos, procurando además, por la rigurosidad en la aprobación de apoyos con efecto presupuestal.'),0,'J');
 $pdf->Ln(3);

  $pdf ->Multicell(160 ,5,utf8_decode('La Rectoría y Vicerrectoría Académica con el propósito de promover los principios universitarios, los objetivos del Estatuto del Profesor en cuanto al mejoramiento del nivel educativo y, con la finalidad de salvaguardar los principios de la función administrativa, atinentes a la celeridad, economía, eficacia,  eficiencia y responsabilidad, verifica la articulación de las solicitudes de comisión académica de los profesores, con el Proyecto Educativo Institucional (PEI) y con el Plan Desarrollo Institucional (PDI) 2023-2027 «Por una Universidad de Excelencia y Solidaria».'),0,'J');
 $pdf->Ln(3); 

   $pdf ->Multicell(160 ,5,utf8_decode('La autorización y concesión de comisiones académicas al exterior es competencia del Rector, con aval previo del Consejo de Facultad y la Vicerrectoría Académica, conforme a la delegación del Consejo Académico mediante Resolución Académica 025 de 2023.'),0,'J');
 $pdf->Ln(3);  



$pdf->Multicell(160, 5, utf8_decode('En sesión del Consejo de la ' . $facultad_min . ', del día ' . $fecha_letras_aval . ', se avaló la Comisión Académica al exterior, ' . $saludo . ' ' . $nombre_profesor . ', ' . $identificado . ' con la cédula de ciudadanía número ' . $documento_tercero . ', ' . $adscrito . ' al departamento de ' . $depto_nom_propio . '.'), 0, 'J');


 $pdf->Ln(3);
$pdf ->Multicell(160 ,5,utf8_decode('De conformidad con los documentos remitidos por el presidente del Consejo de Facultad, que soportan la solicitud de la comisión, resulta pertinente la asistencia '.$saludode.' '.$nombre_profesor.', con el fin de participar '.$coneltrabajo.', en '.$evento.'), organizado por '.$organizado_por.', '.$endestinos.$destinos.'; lo que permitirá '.$justificacion.'.'),0,'J');
 $pdf->Ln(3);

$pdf ->Multicell(160 ,5,utf8_decode('En cumplimiento del deber de generar y socializar la ciencia, la cultura en la docencia, la investigación y  la proyección social como fines misionales universitarios, se justifica la autorización de una comisión académica, '.obtenerFechasFormateadas($fechainicio1, $fechafin1).', de conformidad con lo establecido en el artículo 117 del Acuerdo Superior 024 de 1993, para que '.$saludo_el_la.' '. $nombre_profesor.' participe en la misión académica mencionada.'),0,'J');

$pdf->Ln();
$pdf ->Multicell(160 ,5,utf8_decode('La comisión solicitada'.$texto_erogaciones),0,'J');
 $pdf->Ln(3);
$pdf ->Multicell(160 ,5,utf8_decode('Por lo expuesto,'),0,'J');

$pdf->SetFont('Arial','B',11);

$pdf ->Multicell(160 ,5,utf8_decode('RESUELVE:'),0,'C');
 $pdf->Ln(3);
$pdf->SetFont('Arial','',10);
 $pdf->Ln(3);

$pdf->Multicell(160, 5, utf8_decode('ARTÍCULO PRIMERO: Conceder comisión académica al exterior, ' . obtenerFechasFormateadas($fechainicio1, $fechafin1) . ', ' . $saludo . ' ' . $nombre_profesor . ', ' . $identificado . ' con la cédula de ciudadanía número ' . $documento_tercero . ', ' . $adscrito . ' al departamento de ' . $depto_nom_propio . ', de la ' . $facultad_min . ', con el fin de participar ' . $coneltrabajo . ', en ' . $evento . ', organizado por ' . $organizado_por . ', ' . $endestinos . $destinos . '.'), 0, 'J');
 $pdf->Ln(3);


$pdf ->Multicell(160 ,5,utf8_decode('ARTÍCULO SEGUNDO: La comisión autorizada en el artículo anterior'.$texto_erogaciones.'.'),0,'J');

 $pdf->Ln(3);

$pdf ->Multicell(160 ,5,utf8_decode('ARTÍCULO TERCERO: Como deber especial, '.$comisionado.' rendirá ante la Decanatura de su Facultad con copia a la Vicerrectoría Académica, un informe escrito dentro de los diez (10) días siguientes al vencimiento de la comisión.  En el informe se presentarán los aspectos pertinentes a los objetivos específicos de la unidad académica y generales de la institución, de lo cual se dejará constancia en el Acta de Reunión de Departamento.'),0,'J');
$pdf->Ln(3);

$pdf ->Multicell(160 ,5,utf8_decode('PARÁGRAFO: La Administración Universitaria se abstendrá de tramitar nueva comisión al profesor que no satisfaga los requerimientos estipulados en la presente resolución, por cuyo cumplimiento velará el Decano de la respectiva Facultad'),0,'J');
$pdf->Ln(3);

$pdf ->Multicell(160 ,5,utf8_decode('ARTÍCULO CUARTO: Enviar copia del presente acto administrativo a la Vicerrectoría Académica (viceacad@unicauca.edu.co), Oficina de Relaciones Interinstitucionales e internacionales (relacionesinter@unicauca.edu.co), ' . $facultad_min . ' (' . $email_fac . '), División de Gestión del Talento Humano (rhumanos@unicauca.edu.co), División de Gestión Financiera (financiera@unicauca.edu.co) y '.$alinteresado.';  '.$nombresOrdenadosp.'('.$email_profesor.').'),0,'J');

$pdf->Ln();
$pdf ->Multicell(160 ,5,utf8_decode('Se expide en Popayán,  '),0,'J');
$pdf->SetFont('Arial','B',11);
 $pdf->Ln(3);
 $pdf->Ln(3);

$pdf ->Multicell(160 ,5,utf8_decode('COMUNÍQUESE, NOTIFÍQUESE Y CÚMPLASE:'),0,'C');
 $pdf->Ln(3);
 $pdf->Ln(3);
 $pdf->Ln(3); $pdf->Ln(3); $pdf->Ln(3);
 $pdf->Ln(3);  $pdf->Ln(3); 
$pdf ->Multicell(160 ,5,utf8_decode($rector),0,'C');
$pdf ->Multicell(160 ,5,utf8_decode($tipo_rector),0,'C');
 $pdf->Ln(3); 
 $pdf->Ln(3); 
 $pdf->Ln(3); 
$pdf->SetFont('Arial','',8);
if ($tipo_vice == 'Encargado') {
    $pdf->Cell(30, 4, utf8_decode('Autorizó: ' . $autorizo . ' (E)'), 0, 1, 'L', 0);
} elseif ($tipo_vice == 'Delegatario') {
    $pdf->Cell(30, 4, utf8_decode('Autorizó: ' . $autorizo . ' (D)'), 0, 1, 'L', 0);
} else {
    $pdf->Cell(30, 4, utf8_decode('Autorizó: ' . $autorizo), 0, 1, 'L', 0);
}$pdf ->Cell(30,4,utf8_decode('Revisó: '.$reviso),0,1,'L',0);
 $pdf ->Cell(30,4,utf8_decode('elaboró: '.$elaboro),0,1,'L',0);

$pdf->Output();
?>