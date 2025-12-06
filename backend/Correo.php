<?php

// Importamos las clases necesarias de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Correo {

    // Aquí guardaremos el objeto PHPMailer
    private $mailer;

    public function __construct() {

        // Cargamos PHPMailer desde vendor
        require_once '../../../vendor/autoload.php';

        // Creamos el objeto PHPMailer
        $this->mailer = new PHPMailer(true);

        // Activamos el uso de SMTP (servidor de correo)
        $this->mailer->isSMTP();

        // Dirección del servidor SMTP de Gmail
        $this->mailer->Host = 'smtp.gmail.com';

        // Indicamos que el servidor requiere autenticación
        $this->mailer->SMTPAuth = true;

        // Usuario del correo que enviará los mensajes
        $this->mailer->Username = 'javi2006pj@gmail.com';

        // Contraseña del correo (debería estar oculta en producción)
        $this->mailer->Password = 'xjzo zywf bpkn qift';

        // Tipo de seguridad
        $this->mailer->SMTPSecure = 'tls';

        // Puerto del servidor SMTP
        $this->mailer->Port = 587;

        // Dirección desde la que se enviarán los correos
        $this->mailer->setFrom('moodloop@gmail.com', 'Mailer');
    }

    // Envía el correo de verificación al registrarse
    public function enviarCorreoRegistro($destinario, $token)
    {
        try {
            // Valor por defecto: no enviado
            $resultado = false;

            // Limpiamos direcciones anteriores
            $this->mailer->clearAddresses();

            // Añadimos el correo del destinatario
            $this->mailer->addAddress($destinario);

            // Indicamos que el mensaje será HTML
            $this->mailer->isHTML(true);

            // Asunto del correo
            $this->mailer->Subject = "Verificar tu cuenta de Moodloop.";

            // Cuerpo del mensaje con el enlace de verificación
            $this->mailer->Body = 'Pulsa <a href="http://aula2gs.edu/moodloop/backend/verificar.php?email='
                . $destinario . '&token=' . $token . '">aquí</a> para confirmar tu correo.';

            // Enviamos el correo
            $this->mailer->send();

            // Si llega aquí, el envío fue correcto
            $resultado = true;

            return $resultado;

        } catch (Exception $e) {

            // Si ocurre un error, devolvemos false
            return $resultado;
        }
    }
}
