<?php
// ------------------------------------------
// Pengaturan Email jangan diganti
// Kecuali sudah diperintahkan oleh kak Azhar
// ------------------------------------------
$smtp     = "mail.cerdasly.com";
$email    = "admin@cerdasly.com";
$password = "azharrudin595";
$sender   = "Admin Cerdasly";
// ------------------------------------------
require_once(__DIR__."/../vendor/autoload.php");
// ------------------------------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//-------------------------------------------
require_once(__DIR__ . "/../vendor/phpmailer/phpmailer/src/Exception.php");
require_once(__DIR__ . "/../vendor/phpmailer/phpmailer/src/PHPMailer.php");
require_once(__DIR__ . "/../vendor/phpmailer/phpmailer/src/SMTP.php");
function sendVerification($to, $code, $message = ""){
    // default message
    if(strlen($message) == 0){
        $message     = "Halo, untuk mengaktifkan akun yang anda sedang dibuat, gunakan kode verifikasi: <br><h2><tt>$code</tt></h2><br>(<b>jangan sebarkan kesiapapun</b>).";
        $message_alt = "Halo, untuk mengaktifkan akun yang anda sedang dibuat, gunakan kode verifikasi: \n$code\n(jangan sebarkan kesiapapun).";
    }
    else {
        $message_alt = strip_tags($message);
    }
    global $smtp;
    global $email;
    global $password;
    global $sender;
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $smtp;
        $mail->SMTPAuth   = true;
        $mail->Port       = 465;
        $mail->SMTPSecure = "ssl";
        $mail->Username   = $email;
        $mail->Password   = $password; 
        $mail->From       = $email;
        $mail->FromName   = "Admin Cerdasly";
        $mail->addAddress($to, 'Pengguna Cerdasly');
        $mail->addReplyTo($email, $sender);
        $mail->IsHTML(true);
        $mail->Subject = "Informasi penting dari Cerdasly";
        $mail->Body    = $message;
        $mail->AltBody = $message_alt;
        $mail->send();
        return $code;
    } catch (Exception $e) {
        return false;
    }
}