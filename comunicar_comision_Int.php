<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluye los archivos necesarios de PHPMailer
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
$config = require 'config_email.php';

// Obtén el id de la URL
$id_comision = $_GET['id'] ?? null;

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
                comision_academica.fechaINI as inicio,
                comision_academica.vence as final,
                tercero.nombre_completo, 
                tercero.email,
				deparmanentos.depto_nom_propio, facultad.email_fac, comision_academica.link_resolucion, comision_academica.tramito

            FROM 
                comision_academica
            JOIN 
                tercero ON tercero.documento_tercero = comision_academica.documento 
                join  
                deparmanentos on deparmanentos.PK_DEPTO = comision_academica.com_acad_depto
				JOIN
                facultad on facultad.PK_FAC = deparmanentos.FK_FAC
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
        $tramito = $comision['tramito'];
$vence = $comision['final'];
$fechaini = $comision['inicio']; 
        $depto_nombre = $comision['depto_nom_propio'];
$email_facultad = $comision['email_fac'];
$link_resolucion = $comision['link_resolucion'];
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
        
            // Agregar copias (CC)
            $mail->addCC('orii@unicauca.edu.co');
            $mail->addCC('rhumanos@unicauca.edu.co');
            $mail->addCC('financiera@unicauca.edu.co');
            $mail->addCC('saludocu@unicauca.edu.co');
            $mail->addCC('viceacad@unicauca.edu.co');
            $mail->addCC('viceadm@unicauca.edu.co');
            $mail->addCC($email_facultad);
            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Comunicación Resolución VRA N°'.$no_resolucion.' de  '. $fecha_resolucion .' - '.$nombre_completo;
            $mail->Body    = "<p>Atento saludo</p>


<p>Por medio del presente, remito la RESOLUCIÓN VRA N° $no_resolucion de $fecha_resolucion, por la cual se autoriza una comisión académica en el territorio nacional al profesor(a)  $nombre_completo , en atención a las solicitud allegada a la Vicerrectoría Académica. Lo anterior, con el fin de comunicar a través de este medio electrónico el precitado acto administrativo</p>

<p>Acceder  a la resolución en el siguiente enlace: <a href='$link_resolucion'>Ver Resolución</a></p>


<p>Es preciso recalcar el deber especial del comisionado  la entrega en físico a esta Vicerrectoría, de la copia del informe de la comisión que le ha sido autorizada, una vez haya culminado esta, lo anterior en cumplimiento de la Resolución Rectoral 901 de 2023 </p>
<p>Universitariamente,</p>

<p><strong>AIDA PATRICIA GONZALEZ NIEVA</strong><br>Vicerrectora Académica</p>

<p>Elaboró: $tramito.</p>
";
            // Enviar el correo
            $mail->send();
            
            
            // Realizar el INSERT después de enviar el correo
        $fecha_actual = date('Y-m-d H:i:s');
        $update_sql = "UPDATE comision_academica SET notificado = 1, fecha_notificacion = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $fecha_actual, $id_comision);
        $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<br>notificado correctamente.";
            } else {
                echo "<br>Error al acutalizar comunicacion.";
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
echo '<br><a href="' . $_SERVER['HTTP_REFERER'] . '">Volver a la página anterior</a>';
?>