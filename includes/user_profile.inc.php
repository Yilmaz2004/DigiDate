<?php
require '../private/conn_digidate_examen.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    exit();
}

$userId = $_SESSION['userId'];

// Query to fetch profile data and all profile pictures associated with the same FKuserProfileId
$query = "SELECT u.firstName, u.middleName, u.lastName, up.description, up.profilePicture, up.genderPreference, 
                 up.minAgePreference, up.maxAgePreference, up.userProfileId, upi.image 
          FROM userProfiles up
          LEFT JOIN users u ON up.FKuserId = u.userId
          LEFT JOIN userProfileImages upi ON up.userProfileId = upi.FKuserProfileId
          WHERE up.FKuserId = :userId AND up.deletedAt IS NULL";
$stmt = $conn->prepare($query);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();

// Fetch all rows (since there can be multiple images)
$userProfile = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$userProfile) {
    echo "Geen profiel gevonden.";
    exit();
}

// Extract common profile data from the first row
$profileData = [
    'firstName' => $userProfile[0]['firstName'],
    'middleName' => $userProfile[0]['middleName'],
    'lastName' => $userProfile[0]['lastName'],
    'description' => $userProfile[0]['description'],
    'profilePicture' => base64_encode($userProfile[0]['profilePicture']),
    'genderPreference' => $userProfile[0]['genderPreference'],
    'minAgePreference' => $userProfile[0]['minAgePreference'],
    'maxAgePreference' => $userProfile[0]['maxAgePreference'],
    'images' => [],
    'userProfileId' => $userProfile[0]['userProfileId'] // Save the userProfileId for tags query
];

// Collect all images from the fetched results
foreach ($userProfile as $row) {
    if ($row['image']) {
        $profileData['images'][] = base64_encode($row['image']);
    }
}

// Fetch the tags associated with the user profile using userProfileId
$query = "SELECT t.tagName 
          FROM userProfileTags upt 
          INNER JOIN tags t ON upt.FKtagId = t.tagId 
          WHERE upt.FKuserProfileId = :userProfileId AND t.enabled = 1"; // Filter for enabled tags
$stmt = $conn->prepare($query);
$stmt->bindParam(':userProfileId', $profileData['userProfileId'], PDO::PARAM_INT); // Use userProfileId
$stmt->execute();

$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Predefined tag colors (cycle through these colors)
$tagColors = [
    'bg-blue-200 text-blue-800',
    'bg-green-200 text-green-800',
    'bg-red-200 text-red-800',
    'bg-yellow-200 text-yellow-800',
    'bg-purple-200 text-purple-800',
    'bg-pink-200 text-pink-800'
];
?>

<div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">

    <!-- Profile picture -->
    <div class="flex justify-center">
        <img class="w-32 h-32 rounded-full border-4 border-pink-500 object-cover"
             src="data:image/jpeg;base64,<?= $profileData['profilePicture'] ?>" alt="Profiel Foto">
    </div>

    <!-- Name and basic info -->
    <div class="text-center mt-4">
        <h2 class="text-2xl font-bold text-gray-800">
            <?= $profileData['firstName'] ?> <?= $profileData['middleName'] ?> <?= $profileData['lastName'] ?>
        </h2>

        <p class="text-gray-600">Geslachts Voorkeur: <?= $profileData['genderPreference'] ?> |
            Leeftijds voorkeur: <?= $profileData['minAgePreference'] ?>
            - <?= $profileData['maxAgePreference'] ?></p>
    </div>

    <!-- Short bio -->
    <div class="mt-4 text-center">
        <p class="text-gray-700">Beschrijving: <?= $profileData['description'] ?></p>
    </div>

    <!-- Tags displayed as colorful badges -->
    <div class="mt-4">
        <h3 class="text-center text-xl font-semibold text-gray-800">Labels</h3>
        <div class="flex justify-center flex-wrap gap-2 mt-6">
            <?php foreach ($tags as $index => $tag):
                $colorClass = $tagColors[$index % count($tagColors)]; // Rotate through colors
                ?>
                <span class="inline-block px-4 py-2 rounded-full text-sm font-medium <?= $colorClass ?> shadow-md">
                    <?= $tag['tagName'] ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- All extra images in a 5-column grid with clickable popup -->
    <?php if (!empty($profileData['images'])): ?>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-5 gap-4">
            <?php foreach ($profileData['images'] as $index => $image): ?>
                <div class="text-center">
                    <img class="w-40 h-40 object-cover mx-auto cursor-pointer"
                         src="data:image/jpeg;base64,<?= $image ?>" alt="Extra afbeelding"
                         onclick="openPopup(<?= $index ?>)">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>



    <!-- Popup to display larger images -->
    <div id="imagePopup" class="fixed inset-0 hidden bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="relative bg-white p-4 rounded-lg shadow-lg max-w-lg mx-auto">
            <span class="absolute top-2 right-2 text-black text-2xl cursor-pointer"
                  onclick="closePopup()">&times;</span>
            <img id="popupImage" class="w-full h-auto object-cover rounded-lg">
        </div>
    </div>

    <!-- Actions: buttons to chat or like -->
    <div class="mt-8 flex justify-center space-x-4">
        <button class="bg-red-600 text-white font-bold py-3 px-6 hover:bg-red-700 transition transform hover:scale-105 rounded-full"
                onclick=" if(confirm('Weet u zeker dat u uw profiel wilt verwijderen?'))window.location.href='php/delete_user.php'">
            Profiel Verwijderen
        </button>
        <button class="bg-blue-600 text-white font-bold py-3 px-6 hover:bg-blue-700 transition transform hover:scale-105 rounded-full"
                onclick="window.location.href='index.php?page=edit_profile'">Profiel Aanpassen
        </button>
    </div>
</div>

<script>
    // Function to open the popup and display the clicked image
    function openPopup(imageIndex) {
        const images = <?= json_encode($profileData['images']) ?>;
        const popup = document.getElementById('imagePopup');
        const popupImage = document.getElementById('popupImage');

        popupImage.src = 'data:image/jpeg;base64,' + images[imageIndex];
        popup.classList.remove('hidden');
    }

    // Function to close the popup
    function closePopup() {
        const popup = document.getElementById('imagePopup');
        popup.classList.add('hidden');
    }
</script>
