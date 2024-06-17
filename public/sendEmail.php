<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

return function (String $emailReceiver, String $subject, String $body): void
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tailscaleadio@gmail.com';
        $mail->Password = 'oujvgjcucgtdmmhn';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Remitente
        $mail->setFrom('tailscaleadio@gmail.com', 'Equipo de task manager');

        // Destinatario
        $mail->addAddress($emailReceiver);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        echo 'Correo enviado exitosamente';
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
};
