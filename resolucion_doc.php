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

// Añadir una nueva sección
$section = $phpWord->addSection();

// Consulta SQL
$id_res = 229; // Asegúrate de definir $id_res con el valor correcto
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
    GROUP BY id_comision
) AS subquery ON subquery.id_comision = comision_academica.id
WHERE comision_academica.id = '$id_res'
GROUP BY comision_academica.id;";

$resultadores = $con->query($consultares);

if ($resultadores->num_rows > 0) {
    $row = $resultadores->fetch_assoc();
    $res = $row['No_resolucion'];
    $tipo_vice= $row['tipo_vice'];
    $res_encargo_vice = $row['res_encargo_vice'];

    // Añadir el título centrado
    $section->addText('RESOLUCIÓN VRA ' . $res, array('bold' => true, 'size' => 12), array('alignment' => Jc::CENTER));

    // Construir el párrafo según el tipo de rector
    $paragraphStyle = array('alignment' => Jc::BOTH);
    $textRun = $section->addTextRun($paragraphStyle);

    if ($tipo_vice == 'propiedad') {
        $textRun->addText('La ');
        $textRun->addText('VICERRECTORA ACADÉMICA DE LA UNIVERSIDAD DEL CAUCA', array('bold' => true));
        $textRun->addText(', en uso de competencias establecidas en el Acuerdo Superior 024 de 1993 - Estatuto del Profesor de la Universidad del Cauca, principalmente conforme a lo previsto en el artículo 73, modificado por el artículo quinto del Acuerdo Superior 031 de 2020, y');
    } elseif ($tipo_vice == 'Encargado') {
        $textRun->addText('El ');
        $textRun->addText('VICERRECTOR ACADÉMICO ENCARGADO DE LA UNIVERSIDAD DEL CAUCA', array('bold' => true));
        $textRun->addText(', en uso de competencias establecidas en el Acuerdo Superior 024 de 1993 - Estatuto del Profesor de la Universidad del Cauca, principalmente conforme a lo previsto en el artículo 73, modificado por el artículo quinto del Acuerdo Superior 031 de 2020, y, y conforme a la ' . $resol_encargo_rector . '.');
    } elseif ($$tipo_vice == 'delegatario') {
        $textRun->addText('El RECTOR DELEGATARIO DE LA UNIVERSIDAD DEL CAUCA', array('bold' => true));
        $textRun->addText(', en uso de sus competencias previstas mediante el Acuerdo 105 de 1993, y conforme a la ' . $resol_encargo_rector . '.');
    }
} else {
    // Añadir mensaje de error si no se encontraron resultados
    $section->addText('No se encontraron resultados para la consulta.', array('alignment' => Jc::CENTER));
}

// Guardar el archivo en formato DOCX
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('resolucion_doc.docx');

echo "Documento creado exitosamente.";
?>
