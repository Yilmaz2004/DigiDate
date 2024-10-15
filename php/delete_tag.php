<?php
session_start();
include '../../private/conn_digidate_examen.php';

if (!isset($_SESSION['userId']) || $_SESSION['roleId'] !== 2) {
    exit('Access denied.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tagId = intval($_POST['tagId']);

    if (empty($tagId)) {
        header('Location: ../index.php?page=view_tags?error=Invalid tag ID');
        exit();
    }

    try {
//            set enabled to false
        $query = "UPDATE tags SET enabled = false WHERE tagId = :tagId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':tagId', $tagId);
        $stmt->execute();

        header('Location: ../index.php?page=view_tags&message=Tag disabled successfully');
    } catch (PDOException $e) {
        header('Location: ../index.php?page=view_tags&error=' . urlencode($e->getMessage()));
    }
}
?>