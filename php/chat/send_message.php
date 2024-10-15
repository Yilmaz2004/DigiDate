<?php
session_start();
require '../../../private/conn_digidate_examen.php';

if (!isset($_SESSION['userId']) || !isset($_POST['receiverProfileId']) || !isset($_POST['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Er ontbreken gegevens']);
    exit();
}

$senderUserId = $_SESSION['userId'];

// Get sender's profile ID -Sam
$sqlSenderProfile = "SELECT userProfileId FROM userprofiles WHERE FKuserId = :userId";
$stmtSenderProfile = $conn->prepare($sqlSenderProfile);
$stmtSenderProfile->bindParam(':userId', $senderUserId);
$stmtSenderProfile->execute();
$senderProfile = $stmtSenderProfile->fetch(PDO::FETCH_ASSOC);

if (!$senderProfile) {
    http_response_code(400);
    echo json_encode(['error' => 'Profiel niet gevonden']);
    exit();
}

$senderProfileId = $senderProfile['userProfileId'];
$receiverProfileId = $_POST['receiverProfileId'];
$message = trim($_POST['message']);

// Verify that these users have matched -Sam
$sqlCheckMatch = "SELECT 1 FROM userswipechoices usc1
                      JOIN userswipechoices usc2 ON usc1.FKuserProfileId1 = usc2.FKuserProfileId2 
                                                AND usc1.FKuserProfileId2 = usc2.FKuserProfileId1
                      WHERE usc1.FKuserProfileId1 = :senderProfileId 
                      AND usc1.FKuserProfileId2 = :receiverProfileId
                      AND usc1.FKchoice = 1 AND usc2.FKchoice = 1";
$stmtCheckMatch = $conn->prepare($sqlCheckMatch);
$stmtCheckMatch->bindParam(':senderProfileId', $senderProfileId);
$stmtCheckMatch->bindParam(':receiverProfileId', $receiverProfileId);
$stmtCheckMatch->execute();

if ($stmtCheckMatch->rowCount() == 0) {
    http_response_code(403);
    echo json_encode(['error' => 'Gebruikers zijn geen match']);
    exit();
}

$sqlInsert = "INSERT INTO chats (FKuserProfileId1, FKuserProfileId2, message, sendAt) 
                  VALUES (:senderProfileId, :receiverProfileId, :message, NOW())";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bindParam(':senderProfileId', $senderProfileId);
$stmtInsert->bindParam(':receiverProfileId', $receiverProfileId);
$stmtInsert->bindParam(':message', $message);

if ($stmtInsert->execute()) {
    echo json_encode([
        'success' => true,
        'message' => $message,
        'sendAt' => date('Y-m-d H:i:s')
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Er is een fout opgetreden bij het verzenden van het bericht']);
}
?>