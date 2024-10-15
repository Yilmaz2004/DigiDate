<?php
require '../private/conn_digidate_examen.php';
// session_start(); // Assuming session is already started elsewhere

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    header('Location: ../login.php');
    exit();
}

$userId = $_SESSION['userId'];

// Fetch current user's profile ID
$sqlUserProfile = "SELECT userProfileId FROM userprofiles WHERE FKuserId = :userId";
$stmtUserProfile = $conn->prepare($sqlUserProfile);
$stmtUserProfile->bindParam(':userId', $userId);
$stmtUserProfile->execute();
$currentUserProfile = $stmtUserProfile->fetch(PDO::FETCH_ASSOC);

if (!$currentUserProfile) {
    echo "User profile not found.";
    exit();
}

$currentUserProfileId = $currentUserProfile['userProfileId'];

// Handle Like/Dislike action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['swipeChoice']) && isset($_POST['matchedProfileId'])) {
    $swipeChoice = (int)$_POST['swipeChoice'];
    $matchedProfileId = (int)$_POST['matchedProfileId'];

    if ($swipeChoice === 1) { // User clicked "Like"
        // Check if the matched profile already liked the current user
        $sqlCheckIfLiked = "SELECT * FROM userswipechoices 
                            WHERE FKuserProfileId1 = :matchedProfileId 
                            AND FKuserProfileId2 = :currentUserProfileId 
                            AND FKchoice = 1"; // 1 means "Like"
        $stmtCheckIfLiked = $conn->prepare($sqlCheckIfLiked);
        $stmtCheckIfLiked->bindParam(':matchedProfileId', $matchedProfileId, PDO::PARAM_INT);
        $stmtCheckIfLiked->bindParam(':currentUserProfileId', $currentUserProfileId, PDO::PARAM_INT);
        $stmtCheckIfLiked->execute();
        $likedByMatchedProfile = $stmtCheckIfLiked->fetch(PDO::FETCH_ASSOC);

        $sqlGetMatchedData = $conn->prepare("SELECT firstName FROM users WHERE userId = :matchedProfileId");
        $sqlGetMatchedData->execute(array('matchedProfileId' => $matchedProfileId));
        $matchedUserDetails = $sqlGetMatchedData->fetch(PDO::FETCH_ASSOC);

        if ($likedByMatchedProfile) {
            $matchMessage = "het is een match";
        }
    }

    // Insert the swipe choice into userswipechoices table
    $sqlInsertSwipe = "INSERT INTO userswipechoices (FKuserProfileId1, FKuserProfileId2, FKchoice)
                       VALUES (:currentUserProfileId, :matchedProfileId, :swipeChoice)";
    $stmtInsertSwipe = $conn->prepare($sqlInsertSwipe);
    $stmtInsertSwipe->bindParam(':currentUserProfileId', $currentUserProfileId, PDO::PARAM_INT);
    $stmtInsertSwipe->bindParam(':matchedProfileId', $matchedProfileId, PDO::PARAM_INT);
    $stmtInsertSwipe->bindParam(':swipeChoice', $swipeChoice, PDO::PARAM_INT);

    try {
        $stmtInsertSwipe->execute();
        // Redirect to refresh the page after swipe

    } catch (PDOException $e) {
        echo "Error inserting swipe choice: " . $e->getMessage();
    }
}

// Fetch the current user's date of birth and gender from the users table
$sqlUserDOB = "SELECT dob, gender FROM users WHERE userId = :userId";
$stmtUserDOB = $conn->prepare($sqlUserDOB);
$stmtUserDOB->bindParam(':userId', $userId);
$stmtUserDOB->execute();
$currentUser = $stmtUserDOB->fetch(PDO::FETCH_ASSOC);

// Calculate the age of the current user based on their date of birth
$currentAge = null;
if (!empty($currentUser['dob'])) {
    $currentAge = date_diff(date_create($currentUser['dob']), date_create('today'))->y;
}

// Fetch gender and age preferences from session
$minAge = $_SESSION['minAge'] ?? null;
$maxAge = $_SESSION['maxAge'] ?? null;
$preferredGender = $_SESSION['gender'] ?? null;

$tagFilterSQL = "";
$tagBindings = [];
if (isset($_SESSION['tags']) && !empty($_SESSION['tags'])) {
    // Prepare the SQL for tag filtering
    $selectedTags = $_SESSION['tags'];
    $placeholders = implode(',', array_map(fn($i) => ':tag' . $i, range(1, count($selectedTags)))); // Create named parameters like :tag1, :tag2, etc.
    $tagFilterSQL = " AND userProfileId IN (
                          SELECT FKuserProfileId FROM userProfileTags upt
                          INNER JOIN tags t ON upt.FKtagId = t.tagId
                          WHERE t.tagName IN ($placeholders)
                      )";

    // Bind tag values to the named parameters
    foreach ($selectedTags as $index => $tag) {
        $tagBindings[':tag' . ($index + 1)] = $tag;
    }
}

