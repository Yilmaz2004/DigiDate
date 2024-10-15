<?php
session_start();

if (isset($_SESSION['userId'])) {
    session_destroy();
    header('Location: ../index.php?page=login');
    exit();
} else {
    header('Location: ../index.php?page=login');
    exit();
}
?>
