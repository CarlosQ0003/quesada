<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar los campos del formulario
    if (empty($_POST['name']) || empty($_POST['msg']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(500);
        exit();
    }

    // Obtener datos del formulario y sanearlos
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['msg']);

    // Configuración de PHPMailer
    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host = 'smtp.zeptomail.com'; // Cambiar el host SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'emailapikey'; // Cambiar el nombre de usuario SMTP
    $mail->Password = 'wSsVR60n/EShC65+yDGsJOZqkVRdA1mgQ08p3FOj6CT/TfiX9sdplkScBQGkG/FLQzRpRzARobp4yxgI12JdjdgunFgEWyiF9mqRe1U4J3x17qnvhDzOWmxbmxOLK4oKwwponGBlE8kh+g=='; // Cambiar la contraseña SMTP
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Configurar remitente y destinatario
    $mail->setFrom('noreply@loscorehardcar.shop', 'Carlos');
    $mail->addAddress('1442681@loscorehardcar.shop');

    // Configurar contenido del mensaje
    $mail->isHTML(true);
    $mail->Subject = "Nuevo Mensaje de Contacto";
    $mail->Body = "Has recibido un nuevo mensaje desde el formulario de contacto de tu sitio web.<br><br>Detalles:<br><br>Nombre: $name<br>Email: $email<br>Mensaje: $message";

    // Si se ha enviado un archivo adjunto
    if (!empty($_FILES['adjunto']['name'])) {
        $adjunto_nombre = $_FILES['adjunto']['name'];
        $adjunto_tmp_name = $_FILES['adjunto']['tmp_name'];

        if ($_FILES['adjunto']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(500);
            echo "Error al subir el archivo: " . $_FILES['adjunto']['error'];
            exit();
        }

        // Definir la ruta donde deseas guardar los archivos adjuntos
        $ruta_destino = 'adjuntos/' . $adjunto_nombre;

        // Mover el archivo cargado a la ubicación deseada
        if (!move_uploaded_file($adjunto_tmp_name, $ruta_destino)) {
            http_response_code(500);
            echo "Error al mover el archivo adjunto";
            exit();
        }

        // Agregar el archivo adjunto al correo
        $mail->addAttachment($ruta_destino);
    }

    try {
        // Enviar correo
        $mail->send();

        // Enviar correo de agradecimiento al usuario
        $mail->clearAddresses();
        $mail->addAddress($email);
        $mail->Subject = "Gracias por ponerte en contacto";
        $mail->Body = "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Agradecimiento</title>
            <style>
                /* Estilos CSS personalizados */
                body {
                    font-family: 'Comic Sans MS', cursive, sans-serif;
                    background-color: #f2f2f2;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    border: 2px dashed #4285f4;
                    padding: 20px;
                    margin: 20px auto;
                    background-color: #ffffff;
                    max-width: 600px;
                    border-radius: 10px;
                    text-align: center;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                h1 {
                    color: #4285f4;
                    font-size: 36px;
                    margin-bottom: 20px;
                    text-shadow: 2px 2px 2px rgba(0, 0, 0, 0.1);
                }
                p {
                    color: #333333;
                    font-size: 18px;
                    line-height: 1.6;
                    margin-bottom: 10px;
                }
                strong {
                    font-weight: bold;
                    color: #ff5722;
                }
                a {
                    color: #4285f4;
                    text-decoration: none;
                }
                a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>¡Gracias por Ponerte en Contacto!</h1>
                <p>¡Hola <strong><?php echo htmlspecialchars(\$name); ?>!</strong></p>
                <p>¡Tu mensaje nos ha alegrado el día!</p>
                <p>Pronto nos pondremos en contacto contigo a través de <strong><?php echo htmlspecialchars(\$email); ?></strong>.</p>
                <p><em>¡Estamos ansiosos por hablar contigo!</em></p>
                <p><a href='https://www.tuempresa.com' target='_blank'>¡Visita nuestro sitio web para descubrir más sorpresas!</a></p>
                <img src='https://via.placeholder.com/400' alt='Gracias' style='max-width: 100%; border-radius: 10px; margin-top: 20px;'>
            </div>
        </body>
        </html>";
        

        $mail->send();

        echo "Mensaje enviado correctamente";
    } catch (Exception $e) {
        http_response_code(500);
        echo "Error al enviar el mensaje";
        exit();
    }
} else {
    // Redirigir si no es una solicitud POST
    header("Location: contact.html");
  exit();
}
?>