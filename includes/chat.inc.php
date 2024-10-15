<?php
require '../private/conn_digidate_examen.php';

// Check if user is logged in -Sam
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

// Fetch all matches for the current user -Sam
$sqlMatches = "SELECT up.userProfileId, up.profilePicture, u.firstName, u.middleName, u.lastName
               FROM userswipechoices usc1
               JOIN userswipechoices usc2 ON usc1.FKuserProfileId1 = usc2.FKuserProfileId2 
                                         AND usc1.FKuserProfileId2 = usc2.FKuserProfileId1
               JOIN userprofiles up ON up.userProfileId = usc1.FKuserProfileId2
               JOIN users u ON u.userId = up.FKuserId
               WHERE usc1.FKuserProfileId1 = :currentUserProfileId 
               AND usc1.FKchoice = 1 AND usc2.FKchoice = 1";
$stmtMatches = $conn->prepare($sqlMatches);
$stmtMatches->bindParam(':currentUserProfileId', $currentUserProfile['userProfileId']);
$stmtMatches->execute();
$matches = $stmtMatches->fetchAll(PDO::FETCH_ASSOC);

$selectedMatchId = isset($_GET['match']) ? $_GET['match'] : null;
$selectedMatch = null;

if ($selectedMatchId) {
    foreach ($matches as $match) {
        if ($match['userProfileId'] == $selectedMatchId) {
            $selectedMatch = $match;
            break;
        }
    }
}
?>

<div class="max-w-6xl mx-auto mt-10 p-6">
    <div class="flex space-x-4">
        <div class="w-1/3 bg-white rounded-lg shadow-md p-4">
            <h2 class="text-xl font-semibold mb-4">Jouw Matches</h2>
            <div class="space-y-4">
                <?php foreach ($matches as $match): ?>
                    <a href="?page=chat&match=<?php echo $match['userProfileId']; ?>"
                       class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition <?php echo ($selectedMatchId == $match['userProfileId']) ? 'bg-pink-100' : ''; ?>">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($match['profilePicture']); ?>"
                             alt="Profile Picture"
                             class="w-12 h-12 rounded-full object-cover">
                        <div class="ml-4">
                            <h3 class="font-semibold">
                                <?php echo htmlspecialchars($match['firstName']) . ' ' .
                                    htmlspecialchars($match['middleName']) . ' ' .
                                    htmlspecialchars($match['lastName']); ?>
                            </h3>
                        </div>
                    </a>
                <?php endforeach; ?>
                <?php if (empty($matches)): ?>
                    <p class="text-gray-500 text-center">No matches yet. Keep swiping!</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="w-2/3 bg-white rounded-lg shadow-md p-4">
            <?php if ($selectedMatch): ?>
                <!-- Chat header -->
                <div class="flex items-center pb-4 border-b">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($selectedMatch['profilePicture']); ?>"
                         alt="Profile Picture"
                         class="w-12 h-12 rounded-full object-cover">
                    <h2 class="ml-4 text-xl font-semibold">
                        <?php echo htmlspecialchars($selectedMatch['firstName']) . ' ' .
                            htmlspecialchars($selectedMatch['middleName']) . ' ' .
                            htmlspecialchars($selectedMatch['lastName']); ?>
                    </h2>
                </div>

                <div class="h-96 overflow-y-auto py-4 space-y-4" id="chat-messages">
                    <!-- Messages will be populated here by your backend -->
                </div>

                <div class="mt-4 border-t pt-4">
                    <form class="flex space-x-4" id="message-form">
                        <input type="text"
                               class="flex-1 p-2 border rounded-lg focus:outline-none focus:border-pink-500"
                               placeholder="Typ je bericht..."
                               id="message-input">
                        <button type="submit"
                                class="bg-pink-500 text-white px-6 py-2 rounded-lg hover:bg-pink-600 transition">
                            Verstuur
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="h-full flex items-center justify-center">
                    <p class="text-gray-500 text-lg">Selecteer een match om te chatten</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        const chatMessages = document.getElementById('chat-messages');
        const urlParams = new URLSearchParams(window.location.search);
        const selectedMatchId = urlParams.get('match');

        let latestMessageTimestamp = null;

        if (messageForm && selectedMatchId) {
            loadMessages();

            // Set up periodic message fetching -Sam
            setInterval(loadMessages, 500);

            messageForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const message = messageInput.value.trim();
                if (message) {
                    sendMessage(message);
                }
            });
        }

        function sendMessage(message) {
            fetch('php/chat/send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `receiverProfileId=${selectedMatchId}&message=${encodeURIComponent(message)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const messageIdentifier = `${message}-${data.sendAt}`;
                        if (!seenMessages.has(messageIdentifier)) {
                            appendMessage(message, true, data.sendAt);
                            seenMessages.add(messageIdentifier);  // Add to seenMessages after sending
                        }
                        messageInput.value = '';
                        latestMessageTimestamp = data.sendAt;
                    } else {
                        console.error('Failed to send message:', data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
        }


        const seenMessages = new Set();  // Store unique message -Sam

        function loadMessages() {
            let url = `/php/chat/fetch_messages.php?matchProfileId=${selectedMatchId}`;
            if (latestMessageTimestamp) {
                url += `&lastFetchTime=${encodeURIComponent(latestMessageTimestamp)}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            const messageIdentifier = `${msg.message}-${msg.sendAt}`;

                            if (!seenMessages.has(messageIdentifier)) {
                                appendMessage(msg.message, msg.isOurs, msg.sendAt);
                                seenMessages.add(messageIdentifier);  // Add the unique identifier to the set
                            }
                        });
                    }
                    if (data.latestTimestamp) {
                        latestMessageTimestamp = data.latestTimestamp;
                    }
                })
                .catch(error => console.error('Error:', error));
        }


        function appendMessage(message, isOurs, timestamp) {
            const messageElement = document.createElement('div');
            messageElement.classList.add('flex', isOurs ? 'justify-end' : 'justify-start', 'mb-4');

            const timestampFormatted = new Date(timestamp).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});

            messageElement.innerHTML = `
    <div class="${isOurs ? 'bg-pink-500 text-white' : 'bg-gray-200 text-gray-800'}
                rounded-lg px-4 py-2 max-w-xs">
        <div>${message}</div>
        <div class="text-xs ${isOurs ? 'text-pink-200' : 'text-gray-500'} mt-1">${timestampFormatted}</div>
    </div>
    `;

            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
</script>