<?php
require 'vendor/autoload.php'; // Asegúrate de que esta línea apunta a la ruta correcta

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;
$config = require 'config_email.php';
//var_dump($config);
// Carga las variables de entorno desde el archivo .env
$dotenv = Dotenv::createImmutable(__DIR__); // Asegúrate de que __DIR__ apunta al directorio correcto
$dotenv->load();

// Incluye los archivos necesarios de PHPMailer
/*require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
*/
if (isset($_POST['comisiones']) && !empty($_POST['comisiones'])) {
    $comisiones_ids = $_POST['comisiones'];

    // Realiza el procesamiento, por ejemplo, actualizar el estado de las comisiones
    foreach ($comisiones_ids as $id_comision) {
       if ($id_comision) {
    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "comisiones_academicas");

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta SQL para obtener los datos
    $sql = "SELECT 
                comision_academica.id, 
                comision_academica.No_resolucion, 
                comision_academica.fecha_resolucion, 
                comision_academica.documento, 
                comision_academica.evento,
                comision_academica.organizado_por,
                 comision_academica.fechaINI as inicio,
                comision_academica.vence as final,
                tercero.nombre_completo, 
                tercero.email
            FROM 
                comision_academica
            JOIN 
                tercero ON tercero.documento_tercero = comision_academica.documento 
            WHERE 
                comision_academica.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_comision); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $comision = $result->fetch_assoc();
        $nombre_completo = $comision['nombre_completo'];
        $email_destinatario = $comision['email'];
        $no_resolucion = $comision['No_resolucion'];
        $fecha_resolucion = $comision['fecha_resolucion'];
        $evento = $comision['evento'];
        $organizado_por = $comision['organizado_por'];
$vence = $comision['final'];
$fechaini = $comision['inicio']; 
        // Crear instancia de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->SMTPDebug = 0; // Cambia a 2 para ver los mensajes de depuración
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
           $mail->Username   =  $config['smtp_username'];
            $mail->Password   =  $config['smtp_password'];
/*
             $mail->Username = getenv('SMTP_USERNAME');
$mail->Password = getenv('SMTP_PASSWORD');
  */         
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Opciones SSL para mayor compatibilidad
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->CharSet = 'UTF-8';

// Configurar destinatarios
$mail->setFrom($config['smtp_username'], 'Vicerrectoría Académica');
$mail->addAddress($email_destinatario, $nombre_completo);

// Configurar la codificación para manejar correctamente caracteres especiales

// Contenido del correo
$mail->isHTML(true);
$mail->Subject = 'Solicitud de informe de Comisión Res. ' . $no_resolucion;
           $mail->Body    = "<p>Profesor(a) <strong>$nombre_completo</strong>,</p>  
<p>Cordial saludo.</p>

<p>En atención al cumplimiento de la Resolución Rectoral 901 de 2023, mediante la cual se reglamentan los requisitos para la autorización de las comisiones académicas al interior y al exterior del país, en ese mismo sentido, el Artículo segundo de las Resoluciones VRA de comisiones académicas al interior del país, establece que:</p>
<p><strong>\"ARTÍCULO SEGUNDO. Como deber especial el profesor (profesora) comisionado rendirá ante la Decanatura de su Facultad con copia a esta Vicerrectoría, un informe escrito dentro de los diez (10) días siguientes al vencimiento de la comisión. En el informe se presentarán los aspectos pertinentes a los objetivos específicos de la unidad académica y generales de la institución, de lo cual se dejará constancia en el Acta de Reunión de Departamento. PARÁGRAFO: La Administración Universitaria se abstendrá de tramitar nueva comisión al docente que no satisfaga los requerimientos estipulados en la presente Resolución, por cuyo cumplimiento velará el Decano de la respectiva Facultad.\"</strong></p>

<p>Por lo anterior, se informa que se encuentra pendiente el informe de comisión según resolución No. <strong>$no_resolucion</strong>, referente a su participación en el evento <strong>$evento</strong> organizado por <strong>$organizado_por</strong>, con fecha de inicio: <strong>$fechaini</strong> y fecha final: <strong>$vence</strong>, y se solicita respetuosamente remitir la copia en físico del informe. De haber sido entregado, se solicita suministre copia del recibido del informe, por parte de la oficina de Vicerrectoría Académica </p>


<p>Agradezco su atención y colaboración.</p>

<p>Universitariamente,</p>

<p><strong>AIDA PATRICIA GONZÁLEZ NIEVA</strong><br>Vicerrectora Académica</p>";

            // Enviar el correo
            $mail->send();
            
            
            // Realizar el INSERT después de enviar el correo
            $fecha_actual = date('Y-m-d H:i:s');
            $insert_sql = "INSERT INTO notificar_informe_pend (fk_notificar_id_comision, fecha_notificar) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("is", $id_comision, $fecha_actual);
            $insert_stmt->execute();

            if ($insert_stmt->affected_rows > 0) {
                echo "<br>Registro insertado correctamente en la tabla notificar_informe_pend.";
            } else {
                echo "<br>Error al insertar el registro en la tabla notificar_informe_pend.";
            }
            echo 'Correo enviado correctamente a ' . $nombre_completo;
            
        } catch (Exception $e) {
            echo "No se pudo enviar el correo: {$mail->ErrorInfo}";
              
        }
    } else {
        echo "No se encontraron datos para el ID proporcionado.";
    }

    // Cerrar la conexión
    $conn->close();
} else {
    echo "ID de comisión no proporcionado.";
}
    }

    echo "Comisiones procesadas exitosamente.";
} else {
    echo "No se seleccionaron comisiones.";
}
echo '<br><a href="' . $_SERVER['HTTP_REFERER'] . '">Volver a la página anterior</a>';

?>
