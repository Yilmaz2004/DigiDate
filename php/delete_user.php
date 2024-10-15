<?php
include '../../private/conn_digidate_examen.php';
session_start();

$userId = $_SESSION['userId'];

$rndmNumber = rand(1000, 9999);
$currentTime = date('Y-m-d H:i:s');


$deleteUser = $conn->prepare("UPDATE users SET firstName =:rndmNumber,middleName =:rndmNumber,lastName =:rndmNumber,phonenumber =:rndmNumber,email =:rndmNumber,residence =:rndmNumber, activated = 0, deletedAt = :currentTime WHERE userId= :userId");
$deleteUser->execute(array(':rndmNumber' => $rndmNumber, ':currentTime' => $currentTime, ':userId' => $userId));


$deleteSwipeChoices = $conn->prepare("DELETE userswipechoices FROM userswipechoices
       LEFT JOIN userprofiles ON userswipechoices.FKuserProfileId1 = userprofiles.userProfileId
       WHERE userprofiles.FKuserId = :userId");

$deleteSwipeChoices->execute(array(':userId' => $userId));


$deleteTest = $conn->prepare("DELETE userprofiles,userprofileimages
       FROM userprofiles
       LEFT JOIN userprofileimages ON userprofiles.userProfileId = userprofileimages.FKuserProfileId
       WHERE userprofiles.FKuserId = :userId");
$deleteTest->execute(array(':userId' => $userId));

unset($_SESSION['userId']);
header('Location: ../index.php?page=profile_deleted');
exit();