// Apply gender and age filters to the SQL query
$filterConditions = " AND up.userProfileId != :currentUserProfileId AND up.deletedAt IS NULL
                      AND up.userProfileId NOT IN (
                          SELECT FKuserProfileId2 FROM userswipechoices
                          WHERE FKuserProfileId1 = :currentUserProfileId AND (FKchoice = 1 OR FKchoice = 2)
                      )";

// Add gender filter if selected
if ($preferredGender) {
    $filterConditions .= " AND u.gender = :preferredGender";
}

// Add age filter if specified
if ($minAge && $maxAge) {
    $filterConditions .= " AND YEAR(CURDATE()) - YEAR(u.dob) BETWEEN :minAge AND :maxAge";
} elseif ($minAge) {
    $filterConditions .= " AND YEAR(CURDATE()) - YEAR(u.dob) >= :minAge";
} elseif ($maxAge) {
    $filterConditions .= " AND YEAR(CURDATE()) - YEAR(u.dob) <= :maxAge";
}

$sql = "SELECT up.*, u.gender, u.dob,u.postalCode FROM userprofiles up
        INNER JOIN users u ON up.FKuserId = u.userId
        WHERE 1=1
        $filterConditions
        $tagFilterSQL
        AND u.roleId = 1
        ORDER BY RAND() LIMIT 1";

$stmt = $conn->prepare($sql);

// Bind common parameters
$stmt->bindParam(':currentUserProfileId', $currentUserProfileId);

// Bind gender and age filters if provided
if ($preferredGender) {
    $stmt->bindParam(':preferredGender', $preferredGender);
}
if ($minAge) {
    $stmt->bindParam(':minAge', $minAge, PDO::PARAM_INT);
}
if ($maxAge) {
    $stmt->bindParam(':maxAge', $maxAge, PDO::PARAM_INT);
}

// Bind the tag filter parameters dynamically using named parameters
$bindings = array_merge([':currentUserProfileId' => $currentUserProfileId], $tagBindings);
if ($preferredGender) {
    $bindings[':preferredGender'] = $preferredGender;
}
if ($minAge) {
    $bindings[':minAge'] = $minAge;
}
if ($maxAge) {
    $bindings[':maxAge'] = $maxAge;
}

$stmt->execute($bindings);

$match = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch the tags associated with the matched profile (if a match exists)
$tags = [];
if ($match) {
    $queryTags = "SELECT t.tagName 
                  FROM userProfileTags upt 
                  INNER JOIN tags t ON upt.FKtagId = t.tagId 
                  WHERE upt.FKuserProfileId = :userProfileId AND t.enabled = 1";
    $stmtTags = $conn->prepare($queryTags);
    $stmtTags->bindParam(':userProfileId', $match['userProfileId'], PDO::PARAM_INT);
    $stmtTags->execute();
    $tags = $stmtTags->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the matched user's details (firstName, middleName, lastName)
    $sqlMatchedUserDetails = "SELECT firstName, middleName, lastName FROM users WHERE userId = :userId";
    $stmtMatchedUserDetails = $conn->prepare($sqlMatchedUserDetails);
    $stmtMatchedUserDetails->bindParam(':userId', $match['FKuserId']);
    $stmtMatchedUserDetails->execute();
    $matchedUserDetails = $stmtMatchedUserDetails->fetch(PDO::FETCH_ASSOC);
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


$getCurrentUserPostalCode = $conn->prepare("SELECT postalCode FROM users WHERE userId = :userId");
$getCurrentUserPostalCode->execute([':userId' => $userId]);
$fetchCurrentPostalCode = $getCurrentUserPostalCode->fetch(PDO::FETCH_ASSOC);

function getCoordinates($place, $apiKey)
{
    $encodedPlace = urlencode($place);
    $url = "https://api.opencagedata.com/geocode/v1/json?q=$encodedPlace&key=$apiKey";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return null;
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code != 200) {
        echo "HTTP error: $http_code";
        return null;
    }

    $data = json_decode($response, true);

    if (isset($data['results'][0]['geometry'])) {
        return $data['results'][0]['geometry'];
    } else {
        return null;
    }
}

function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // Radius of the earth in km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distance = $earthRadius * $c; // Distance in km
    return $distance;
}

$apiKey = '6e3c04ab573c48528abbc2e7ef1c4286';
$fetchCurrentPostalCodeString = is_array($fetchCurrentPostalCode) ? $fetchCurrentPostalCode['postalCode'] : $fetchCurrentPostalCode;

