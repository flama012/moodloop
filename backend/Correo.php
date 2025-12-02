<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class Correo
{
    private $mailer;

    public function __construct() {

        require_once '../../../vendor/autoload.php';

        // Crear instancia de PHPMailer
        $this->mailer = new PHPMailer(true);

        // Configuración general
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'javi2006pj@gmail.com';
        $this->mailer->Password = 'xjzo zywf bpkn qift';
        $this->mailer->SMTPSecure = 'tls';
        $this->mailer->Port = 587;

        // Remitente por defecto
        $this->mailer->setFrom('moodloop@gmail.com', 'Mailer');

    }

    public function enviarCorreoRegistro($destinario, $token)
    {
        try {
            $resultado = false;
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinario);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = "Correo de verificación de Moodloop.";
            $this->mailer->Body = 'Pulsa <a href="http://aula2gs.edu/moodloop/backend/verificar.php?email='
                . $destinario . '&token=' . $token . '">aquí</a> para confirmar tu correo.';
            $this->mailer->send();
            $resultado = true;
            return $resultado;
        } catch (Exception $e) {
            return $resultado;
        }
    }

/*
    public function enviarCorreoVerificacion($correo, $token) {
        $asunto = "Verificación de correo";

        $mensaje = "<h2>Verifica tu cuenta</h2>
                  <p>Haz click en este enlace para verificar tu correo:</p>
                  http://aula2gs.edu/Tareas/daniell/tarea19/index.php?&email=" . $correo . "&token=" . $token;

        return $this->enviarCorreo($correo, $asunto, $mensaje);
    }
*/

}