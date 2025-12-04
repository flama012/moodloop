<?php
// Este archivo simplemente imprime una página HTML completa.
// Se usa echo con comillas dobles para poder escribir HTML directamente.
// No contiene lógica de PHP, solo sirve como página informativa.

echo "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Información Legal</title>

    <!-- Estilos básicos para mejorar la presentación -->
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; line-height: 1.6; }
        h1, h2 { color: #333; }
        h1 { margin-bottom: 10px; }
        section { margin-bottom: 40px; }
    </style>
</head>
<body>

<h1>Información Legal</h1>

<!-- Sección de Términos y Condiciones -->
<section>
    <h2>Términos y Condiciones</h2>
    <p>El uso de este sitio web implica la aceptación de estas condiciones. El usuario se compromete a realizar
    un uso responsable y legal del servicio. Los contenidos, imágenes, textos y elementos del sitio están
    protegidos por derechos de propiedad intelectual y no pueden ser copiados sin autorización.
    Nos reservamos el derecho de modificar estas condiciones en cualquier momento.</p>
</section>

<!-- Sección de Política de Privacidad -->
<section>
    <h2>Política de Privacidad</h2>
    <p>Este sitio recopila los datos necesarios para el funcionamiento del servicio, como nombre, correo
    electrónico y datos técnicos del dispositivo. El responsable del tratamiento protege la información
    aplicando medidas de seguridad. Los usuarios pueden solicitar acceso, modificación o eliminación
    de sus datos escribiendo a nuestro correo de contacto.</p>
</section>

<!-- Sección de Política de Cookies -->
<section>
    <h2>Política de Cookies</h2>
    <p>Este sitio utiliza cookies técnicas para el funcionamiento básico y cookies analíticas para mejorar
    la experiencia del usuario. Puedes configurar tu navegador para bloquearlas. El uso de la web implica
    la aceptación de esta política de cookies.</p>
</section>

<!-- Enlace para volver al registro -->
<p>
    <a href='./registro.php'>Volver al registro</a>
</p>

</body>
</html>
";
?>
