<?php
session_start();
include '../../private/conn_digidate_examen.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resetToken = $_POST['token'];

    if (empty($resetToken)) {
        header('Location: ../index.php?page=register');
        exit();
    }

    $query = "SELECT * FROM users WHERE resetToken = :resetToken";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':resetToken', $resetToken);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: ../index.php?page=register');
        exit();
    }

    $expiry = new DateTime($user['resetTokenExpiresAt']);
    $currentDate = new DateTime();

    if ($expiry < $currentDate) {
        $query = "UPDATE users SET resetToken = null, resetTokenExpiresAt = null WHERE resetToken = :resetToken";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':resetToken', $resetToken);
        $stmt->execute();

        header('Location: ../index.php?page=register');
        exit();
    }

    $password = $_POST['password'];
    $passwordConfirm = $_POST['confirmPassword'];

    if (empty($password) || empty($passwordConfirm)) {
        header('Location: ../index.php?page=forgot_password&token=' . $resetToken . '&error=Vul beide wachtwoordvelden in');
        exit();
    }

    if ($password !== $passwordConfirm) {
        header('Location: ../index.php?page=forgot_password&token=' . $resetToken . '&error=Wachtwoorden komen niet overeen');
        exit();
    }


    if (strlen($password) < 8 || !preg_match("/\d/", $password) || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/\W/", $password)) {
        header('Location: ../index.php?page=register');
        exit();
    }

    $hashedpassword = password_hash($password, PASSWORD_DEFAULT);

    $query = "UPDATE users SET password = :password, resetToken = null, resetTokenExpiresAt = null WHERE resetToken = :resetToken";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':password', $hashedpassword);
    $stmt->bindParam(':resetToken', $resetToken);
    $stmt->execute();

    header('Location: ../index.php?page=login');
}
?>