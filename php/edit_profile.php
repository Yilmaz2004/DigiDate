<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require '../../private/conn_digidate_examen.php';
    session_start();
    $userId = $_SESSION['userId'];

    if ($_POST['minAgePreference'] > $_POST['maxAgePreference']) {
        $_SESSION['error'] = 'Minimum leeftijd voorkeur mag niet hoger zijn dan de maximum leeftijd voorkeur.';
        header('Location: ../index.php?page=edit_profile');
        exit();
    }

    if ($_POST['minAgePreference'] < 18) {
        $_SESSION['error'] = 'Minimum leeftijd is 18 jaar oud.';
        header('Location: ../index.php?page=edit_profile');
        exit();
    }


    $fetchUserProfileId = $conn->prepare("SELECT * FROM userprofiles WHERE FKuserId = :userId");
    $fetchUserProfileId->execute(array('userId' => $userId));
    $fetchId = $fetchUserProfileId->fetch(PDO::FETCH_ASSOC);

    if (!empty($_FILES['extraPhotos']['name'][0])) {
        // Verwijder bestaande extra foto's
        $deletePreviousImages = $conn->prepare("DELETE FROM userprofileimages WHERE FKuserProfileId = :profileId");
        $deletePreviousImages->execute(array('profileId' => $fetchId['userProfileId']));

        foreach ($_FILES['extraPhotos']['tmp_name'] as $key => $tmpName) {
            if (is_uploaded_file($tmpName)) {
                $fileContent = file_get_contents($tmpName);
                $insertPhoto = $conn->prepare("INSERT INTO userprofileimages (FKuserProfileId, image) VALUES (:FKuserProfileId, :image)");
                $insertPhoto->execute(array(':FKuserProfileId' => $fetchId['userProfileId'], ':image' => $fileContent));
            }
        }
    }


    if (isset($_POST['tags'])) {
        $selectedTags = $_POST['tags'];

        if (count($selectedTags) > 5) {
            $_SESSION['error'] = 'Je kunt maximaal 5 tags selecteren.';
            header('Location: ../index.php?page=edit_profile');
            exit();
        }
    } else {
        $selectedTags = [];
    }

    $fetchExistingTags = $conn->prepare("SELECT FKtagId FROM userprofiletags WHERE FKuserProfileId = :FKuserProfileId");
    $fetchExistingTags->execute(array(':FKuserProfileId' => $fetchId['userProfileId']));
    $existingTags = $fetchExistingTags->fetchAll(PDO::FETCH_COLUMN, 0);

    $selectedTagIds = [];
    foreach ($selectedTags as $tagName) {
        $fetchTagId = $conn->prepare("SELECT tagId FROM tags WHERE tagName = :tagName");
        $fetchTagId->execute(array(':tagName' => $tagName));
        $tag = $fetchTagId->fetch(PDO::FETCH_ASSOC);
        $selectedTagIds[] = $tag['tagId'];
    }

    foreach ($selectedTagIds as $tagId) {
        if (!in_array($tagId, $existingTags)) {
            // Voeg de tag toe aan het profiel als deze nog niet bestaat
            $insertProfileTag = $conn->prepare("INSERT INTO userprofiletags (FKuserProfileId, FKtagId) VALUES (:FKuserProfileId, :FKtagId)");
            $insertProfileTag->execute(array(':FKuserProfileId' => $fetchId['userProfileId'], ':FKtagId' => $tagId));
        }
    }

    foreach ($existingTags as $existingTagId) {
        if (!in_array($existingTagId, $selectedTagIds)) {
            // Verwijder de tag als deze niet meer geselecteerd is
            $deleteTag = $conn->prepare("DELETE FROM userprofiletags WHERE FKuserProfileId = :FKuserProfileId AND FKtagId = :FKtagId");
            $deleteTag->execute(array(':FKuserProfileId' => $fetchId['userProfileId'], ':FKtagId' => $existingTagId));
        }
    }


    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
        $profilePicture = file_get_contents($_FILES['profilePicture']['tmp_name']);
    }

    if ($profilePicture == NULL) {
        $updateUserProfile = $conn->prepare("UPDATE userprofiles SET description = :description, genderPreference = :genderPreference , minAgePreference = :minAgePreference, maxAgePreference = :maxAgePreference WHERE FKuserId = :userId");
        $updateUserProfile->execute(array(':description' => $_POST['description'], ':genderPreference' => $_POST['genderPreference'], ':minAgePreference' => $_POST['minAgePreference'], ':maxAgePreference' => $_POST['maxAgePreference'], ':userId' => $userId));

        header('Location: ../index.php?page=user_profile');
        exit();
    } else {
        $updateUserProfile = $conn->prepare("UPDATE userprofiles SET profilePicture = :profilePicture , description = :description, genderPreference = :genderPreference , minAgePreference = :minAgePreference, maxAgePreference = :maxAgePreference WHERE FKuserId = :userId");
        $updateUserProfile->execute(array(':profilePicture' => $profilePicture, ':description' => $_POST['description'], ':genderPreference' => $_POST['genderPreference'], ':minAgePreference' => $_POST['minAgePreference'], ':maxAgePreference' => $_POST['maxAgePreference'], ':userId' => $userId));

        header('Location: ../index.php?page=user_profile');
        exit();
    }

} else {
    header('Location: ../index.php?page=edit_profile');
    exit();
}