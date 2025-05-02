<?php
// Import PHPMailer classes at the top level
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Configuración de logs
ini_set('log_errors', 1);
ini_set('error_log', 'email_errors.log');

// Cargar Composer autoloader (si está instalado)
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
    $usePHPMailer = true;
} else {
    $usePHPMailer = false;
    error_log('PHPMailer no disponible: autoload.php no encontrado');
}

// Validar método de solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log('Método de solicitud no permitido: ' . $_SERVER['REQUEST_METHOD']);
    
    // Respuesta más amigable para accesos directos (GET)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Error - Método no permitido</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 50px auto; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
                h1 { color: #e48416; }
                p { line-height: 1.6; }
                .note { background: #f8f8f8; padding: 10px; border-left: 4px solid #e48416; }
                code { background: #f0f0f0; padding: 2px 4px; border-radius: 3px; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Método no permitido</h1>
                <p>Este script está diseñado para recibir datos desde un formulario mediante el método POST.</p>
                <div class="note">
                    <p><strong>Nota para desarrolladores:</strong> Asegúrate de que tu formulario HTML tenga los siguientes atributos:</p>
                    <code>method="post" action="send_email.php" enctype="multipart/form-data"</code>
                    <p>Ejemplo de formulario correcto:</p>
                    <pre><code>&lt;form method="post" action="send_email.php"&gt;
    &lt;input type="text" name="name" placeholder="Nombre" required&gt;
    &lt;input type="email" name="email" placeholder="Email" required&gt;
    &lt;textarea name="message" placeholder="Mensaje" required&gt;&lt;/textarea&gt;
    &lt;button type="submit"&gt;Enviar&lt;/button&gt;
&lt;/form&gt;</code></pre>
                </div>
            </div>
        </body>
        </html>';
        exit;
    }
    
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar que los campos existan
if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['message'])) {
    error_log('Datos de formulario incompletos');
    // Registrar para depuración qué datos se recibieron
    error_log('POST recibido: ' . json_encode($_POST));
    
    echo json_encode(['success' => false, 'message' => 'Datos de formulario incompletos']);
    exit;
}

// Obtener y sanitizar datos - reemplazar FILTER_SANITIZE_STRING con htmlspecialchars
$name = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8');

// Validaciones
if (empty($name) || empty($email) || empty($message)) {
    error_log('Campos vacíos detectados');
    echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log('Email inválido: ' . $email);
    echo json_encode(['success' => false, 'message' => 'Email inválido']);
    exit;
}

// Destino del correo
$to = 'contacto@viewmedia.cl';
$subject = 'Nuevo mensaje de contacto desde el sitio web';

// Correo electrónico en formato HTML
$email_content = "
<html>
<head>
    <title>Nuevo mensaje de contacto</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        h2 { color: #e48416; }
        .info { margin: 10px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <h2>Nuevo mensaje de contacto</h2>
        <div class='info'><strong>Nombre:</strong> {$name}</div>
        <div class='info'><strong>Email:</strong> {$email}</div>
        <div class='info'><strong>Mensaje:</strong></div>
        <div class='info'>{$message}</div>
    </div>
</body>
</html>
";

// Cargar configuración SMTP desde archivo externo o variables de entorno
// (Esta es una implementación de muestra - en producción usar variables de entorno o archivos .env)
function getSmtpSettings() {
    // Idealmente, cargar desde .env o config.php
    return [
        'host' => 'mail.viewmedia.cl',
        'username' => 'contacto@viewmedia.cl',
        'password' => 'Osmolive128"', // En producción, cargar desde variable de entorno
        'port' => 465,
        'secure' => 'ssl'
    ];
}

if ($usePHPMailer && class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    // Usar PHPMailer si está disponible
    try {
        // Crear una nueva instancia de PHPMailer
        $mail = new PHPMailer(true);
        
        // Cargar configuración SMTP
        $smtp = getSmtpSettings();

        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = $smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['username'];
        $mail->Password = $smtp['password'];
        $mail->SMTPSecure = $smtp['secure'];
        $mail->Port = $smtp['port'];
        $mail->CharSet = 'UTF-8';

        // Remitentes y destinatarios
        $mail->setFrom('contacto@viewmedia.cl', 'View Media Contacto');
        $mail->addAddress('contacto@viewmedia.cl', 'View Media');
        $mail->addReplyTo($email, $name);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $email_content;
        $mail->AltBody = strip_tags($email_content);

        // Enviar el correo
        $mail->send();
        error_log('Correo enviado exitosamente a ' . $to);
        echo json_encode(['success' => true, 'message' => 'Mensaje enviado exitosamente']);
        
    } catch (Exception $e) {
        error_log('Error al enviar correo: ' . ($mail->ErrorInfo ?? 'Error desconocido'));
        echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje. Por favor, intente más tarde.']);
    }
} else {
    // Fallback a la función mail() de PHP si PHPMailer no está disponible
    $headers = "From: contacto@viewmedia.cl\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    if (mail($to, $subject, $email_content, $headers)) {
        error_log('Correo enviado exitosamente a ' . $to . ' usando mail()');
        echo json_encode(['success' => true, 'message' => 'Mensaje enviado exitosamente']);
    } else {
        error_log('Error al enviar correo usando mail()');
        echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje. Por favor, intente más tarde.']);
    }
}
?> 