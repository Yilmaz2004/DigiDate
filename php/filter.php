<?php
// php/filter.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require '../../private/conn_digidate_examen.php';
    session_start();
    $userId = $_SESSION['userId'];

    // Check if tags are selected, otherwise set to an empty array
    $_SESSION['tags'] = isset($_POST['tags']) ? $_POST['tags'] : [];

    // Store age filters
    $minAge = isset($_POST['minAge']) ? $_POST['minAge'] : null;
    $maxAge = isset($_POST['maxAge']) ? $_POST['maxAge'] : null;

    // Validate age filters
    if (empty($minAge) || empty($maxAge)) {
        $_SESSION['error'] = 'Vul de leeftijden in.';
        header('Location: ../index.php?page=filter');
        exit();
    }

    // Validate age range
    if ($minAge >= $maxAge) {
        $_SESSION['error'] = 'Minimale leeftijd moet minder zijn dan maximale leeftijd.';
        header('Location: ../index.php?page=filter');
        exit();
    }

    // Store age filters in session
    $_SESSION['minAge'] = $minAge;
    $_SESSION['maxAge'] = $maxAge;

    // Store gender filter
    $_SESSION['gender'] = isset($_POST['gender']) ? $_POST['gender'] : null;

    header('Location: ../index.php?page=swipe');
    exit();
} else {
    header('Location: ../index.php?page=filter');
    exit();
}