<?php 
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Language;

// Crear una nueva instancia de PHPWord
$phpWord = new PhpWord();

// Configurar el idioma del documento a español
$phpWord->getSettings()->setThemeFontLang(new Language(Language::ES_ES));

// Añadir una nueva sección
$section = $phpWord->addSection();

// Añadir un "Text Run" para combinar diferentes formatos en un solo párrafo con justificación
$paragraphStyle = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH);
$textRun = $section->addTextRun($paragraphStyle);

// Añadir los diferentes segmentos de texto al TextRun
$textRun->addText('La ');
$textRun->addText('VICERRECTORA ACADÉMICA DE LA UNIVERSIDAD DEL CAUCA', array('bold' => true));
$textRun->addText(', en uso de competencias establecidas en el Acuerdo Superior 024 de 1993 - Estatuto del Profesor de la Universidad del Cauca, principalmente conforme a lo previsto en el artículo 73, modificado por el artículo quinto del Acuerdo Superior 031 de 2020, y');

// Guardar el archivo en formato DOCX
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('ejemplo.docx');

echo "Documento creado exitosamente.";
