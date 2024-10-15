<?php
// Start the session and include database connection
session_start();
include '../../private/conn_digidate_examen.php';

// Debugging: Check what session values are being set
if (!isset($_SESSION['userId'])) {
    exit('User is not logged in.');
}

if (!isset($_SESSION['roleId']) || $_SESSION['roleId'] !== 2) {
    exit('Access denied.');
}

// Check if the userId is provided via POST
if (!isset($_POST['userId'])) {
    exit('Invalid request.');
}
$userId = $_POST['userId'];

try {
    // Hard delete the user from the database
    $query = "DELETE FROM users WHERE userId = :userId AND roleId = 2";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    // Check if any row was actually deleted
    if ($stmt->rowCount() > 0) {
        header('Location: ../index.php?page=view_admin&message=User deleted successfully.');
        exit();
    } else {
        header('Location: ../index.php?page=view_admin&error=No user found or unable to delete.');
        exit();
    }

} catch (PDOException $e) {
    // Error handling
    header('Location: admin_dashboard.php?error=' . urlencode("Error: " . $e->getMessage()));
    exit();
}
?>
