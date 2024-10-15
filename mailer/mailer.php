<?php
require '../../private/conn_digidate_examen.php';
require '../vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$emailUsername = "scrizz0318@gmail.com";
$emailPassword = "exuk madc wlaj niez";

$mail = new PHPMailer(true);

$mail->SMTPDebug = SMTP::DEBUG_SERVER;

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host = "smtp.gmail.com";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->Username = $emailUsername;
$mail->Password = $emailPassword;

$mail->isHtml(true);

return $mail;