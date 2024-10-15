<?php
include '../../private/conn_digidate_examen.php';
session_start();
$code = $_POST["code"];
$token = $_POST["token"];
$currentTime = date('Y-m-d H:i:s', time());

$sql = "SELECT * FROM users WHERE 2faToken = :token";
$query = $conn->prepare($sql);
$query->bindParam(':token', $token);
$query->execute();

if ($query->rowCount() > 0) {

    if ($_SESSION['verification_code'] == $code) {
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result['2faTokenExpiresAt'] > $currentTime) {

            if (isset($_SESSION['mailCode']) && $_SESSION['mailCode'] == 'activateAccount') {
                $stmt = $conn->prepare("UPDATE users SET 2faToken = null ,  2faTokenExpiresAt = null, activated = 1 WHERE 2faToken = :token");
                $stmt->execute(array("token" => $token));

                unset($_SESSION['mailCode']);

                header('location: ../index.php?page=login');
                exit();

            } elseif (isset($_SESSION['mailCode']) && $_SESSION['mailCode'] == '2fa') {
                $userLogin = $conn->prepare("SELECT * FROM users WHERE 2faToken = :token");
                $userLogin->execute(array(':token' => $token));
                $userData = $userLogin->fetch(PDO::FETCH_ASSOC);

                $stmt = $conn->prepare("UPDATE users SET 2faToken = null ,  2faTokenExpiresAt = null, activated = 1 WHERE 2faToken = :token");
                $stmt->execute(array("token" => $token));
                unset($_SESSION['mailCode']);

                $_SESSION['roleId'] = $userData['roleId'];
                $_SESSION['userId'] = $userData['userId'];

                if ($_SESSION['roleId'] == 2) {
                    header('location: ../index.php?page=home');
                    exit();
                }


                $checkUserProfile = $conn->prepare("SELECT * FROM userprofiles WHERE FKuserId = :FKuserId");
                $checkUserProfile->execute(array(':FKuserId' => $userData['userId']));


                if ($checkUserProfile->rowCount() == 0) {
                    header('location: ../index.php?page=add_profile');
                    exit();
                } else {
                    header('location: ../index.php?page=home');
                    exit();
                }
            }
        }

    } else {
        $_SESSION['error'] = 'Verkeerde code, probeer het nogmaals.';
        header("location: ../index.php?page=2fa&token=$token");
        exit();
    }

} else {
    $_SESSION['error'] = 'Token niet gevonden';
    header("location: ../index.php?page=404");
    exit();
}



