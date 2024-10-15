<?php
include '../../private/conn_digidate_examen.php';
session_start();

$verificationCode = mt_rand(100000, 999999);
$_SESSION['verification_code'] = $verificationCode;

$userid = $_GET['userId'];

$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);

$verificationCode = mt_rand(100000, 999999);
$_SESSION['verification_code'] = $verificationCode;
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$sql = "SELECT userid,email  FROM users where userid = :userid ";
$query = $conn->prepare($sql);
$query->bindParam(':userid', $userid);
$query->execute();
$userData = $query->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("UPDATE users SET 2faToken = :tokenhash ,  2faTokenExpiresAt = :expiry WHERE email = :email");
$stmt->bindParam(':tokenhash', $token_hash);
$stmt->bindParam(':expiry', $expiry);
$stmt->bindParam(':email', $userData['email']);
$stmt->execute();

$mail = require __DIR__ . "../../mailer/mailer.php";

if ($query->rowCount() == 1) {

    if (isset($_SESSION['mailCode']) && $_SESSION['mailCode'] == 'activateAccount') {

        $mail->setFrom("noreply@digidate.nl");
        $mail->addAddress($userData['email']);
        $mail->Subject = "Confirm login";
        $mail->Body = <<<END

Click <a href="http://127.0.0.1:8000/index.php?page=2fa&token=$token_hash">here</a> to activate your account. Your code is : $verificationCode

END;

        try {
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }

        unset($_SESSION['userId']);

    } elseif (isset($_SESSION['mailCode']) && $_SESSION['mailCode'] == '2fa') {

        $mail->setFrom("noreply@digidate.nl");
        $mail->addAddress($userData['email']);
        $mail->Subject = "Confirm login";
        $mail->Body = <<<END

Click <a href="http://127.0.0.1:8000/index.php?page=2fa&token=$token_hash">here</a> verify. Your code is : $verificationCode

END;

        try {
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }

        unset($_SESSION['userId']);

    }


}

header('location: ../index.php?page=checkEmail');


