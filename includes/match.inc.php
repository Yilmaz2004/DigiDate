<?php
require '../private/conn_digidate_examen.php';

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    exit();
}

$userId = $_SESSION['userId'];

// Query to fetch profiles where both users liked each other and exclude the logged-in user's profile
$query = "SELECT u.firstName, u.middleName, u.lastName, up.description, up.profilePicture, 
                 up.genderPreference, up.minAgePreference, up.maxAgePreference, up.userProfileId
          FROM userSwipeChoices usc1
          JOIN userSwipeChoices usc2 ON usc1.FKuserProfileId2 = usc2.FKuserProfileId1 
          AND usc1.FKuserProfileId1 = usc2.FKuserProfileId2
          JOIN userProfiles up ON up.userProfileId = usc1.FKuserProfileId2
          JOIN users u ON up.FKuserId = u.userId
          WHERE usc1.FKchoice = 1 AND usc2.FKchoice = 1
          AND up.FKuserId != :userId"; // Exclude the logged-in user's profile

$stmt = $conn->prepare($query);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();

// Fetch all liked profiles
$matchedProfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$matchedProfiles) {
    echo "Geen overeenkomende profielen gevonden.";
    exit();
}

// Predefined tag colors (cycle through these colors)
$tagColors = [
    'bg-blue-200 text-blue-800',
    'bg-green-200 text-green-800',
    'bg-red-200 text-red-800',
    'bg-yellow-200 text-yellow-800',
    'bg-purple-200 text-purple-800',
    'bg-pink-200 text-pink-800'
];

// Track displayed user profile IDs
$displayedProfiles = [];
?>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 max-w-7xl mx-auto mt-12 px-4">
    <?php foreach ($matchedProfiles as $profile): ?>
        <?php
        if (in_array($profile['userProfileId'], $displayedProfiles)) {
            continue;
        }
        $displayedProfiles[] = $profile['userProfileId'];

        // Fetch tags
        $queryTags = "SELECT t.tagName 
                      FROM userProfileTags upt 
                      INNER JOIN tags t ON upt.FKtagId = t.tagId 
                      WHERE upt.FKuserProfileId = :userProfileId AND t.enabled = 1";
        $stmtTags = $conn->prepare($queryTags);
        $stmtTags->bindParam(':userProfileId', $profile['userProfileId'], PDO::PARAM_INT);
        $stmtTags->execute();
        $tags = $stmtTags->fetchAll(PDO::FETCH_ASSOC);

        // Profile URL
        $profileUrl = "../index.php?page=chat&" . $profile['userProfileId'];
        ?>
        <div class="bg-white rounded-xl shadow-lg transform transition-transform hover:scale-105 hover:shadow-xl p-6">
            <a href="<?= htmlspecialchars($profileUrl) ?>" class="block text-center">
                <div class="flex justify-center">
                    <img class="w-32 h-32 rounded-full border-4 border-pink-500 object-cover"
                         src="data:image/jpeg;base64,<?= base64_encode($profile['profilePicture']) ?>" alt="Profiel Foto">
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mt-4">
                    <?= htmlspecialchars($profile['firstName']) ?> <?= htmlspecialchars($profile['middleName']) ?> <?= htmlspecialchars($profile['lastName']) ?>
                </h2>
                <p class="text-gray-600 mt-2">Geslachts Voorkeur: <?= htmlspecialchars($profile['genderPreference']) ?> | Leeftijds voorkeur: <?= htmlspecialchars($profile['minAgePreference']) ?> - <?= htmlspecialchars($profile['maxAgePreference']) ?></p>
                <p class="text-gray-700 mt-2"><?= htmlspecialchars($profile['description']) ?></p>
            </a>

            <!-- Tags -->
            <div class="mt-4">
                <h3 class="text-center text-xl font-semibold text-gray-800">Labels</h3>
                <div class="flex justify-center flex-wrap gap-2 mt-2">
                    <?php foreach ($tags as $index => $tag):
                        $colorClass = $tagColors[$index % count($tagColors)]; ?>
                        <span class="inline-block px-4 py-2 rounded-full text-sm font-medium <?= $colorClass ?> shadow-md">
                            <?= htmlspecialchars($tag['tagName']) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Unmatch Button -->
            <form method="POST" action="php/unmatch.php" class="mt-4 text-center">
                <input type="hidden" name="profileIdToUnmatch" value="<?= htmlspecialchars($profile['userProfileId']) ?>">
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50"
                        onclick="return confirm('Weet u zeker dat u met de geselecteerde user wilt ontmatchen?')">
                    Ontmatch
                </button>
            </form>


        </div>
    <?php endforeach; ?>
</div>

