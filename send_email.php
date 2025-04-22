<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Configuración de logs
ini_set('log_errors', 1);
ini_set('error_log', 'email_errors.log');

// Cargar Composer autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Validar método de solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log('Método de solicitud no permitido: ' . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener y sanitizar datos
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

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

// Crear una nueva instancia de PHPMailer
$mail = new PHPMailer(true);

try {
    // Configuración del servidor
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tu-email@gmail.com'; // Reemplaza con tu email de Gmail
    $mail->Password = 'tu-contraseña-de-aplicación'; // Reemplaza con tu contraseña de aplicación
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    // Remitentes y destinatarios
    $mail->setFrom('contacto@viewmedia.cl', 'View Media Contacto');
    $mail->addAddress('contacto@viewmedia.cl', 'View Media');
    $mail->addReplyTo($email, $name);

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = 'Nuevo mensaje de contacto desde el sitio web';
    
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
    
    $mail->Body = $email_content;
    $mail->AltBody = strip_tags($email_content);

    // Enviar el correo
    $mail->send();
    error_log('Correo enviado exitosamente a contacto@viewmedia.cl');
    echo json_encode(['success' => true, 'message' => 'Mensaje enviado exitosamente']);
    
} catch (Exception $e) {
    error_log('Error al enviar correo: ' . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje: ' . $mail->ErrorInfo]);
}
?> 