<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();

    require '../../private/conn_digidate_examen.php';
    $mailer = '../mailer/mailer.php';

    if (empty($_POST['postalCode']) || empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['email']) || empty($_POST['phonenumber']) || empty($_POST['residence']) || empty($_POST['password']) || empty($_POST['dob']) || empty($_POST['gender']) || empty($_POST['study'])) {

        $_SESSION['error'] = 'Er is een verplicht veld niet ingevuld.';
        $_SESSION['data'] = $_POST;
        header('Location: ../index.php?page=register');
        exit();
    }

    $dateOfBirth = new DateTime($_POST['dob']);
    $currentDate = new DateTime();
    $ageInterval = $currentDate->diff($dateOfBirth);

    if ($ageInterval->y < 18) {
        $_SESSION['error'] = 'Je moet 18 jaar of ouder zijn.';
        $_SESSION['data'] = $_POST;
        header('Location: ../index.php?page=register');
        exit();
    }
    if (strlen($_POST['password']) < 8 || !preg_match("/\d/", $_POST['password']) || !preg_match("/[A-Z]/", $_POST['password']) || !preg_match("/[a-z]/", $_POST['password']) || !preg_match("/\W/", $_POST['password'])) {
        $_SESSION['error'] = 'Wachtwoord voldoet niet aan vereisten.';
        $_SESSION['data'] = $_POST;
        header('Location: ../index.php?page=register');
        exit();
    }

    $sql = "SELECT email FROM users WHERE email = :email";
    $query = $conn->prepare($sql);
    $query->bindParam(':email', $_POST['email']);
    $query->execute();


    $hashedpassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($query->rowCount() == 0) {
//        $pfp = base64_encode(file_get_contents($_FILES['pfp']['tmp_name']));


        $stmt = $conn->prepare("INSERT INTO users (firstname, middlename, lastname, email, password, phonenumber, dob, study, gender,postalCode ,residence, roleId, deletedAt)
            VALUES(:firstName, :middleName, :lastName, :email, :password, :phonenumber, :dob, :study, :gender,:postalCode, :residence, :roleId, :deletedAt)");

        $stmt->execute([
            ':firstName' => $_POST['firstName'],
            ':middleName' => $_POST['middleName'],
            ':lastName' => $_POST['lastName'],
            ':email' => $_POST['email'],
            ':password' => $hashedpassword,
            ':phonenumber' => $_POST['phonenumber'],
            ':dob' => $_POST['dob'],
            ':study' => $_POST['study'],
            ':gender' => $_POST['gender'],
            ':postalCode' => $_POST['postalCode'],
            ':residence' => $_POST['residence'],
            ':roleId' => 1,
            ':deletedAt' => null
        ]);


        $_SESSION['mailCode'] = 'activateAccount';
        header('Location: ../php/send_login_code.php?userId=' . urlencode($conn->lastInsertId()));

        exit();
    } else {

        $_SESSION['error'] = 'Email bestaat al.';
        $_SESSION['data'] = $_POST;
        header('Location: ../index.php?page=register');
        exit();
    }

} else {
    header('Location: ../index.php?page=register');
    exit();
}


