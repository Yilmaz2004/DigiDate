<?php
session_start();
require '../../private/conn_digidate_examen.php';

try {
    // Controleer of de gebruiker is ingelogd en de userId in de sessie aanwezig is
    if (!isset($_SESSION['userId'])) {
        throw new Exception("Gebruiker is niet ingelogd.");
    }

    $userId = $_SESSION['userId'];

    if ($conn->inTransaction() === false) {
        $conn->beginTransaction();
    }

    // Controleer of er al een profiel bestaat voor deze userId
    $sqlCheck = "SELECT COUNT(*) FROM userProfiles WHERE FKuserId = :FKuserId";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bindParam(':FKuserId', $userId);
    $stmtCheck->execute();
    $profileExists = $stmtCheck->fetchColumn();

    // Verwerk de profielfoto
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
        $profilePicture = file_get_contents($_FILES['profilePicture']['tmp_name']);
    } else {
        throw new Exception("Profielfoto uploaden mislukt.");
    }

    // Profiel toevoegen
    $description = $_POST['description'];
    $genderPreference = $_POST['genderPreference'];
    $minAgePreference = $_POST['minAgePreference'];
    $maxAgePreference = $_POST['maxAgePreference'];

    // Controleer of minAgePreference niet groter is dan maxAgePreference
    if ($minAgePreference > $maxAgePreference) {
        throw new Exception("Minimum leeftijd voorkeur mag niet hoger zijn dan de maximum leeftijd voorkeur.");
    }
    if ($minAgePreference < 18) {
        throw new Exception("Minimum leeftijd voorkeur mag niet onder 18 jaar zijn");
    }

    if ($maxAgePreference < 18) {
        throw new Exception("Maximum leeftijd voorkeur mag niet onder 18 jaar zijn");
    }

    if ($minAgePreference > 99) {
        throw new Exception("Minimum leeftijd voorkeur mag niet boven 99 jaar zijn");
    }

    if ($maxAgePreference > 99) {
        throw new Exception("Maximum leeftijd voorkeur mag niet boven 99 jaar zijn");
    }

    $deletedAt = null;

    // Voeg het gebruikersprofiel in
    $sql = "INSERT INTO userProfiles (FKuserId, profilePicture, description, genderPreference, minAgePreference, maxAgePreference, deletedAt)
            VALUES (:FKuserId, :profilePicture, :description, :genderPreference, :minAgePreference, :maxAgePreference, :deletedAt)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':FKuserId', $userId);
    $stmt->bindParam(':profilePicture', $profilePicture, PDO::PARAM_LOB);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':genderPreference', $genderPreference);
    $stmt->bindParam(':minAgePreference', $minAgePreference);
    $stmt->bindParam(':maxAgePreference', $maxAgePreference);
    $stmt->bindParam(':deletedAt', $deletedAt, PDO::PARAM_NULL);
    $stmt->execute();

    // Haal het gegenereerde userProfileId op
    $userProfileId = $conn->lastInsertId();

// Verwerk extra foto's
    if (!empty($_FILES['extraPhotos']['name'][0])) {
        $sql = "INSERT INTO userProfileImages (FKuserProfileId, image) VALUES (:FKuserProfileId, :image)";
        $stmt = $conn->prepare($sql);

        foreach ($_FILES['extraPhotos']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['extraPhotos']['error'][$key] == 0) {
                $image = file_get_contents($tmpName);
                $stmt->bindParam(':FKuserProfileId', $userProfileId);
                $stmt->bindParam(':image', $image, PDO::PARAM_LOB);
                $stmt->execute();
            }
        }
    }

    // Tags verwerken
// Controleer of er tags geselecteerd zijn
    if (!empty($_POST['tags'])) {
        // Controleer of het aantal geselecteerde tags meer dan 5 is
        if (count($_POST['tags']) > 5) {
            throw new Exception("Je mag maximaal 5 tags selecteren.");
        }

        $sql = "INSERT INTO userProfileTags (FKuserProfileId, FKtagId) VALUES (:FKuserProfileId, :FKtagId)";
        $stmt = $conn->prepare($sql);

        foreach ($_POST['tags'] as $tagId) {
            $stmt->bindParam(':FKuserProfileId', $userProfileId);
            $stmt->bindParam(':FKtagId', $tagId);
            $stmt->execute();
        }
    }


    // Commit transactie
    $conn->commit();
    header('Location: ../index.php?page=user_profile');

} catch (Exception $e) {

    header("Location: ../index.php?page=add_profile&msg=" . urlencode($e->getMessage()));
}
?>