// Assuming $match['postalCode'] is an array, extract the postal code (modify as per your array structure)
if(!empty($match['postalCode'])){
    $matchedPostalCodeString = is_array($match['postalCode']) ? $match['postalCode']['postalCode'] : $match['postalCode'];


// Now pass the strings to getCoordinates function
    $coords1 = getCoordinates($fetchCurrentPostalCodeString, $apiKey);
    $coords2 = getCoordinates($matchedPostalCodeString, $apiKey);

    if ($coords1 && $coords2) {
        $distance = calculateDistance($coords1['lat'], $coords1['lng'], $coords2['lat'], $coords2['lng']);
        $_SESSION["distance"] = ceil($distance);
        //echo $_SESSION["distance"];
        //header('Location: ../index.php?page=swipe');
        //exit();
    } else {
        echo "Er is een fout opgetreden bij het berekenen van de afstand, probeer het opnieuw.";
    }
}


//<!-- Toast notification -->
if (!empty($matchMessage)): ?>
    <div id="match-toast"
         class="fixed bottom-5 right-5 bg-green-100 text-green-700 p-4 rounded-lg shadow-lg flex items-center space-x-4">
        <p class="text-xl font-semibold"><?php echo $matchMessage; ?></p>
        <button id="match-page-btn"
                class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition">
            Go to Match Page
        </button>
        <button id="close-toast" class="text-gray-700 hover:text-gray-900">
            &#x2715;
        </button>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('match-toast');
            toast.classList.remove('hidden'); // Remove the hidden class
        });
    </script>
<?php endif; ?>


<div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-center mb-6">Like of dislike</h1>

    <?php if ($match): ?>
        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($match['profilePicture']); ?>"
                 alt="Profile Picture" class="w-32 h-32 rounded-full border-4 border-pink-500 object-cover mb-4">
            <!-- Display matched user's name -->
            <h2 class="text-2xl font-bold text-center mb-4"><?php echo htmlspecialchars($matchedUserDetails['firstName']) . ' ' . htmlspecialchars($matchedUserDetails['middleName']) . ' ' . htmlspecialchars($matchedUserDetails['lastName']); ?></h2>
            <p class="text-lg font-semibold text-center mb-2">
                Leeftijd: <?php echo date_diff(date_create($match['dob']), date_create('today'))->y; ?></p>
            <p class="text-lg font-semibold text-center mb-2">
                Geslacht: <?php echo htmlspecialchars($match['gender']); ?></p>
            <p class="text-lg font-semibold text-center mb-2">
                Afstand: <?php echo htmlspecialchars($_SESSION['distance']); ?> km</p>

            <div class="flex flex-wrap justify-center gap-2 mt-4">
                <?php foreach ($tags as $index => $tag): ?>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo $tagColors[$index % count($tagColors)]; ?>">
                        <?php echo htmlspecialchars($tag['tagName']); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Swipe buttons -->
        <div class="flex justify-center gap-4 mt-6">
            <!-- Swipe Like -->
            <form method="POST" action="">
                <input type="hidden" name="swipeChoice" value="1"> <!-- 1 for Like -->
                <input type="hidden" name="matchedProfileId" value="<?php echo $match['userProfileId']; ?>">
                <button type="submit"
                        class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-green-600 transition">
                    Like
                </button>
            </form>

            <!-- Swipe Dislike -->
            <form method="POST" action="">
                <input type="hidden" name="swipeChoice" value="2"> <!-- 2 for Dislike -->
                <input type="hidden" name="matchedProfileId" value="<?php echo $match['userProfileId']; ?>">
                <button type="submit"
                        class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-md hover:bg-red-600 transition">
                    Dislike
                </button>
            </form>
        </div>
    <?php else: ?>
        <p class="text-xl font-semibold text-center mt-6">Er zijn momenteel geen profielen beschikbaar</p>
    <?php endif; ?>
</div>

<!-- Toast JS -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const closeToastBtn = document.getElementById('close-toast');
        const matchPageBtn = document.getElementById('match-page-btn');
        const toast = document.getElementById('match-toast');

        // Check if the toast was previously dismissed
        if (!localStorage.getItem('toastDismissed')) {
            toast.style.display = 'flex';
        }

        // Close toast on 'X' button click
        if (closeToastBtn) {
            closeToastBtn.addEventListener('click', () => {
                toast.style.display = 'none';
                localStorage.setItem('toastDismissed', 'true');  // Mark the toast as dismissed
            });
        }

        // Redirect to match page on "Go to Match Page" button click
        if (matchPageBtn) {
            matchPageBtn.addEventListener('click', () => {
                localStorage.setItem('toastDismissed', 'true');  // Mark the toast as dismissed
                window.location.href = '../index.php?page=match'; // Update with the correct URL for your match page
            });
        }
    });

    // Clear the localStorage when there's a new match (toast should show again)
    <?php if (!empty($matchMessage)): ?>
    localStorage.removeItem('toastDismissed');
    <?php endif; ?>
</script>
