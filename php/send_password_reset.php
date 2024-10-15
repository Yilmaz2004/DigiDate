<?php
session_start();
include '../../private/conn_digidate_examen.php';

// Check if the account even exists
$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email = :email";
$query = $conn->prepare($sql);
$query->bindParam(':email', $email);
$query->execute();
$userData = $query->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    $_SESSION['error'] = "No account found with that email address.";
    header('location: ../index.php?page=forgot_password');
    exit();
}

$token = uniqid();
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$sql = "UPDATE users SET resetToken = :token, resetTokenExpiresAt = :expiry WHERE email = :email";
$query = $conn->prepare($sql);
$query->bindParam(':token', $token);
$query->bindParam(':expiry', $expiry);
$query->bindParam(':email', $email);
$query->execute();

$mail = require __DIR__ . "../../mailer/mailer.php"; // Ensure this returns the mailer object

$mail->setFrom("noreply@digidate.nl");
$mail->addAddress($userData['email']);
$mail->Subject = "Reset je wachtwoord";
$body = file_get_contents(__DIR__ . "../../partials/mails/forgot_password.php");
$body = str_replace("{{token}}", $token, $body);
$mail->Body = $body;

try {
    ob_start(); // Start output buffering
    $mail->send();
    ob_end_clean(); // Clean (erase) the output buffer and turn off output buffering
    $_SESSION['success'] = "An email has been sent to reset your password.";
    header('location: ../index.php?page=forgot_password');
    exit();
} catch (Exception $e) {
    ob_end_clean(); // Clean (erase) the output buffer and turn off output buffering
    $_SESSION['error'] = "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    header('location: ../index.php?page=forgot_password');
    exit();
}
?>