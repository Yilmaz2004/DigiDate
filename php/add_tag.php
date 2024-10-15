<?php
session_start();
include '../../private/conn_digidate_examen.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tagName = trim($_POST['tagName']);

    if (empty($tagName)) {
        header('Location: ../index.php?page=view_tags?error=Tag name cannot be empty');
        exit();
    }

    try {
        $query = "INSERT INTO tags (tagName, enabled) VALUES (:tagName, true)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':tagName', $tagName);
        $stmt->execute();

        header('Location: ../index.php?page=view_tags&message=Tag added successfully');
    } catch (PDOException $e) {
        header('Location: ../index.php?page=view_tags&error=' . urlencode($e->getMessage()));
    }
}
?>