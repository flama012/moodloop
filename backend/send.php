<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['enviarCorreo'])) {
    //verificar si existe ya ese correo en la tabla usuarios
    require_once 'UsuarioBBDD.php';
    $usuBD = new UsuarioBBDD();
    if ($usuBD->existeEmail($_POST['email'])) {
        session_start();
        $_SESSION["error"] = "Error, el email ya existe"; //guardar datos para que se queden los campos
        // del formulario menos el email y el password
        header('Location: index.php');
    }
    else {


        //si no existe se envia el correo y lo registro con el email y el token generado
        $asunto = "Verificación de correo";
        $token = hash('sha256', rand(1, 15000));
        $mensaje = "Pincha en este enlace para confirmar tu correo: http://aula2gs.edu...verificar.php?email=" . $_POST['email'] . '&token=' . $token;;
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        //registro del usuario en la base de datos
        $insertar = $usuBD->insertarUsuario($_POST['dni'], $_POST['apellidos'], $_POST['nombre'], $_POST['email'], $password, $token, 1);
        if($insertar){
            $correo = enviarCorreoGmail($_POST['email'], $asunto, $mensaje);
            if($correo){
                echo "UsuarioBBDD registrado, entra en tu buzón y haz clic en el enlace para confirmar tu correo";
                //ahora habría que enviarlo al index(login)
            }
            else{
                session_start();
                $_SESSION["error"] = "Error, no se ha podido enviar el correo";
                header('Location: index.php');
            }
        }
        else{
            session_start();
            $_SESSION["error"] = "Error, no se ha podido insertar el usuario"; //guardar datos para que se queden los campos
            // del formulario menos el email y el password
            header('Location: index.php');
        }


    }
}
else{
    header('Location: index.php');
}

function enviarCorreoGmail($email, $asunto, $mensaje){
    $resultado = false;
    //Load Composer's autoloader (created by composer, not included with PHPMailer)
    require '../../../vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = 'smtp.gmail.com';                             //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = 'javi2006pj@gmail.com';                     //SMTP username
        $mail->Password = 'xjzo zywf bpkn qift';                               //SMTP password
        $mail->SMTPSecure = 'tls';                                  //seguridad tls explicito
        $mail->Port = 587;                                          //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('noreply@gmail.com', 'Mailer');
        $mail->addAddress($email);                                  //Name is optional


        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;
        $resultado =  $mail->send();
        echo '<script>alert("Correo enviado");</script>';
    } catch (Exception $e) {
        //echo "El correo no ha sido enviado. Error: {$mail->ErrorInfo}";
        echo '<script>alert("Error, el correo no se ha enviado");</script>';

    }
    return $resultado;

}

function reenviarCorreo($email){
    $asunto = "Verificación de correo";
    $usuBD = new UsuarioBBDD();
    $token = $usuBD->obtenerTokern($email);
    if ($token) {
        $mensaje = "Pincha en este enlace para confirmar tu correo: htpp://aula2gs.edu/ejemplosPHP/ejemplo36-phpMailer/verificar.php?email=" . $_POST['email'] . '&token=' . $token;;
        $correo = enviarCorreoGmail($_POST['email'], $asunto, $mensaje);

        if ($correo) {
            echo "Entra en tu buzón y haz clic en el enlace para confirmar tu correo";
            //ahora habría que enviarlo al index(login)
        } else {
            session_start();
            $_SESSION["error"] = "Error, no se ha podido enviar el correo";
            header('Location: index.php');
        }
    }
    else {
        session_start();
        $_SESSION["error"] = "Error, no existe ese email";
        header('Location: reenviar.php');
    }
}
