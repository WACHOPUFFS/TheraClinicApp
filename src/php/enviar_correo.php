<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

require_once 'cors.php';

function enviarCorreo($correo, $token, $tipo)
{
    // Configura el objeto PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configura el servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'mail.siinad.mx';  // Reemplaza con tu servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'contactoaservicios@siinad.mx';     // Reemplaza con tu correo electrónico
        $mail->Password = 'Siinad_servicio'; // Reemplaza con tu contraseña
        $mail->SMTPSecure = 'ssl';          // Puedes cambiar a 'ssl' si es necesario
        $mail->Port = 465;                  // Puedes cambiar el puerto según la configuración de tu servidor

        $mail->setFrom('contactoaservicios@siinad.mx', 'Sinergia de integración administrativa');  // Reemplaza con tu correo y nombre
        $mail->addAddress($correo);  // Utiliza el correo del destinatario pasado como parámetro

        // Configura el correo
        $mail->CharSet = 'UTF-8';  // Establecer la codificación de caracteres
        $mail->Encoding = 'base64'; // Opcional: puedes probar 'quoted-printable' si 'base64' no funciona bien
        $mail->isHTML(true);

        // Configura el asunto y el cuerpo del correo según el tipo de correo
        switch ($tipo) {
            case 'confirmacion':
                $mail->Subject = 'Confirmación de registro';
                $enlaceConfirmacion = "https://siinad.mx/php/confirmar.php?token=$token";
                $body = "¡Gracias por registrarte en nuestra aplicación! Por favor, haz clic en el siguiente enlace para confirmar tu cuenta: <a href='$enlaceConfirmacion'>Confirmar cuenta</a>.";
                break;
            case 'advertencia':
                $mail->Subject = 'Recordatorio: Confirma tu cuenta';
                $body = "¡Hola! Parece que aún no has confirmado tu cuenta en (appName). Por favor, haz clic en el enlace de confirmación que se te envió anteriormente.";
                break;
            case 'eliminacion':
                $mail->Subject = 'Eliminación de cuenta';
                $body = "Lamentablemente, tu cuenta no ha sido confirmada a traves de (appName) y ha sido eliminada de nuestro sistema. Si deseas volver a registrarte, puedes hacerlo en cualquier momento a través. https://ctrlobra.sinsetec.com.mx/register-admin-s";
                break;
            case 'eliminacion_peticion':
                $mail->Subject = 'Eliminación de petición de socio comercial';
                // Utiliza $token como el motivo en el cuerpo del correo
                $body = '<html>
                                <body>
                                    <p>Estimado/a usuario,</p>
                                    <p>Lamentamos informarte que la petición de socio comercial que enviaste ha sido rechazada por el siguiente motivo:</p>
                                    <p><strong>' . $token . '</strong></p>
                                    <p>Por favor, si tienes alguna duda sobre el aplicativo, no dudes en contactarnos. Puedes enviarnos un correo a <a href="contactoaservicios@siinad.mx">contactoaservicios@siinad.mx</a> con los siguientes datos:</p>
                                    <ul>
                                        <li>RFC y Razón Social del beneficiario</li>
                                        <li>RFC del Socio Comercial</li>
                                        <li>Imagen con la descripción del problema</li>
                                        <li>Adjuntar los archivos que presentan el problema (si aplica)</li>
                                    </ul>
                                    <p>Gracias por tu comprensión.</p>
                                    <p>Atentamente,<br>
                                    Sinergia de integración administrativa</p>
                                </body>
                            </html>';
                break;

            case 'avisoSolicitud':
                $mail->Subject = '¡Solicitud de membresía Premium recibida!';
                $body = "¡Hola!<br><br>Gracias por solicitar formar parte de nuestra página y nuestro sistema (AppName).<br><br>Si tu solicitud fue enviada por ti, por favor, espera nuestra respuesta hasta que se confirme.<br><br>Nos encontramos procesando tu solicitud. Si no has realizado esta solicitud, por favor, ponte en contacto con un administrador de la página de inmediato.<br><br>¡Gracias!";
                break;

            case 'AvisoRegistro':
                $mail->Subject = 'Nuevo registro de usuario';
                $body = $token;
                break;
            case 'SolicitudAceptada':
                $mail->Subject = '¡Bienvenido a nuestro servicio (AppName)!';
                $body = "<p>Estimado usuario,</p>
                         <p>Nos complace informarle que su solicitud para unirse a nuestro servicio Premium ha sido aceptada. A partir de ahora, puede disfrutar de todos los beneficios exclusivos que ofrecemos por el periodo solicitado.</p>
                         <p>Estos beneficios incluyen:</p>
                         <ul>
                             <li>Acceso a contenido exclusivo</li>
                             <li>Soporte prioritario</li>
                             <li>Ofertas y descuentos especiales</li>
                             <li>Y mucho más...</li>
                         </ul>
                         <p>Estamos encantados y esperamos que disfrute de su experiencia Premium. Si tiene alguna pregunta o necesita asistencia, no dude en ponerse en contacto con nuestro equipo de soporte.</p>
                         <p>Correo electronico de contacto: contactoaservicios@siinad.mx</p>
                         <p>Horario de atención: Lunes a Viernes, de 8 am a 6 pm.</p>
                         <p>Gracias por confiar en nosotros.</p>
                         <p>Saludos cordiales,<br>El equipo de Sinergia de integración administrativa</p>";
                break;
            default:
                throw new Exception('Tipo de correo no válido');
        }


        $avisoPrivacidad = '<i>La información incluida en este correo electrónico está clasificada como confidencial y potencialmente privilegiada desde el punto de vista legal. Su acceso está restringido exclusivamente al destinatario designado. Cualquier acceso no autorizado a este correo electrónico está estrictamente prohibido. Si ha recibido este mensaje por error, le solicitamos que lo notifique al remitente utilizando el asunto "Recibido por error", devolviendo el correo electrónico al remitente original, y posteriormente elimine el mensaje y destruya todas las copias del mismo. Si usted no es el destinatario previsto, queda terminantemente prohibido divulgar, copiar, distribuir o tomar cualquier medida basada en el contenido de este mensaje, ya que dichas acciones podrían ser ilegales. Cualquier opinión o asesoramiento contenido en este correo electrónico está sujeto a los términos y condiciones expresados en los acuerdos contractuales vigentes con nuestros clientes. Es importante destacar que las opiniones, conclusiones y demás información contenida en este correo electrónico, así como cualquier archivo adjunto, no cuentan con el respaldo ni la validación de nuestras operaciones oficiales. Nos comprometemos a garantizar la seguridad y la integridad de nuestras comunicaciones electrónicas; sin embargo, no podemos asegurar la total protección contra interceptaciones, corrupciones, modificaciones, pérdidas, retrasos, incompletitudes o la presencia de virus informáticos. Este mensaje ha sido enviado en nombre de la firma local de SIINAD (Sinergia de Integración Administrativa) que le presta servicios. SIINAD es una entidad legalmente independiente y cada una de sus filiales se considera como una entidad separada. Cada una de nuestras filiales es responsable de sus propias acciones, obligaciones y responsabilidades. Para obtener más información sobre nuestra estructura organizativa, le invitamos a visitar nuestra página web en <a href="https://siinad.mx">https://siinad.mx</a>.</i>';

        $body .= "<br><br>" . $avisoPrivacidad;
        $mail->Body = $body;
        // Envía el correo
        $mail->send();
    } catch (Exception $e) {
        // Manejo de errores si falla el envío del correo
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}
?>