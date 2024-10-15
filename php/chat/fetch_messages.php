<?php
session_start();
require '../../../private/conn_digidate_examen.php';

if (!isset($_SESSION['userId']) || !isset($_GET['matchProfileId'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required data']);
    exit();
}

$currentUserId = $_SESSION['userId'];
$lastFetchTime = isset($_GET['lastFetchTime']) ? $_GET['lastFetchTime'] : null;

// Get current user's profile ID -Sam
$sqlUserProfile = "SELECT userProfileId FROM userprofiles WHERE FKuserId = :userId";
$stmtUserProfile = $conn->prepare($sqlUserProfile);
$stmtUserProfile->bindParam(':userId', $currentUserId);
$stmtUserProfile->execute();
$currentUserProfile = $stmtUserProfile->fetch(PDO::FETCH_ASSOC);

if (!$currentUserProfile) {
    http_response_code(400);
    echo json_encode(['error' => 'User profile not found']);
    exit();
}

$currentProfileId = $currentUserProfile['userProfileId'];
$matchProfileId = $_GET['matchProfileId'];

// Verify that these users have matched -Sam
$sqlCheckMatch = "SELECT 1 FROM userswipechoices usc1
                      JOIN userswipechoices usc2 ON usc1.FKuserProfileId1 = usc2.FKuserProfileId2 
                                                AND usc1.FKuserProfileId2 = usc2.FKuserProfileId1
                      WHERE usc1.FKuserProfileId1 = :currentProfileId 
                      AND usc1.FKuserProfileId2 = :matchProfileId
                      AND usc1.FKchoice = 1 AND usc2.FKchoice = 1";
$stmtCheckMatch = $conn->prepare($sqlCheckMatch);
$stmtCheckMatch->bindParam(':currentProfileId', $currentProfileId);
$stmtCheckMatch->bindParam(':matchProfileId', $matchProfileId);
$stmtCheckMatch->execute();

if ($stmtCheckMatch->rowCount() == 0) {
    http_response_code(403);
    echo json_encode(['error' => 'Users are not matched']);
    exit();
}

$sqlMessages = "SELECT message, sendAt, FKuserProfileId1
                    FROM chats
                    WHERE (FKuserProfileId1 = :currentProfileId AND FKuserProfileId2 = :matchProfileId)
                    OR (FKuserProfileId1 = :matchProfileId AND FKuserProfileId2 = :currentProfileId)";

if ($lastFetchTime) {
    $sqlMessages .= " AND sendAt > :lastFetchTime";
}

$sqlMessages .= " ORDER BY sendAt ASC";

$stmtMessages = $conn->prepare($sqlMessages);
$stmtMessages->bindParam(':currentProfileId', $currentProfileId);
$stmtMessages->bindParam(':matchProfileId', $matchProfileId);

if ($lastFetchTime) {
    $stmtMessages->bindParam(':lastFetchTime', $lastFetchTime);
}

$stmtMessages->execute();
$messages = $stmtMessages->fetchAll(PDO::FETCH_ASSOC);

$processedMessages = array_map(function ($message) use ($currentProfileId) {
    return [
        'message' => $message['message'],
        'sendAt' => $message['sendAt'],
        'isOurs' => $message['FKuserProfileId1'] == $currentProfileId
    ];
}, $messages);

$latestTimestamp = $messages ? end($messages)['sendAt'] : ($lastFetchTime ?? date('Y-m-d H:i:s'));

echo json_encode([
    'messages' => $processedMessages,
    'latestTimestamp' => $latestTimestamp
]);
?>