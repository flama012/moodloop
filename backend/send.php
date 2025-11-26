<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();

if (isset($_POST['enviar'])) {
    // recoger datos
    $nombre     = $_POST["nombre"];
    $correo     = $_POST["correo"];
    $password   = $_POST["password"];
    $confirmar  = $_POST["confirmar"];

    //Contraseña es la misma que confirmar contraseña
    if ($password != $confirmar) {
        $_SESSION["mensaje"] = "Las contraseñas no coinciden.";
        header("Location: registro.php");
        exit();
    }

    //verificar si existe ya ese correo en la tabla usuarios
    require_once 'UsuarioBBDD.php';
    $usuBD = new UsuarioBBDD();
    if ($usuBD->existeEmail($correo)) {
        $_SESSION["error"] = "Error, el email ya existe"; //guardar datos para que se queden los campos
        // del formulario menos el email y el password
        header('Location: registro.php');
    }
    else {
        //si no existe se envia el correo y lo registro con el email y el token generado
        $asunto = "Verificación de correo";
        $token = hash('sha256', rand(1, 15000));
        $mensaje = "Pincha en este enlace para confirmar tu correo: http://aula2gs.edu/proyecto/moodloop/backend/verificar.php?email=" . $correo . '&token=' . $token;;
        $passwordHaseada = password_hash($password, PASSWORD_DEFAULT);
        //registro del usuario en la base de datos
        $insertar = $usuBD->insertarUsuario(
            null,                       // id_usuario autoincrement
                    $nombre,                     // nombre_usuario
                    $correo,                     // correo
                    $passwordHaseada,            // contraseña_hash
            "",                          // biografia
                "",                          // estado_emocional
                2,                           // id_rol (usuario)
            0,                           // confirmado
            0,                           // baneado
            date("Y-m-d H:i:s"),         // fecha_registro
            $token                       // token
        );
        if($insertar){
            $correoEnviado = enviarCorreoGmail($correo, $asunto, $mensaje);
            if($correoEnviado){
                echo "UsuarioBBDD registrado, entra en tu buzón y haz clic en el enlace para confirmar tu correo";
                //ahora habría que enviarlo al index(login)
                echo '<a href="login.php">Volver al login</a>';
            }
            else{
                $_SESSION["error"] = "Error, no se ha podido enviar el correo";
                header('Location: registro.php');
            }
        }
        else{
            $_SESSION["error"] = "Error, no se ha podido insertar el usuario"; //guardar datos para que se queden los campos
            // del formulario menos el email y el password
            header('Location: registro.php');
        }


    }
}
else{
    header('Location: registro.php');
}

function enviarCorreoGmail($email, $asunto, $mensaje){
    $resultado = false;
    //Load Composer's autoloader (created by composer, not included with PHPMailer)
    require '../../../../vendor/autoload.php';

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

//Reenviar el correo si no ha podido antes
function reenviarCorreo($email){
    $asunto = "Verificación de correo";
    $usuBD = new UsuarioBBDD();
    $token = $usuBD->obtenerTokern($email);
    if ($token) {
        $mensaje = "Pincha en este enlace para confirmar tu correo: htpp://aula2gs.edu/proyecto/moodloop/verificar.php?email=" . $_POST['email'] . '&token=' . $token;;
        $correo = enviarCorreoGmail($_POST['email'], $asunto, $mensaje);

        if ($correo) {
            echo "Entra en tu buzón y haz clic en el enlace para confirmar tu correo";
            //ahora habría que enviarlo al index(login)
            echo '<a href="login.php">Volver al login</a>';

        } else {
            $_SESSION["error"] = "Error, no se ha podido enviar el correo";
            header('Location: registro.php');
        }
    }
    else {
        $_SESSION["error"] = "Error, no existe ese email";
        header('Location: reenviar.php');
    }
}
