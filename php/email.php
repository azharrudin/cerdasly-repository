<?php
// ------------------------------------------
// Pengaturan Email jangan diganti
// Kecuali sudah diperintahkan oleh kak Azhar
// ------------------------------------------
$smtp     = "smtp.mail.yahoo.com";
$email    = "herobrinepe404@yahoo.com";
$password = "hgzkbwixxnxgqvrq";
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
    if(strlen($message) == 0){
        $message     = "Hai, selamat datang di Cerdasly, untuk mengaktifkan akun yang sedang dibuat, anda dapat menggunakan kode verifikasi: <h4><tt>$code</tt></h4>.";
        $message_alt = "Hai, selamat datang di Cerdasly, untuk mengaktifkan akun yang sedang dibuat, anda dapat menggunakan kode verifikasi: $code.";
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
        $mail->Port       = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Username   = $email;
        $mail->Password   = $password; 
        $mail->setFrom($email, $sender);
        $mail->addAddress($to, 'Pengguna Baru');
        $mail->addReplyTo($email, $sender);
        $mail->IsHTML(true);
        $mail->Subject = "Selamat datang di Cerdasly - Ayo verifikasikan akunmu";
        $mail->Body    = $message;
        $mail->AltBody = $message_alt;
        $mail->send();
        return $code;
    } catch (Exception $e) {
        return false;
    }
}