<?php
session_start();  // Start the session

// Include the database connection
require '../../private/conn_digidate_examen.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['userId'])) {
        die('User is not logged in.');
    }

    $userId = $_SESSION['userId'];  // Get the logged-in user's ID from the session

    $sqlMatchedUserDetails = "SELECT userProfileId FROM userprofiles WHERE FKuserId = :userId";
    $stmtMatchedUserDetails = $conn->prepare($sqlMatchedUserDetails);
    $stmtMatchedUserDetails->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmtMatchedUserDetails->execute();
    $matchedUserDetails = $stmtMatchedUserDetails->fetch(PDO::FETCH_ASSOC);

    $profileIdToUnmatch = $_POST['profileIdToUnmatch'];  // Get the profile to unmatch

    if (empty($profileIdToUnmatch)) {
        die('Profile ID to unmatch is missing.');
    }

    try {
        // Prepare the query to update the FKchoice to 2 (indicating unmatch)
        $query = "UPDATE userSwipeChoices
                  SET FKchoice = 2
                  WHERE FKuserProfileId1 = :userId AND FKuserProfileId2 = :profileIdToUnmatch";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':userId', $matchedUserDetails['userProfileId'], PDO::PARAM_INT);
        $stmt->bindParam(':profileIdToUnmatch', $profileIdToUnmatch, PDO::PARAM_INT);

//        delete all the chats between the two users
        $queryDeleteChats = "DELETE FROM chats WHERE (FKuserProfileId1 = :userId AND FKuserProfileId2 = :profileIdToUnmatch) OR (FKuserProfileId1 = :profileIdToUnmatch AND FKuserProfileId2 = :userId)";
        $stmtDeleteChats = $conn->prepare($queryDeleteChats);
        $stmtDeleteChats->bindParam(':userId', $matchedUserDetails['userProfileId'], PDO::PARAM_INT);
        $stmtDeleteChats->bindParam(':profileIdToUnmatch', $profileIdToUnmatch, PDO::PARAM_INT);


        // Execute the query
        if ($stmt->execute() && $stmtDeleteChats->execute()) {
            header("Location: ../index.php?page=match&status=unmatched");
            exit();
        } else {
            // Query execution failed
            echo "Error: Unable to unmatch. Please try again.";
        }
    } catch (PDOException $e) {
        // Catch any PDO exceptions and display the error
        echo "Database error: " . $e->getMessage();
    }
} else {
    // If the request method is not POST
    echo "Invalid request.";
}
