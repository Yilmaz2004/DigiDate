<?php
include '../private/conn_digidate_examen.php';

$userId = $_SESSION['userId'];

$fetchProfile = $conn->prepare("SELECT * FROM userprofiles WHERE FKuserId = :userId");
$fetchProfile->execute(array(':userId' => $userId));
$userProfile = $fetchProfile->fetch(PDO::FETCH_ASSOC);

$fetchTags = $conn->prepare("SELECT tagId, tagName FROM tags WHERE enabled = 1");
$fetchTags->execute();
$tags = $fetchTags->fetchAll(PDO::FETCH_ASSOC);

$fetchSelectedTags = $conn->prepare("SELECT FKtagId FROM userprofiletags WHERE FKuserProfileId = :FKuserProfileId");
$fetchSelectedTags->execute(array(':FKuserProfileId' => $userProfile['userProfileId']));
$selectedTags = $fetchSelectedTags->fetchAll(PDO::FETCH_COLUMN, 0);
?>

<div class="max-w-lg mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">Profiel Bewerken</h2>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p>' . $_SESSION['error'] . '</p>
                    </div>';
        unset($_SESSION['error']);
    }
    ?>
    <form action="php/edit_profile.php" method="POST" enctype="multipart/form-data">

        <div class="mb-4">
            <label for="profilePicture" class="block text-sm font-medium text-gray-700">Profielfoto</label>
            <input type="file" id="profilePicture" name="profilePicture"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            <p class="text-sm text-gray-500 mt-1">Laat dit veld leeg om de huidige profielfoto te behouden.</p>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Extra Foto's</label>
            <div class="flex space-x-2 mt-2">
                <input type="file" id="extraPhoto1" name="extraPhotos[]"
                       class="block w-1/5 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <input type="file" id="extraPhoto2" name="extraPhotos[]"
                       class="block w-1/5 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <input type="file" id="extraPhoto3" name="extraPhotos[]"
                       class="block w-1/5 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <input type="file" id="extraPhoto4" name="extraPhotos[]"
                       class="block w-1/5 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <input type="file" id="extraPhoto5" name="extraPhotos[]"
                       class="block w-1/5 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <p class="text-sm text-gray-500 mt-1">Laat dit veld leeg om de huidige extra foto's te behouden.</p>
        </div>


        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Beschrijving</label>
            <textarea id="description" name="description" rows="3" maxlength="100"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"><?= htmlspecialchars($userProfile['description']) ?></textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Kies Tags</label>
            <div class="mt-2 space-y-2">
                <?php foreach ($tags as $tag): ?>
                    <?php
                    $isChecked = in_array($tag['tagId'], $selectedTags) ? 'checked' : '';
                    ?>
                    <div class="flex items-center">
                        <input type="checkbox" id="tag_<?= htmlspecialchars($tag['tagName']) ?>" name="tags[]"
                               value="<?= htmlspecialchars($tag['tagName']) ?>"
                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" <?= $isChecked ?>>
                        <label for="tag_<?= htmlspecialchars($tag['tagName']) ?>"
                               class="ml-2 block text-sm text-gray-700">
                            <?= htmlspecialchars($tag['tagName']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mb-4">
            <label for="genderPreference" class="block text-sm font-medium text-gray-700">Voorkeur Geslacht</label>
            <select id="genderPreference" name="genderPreference"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="male" <?= $userProfile['genderPreference'] == 'male' ? 'selected' : '' ?>>Man</option>
                <option value="female" <?= $userProfile['genderPreference'] == 'female' ? 'selected' : '' ?>>Vrouw
                </option>
                <option value="other" <?= $userProfile['genderPreference'] == 'other' ? 'selected' : '' ?>>Anders
                </option>
            </select>
        </div>

        <div class="mb-4">
            <label for="minAgePreference" class="block text-sm font-medium text-gray-700">Minimale Leeftijd
                Voorkeur</label>
            <input type="number" value="<?= $userProfile['minAgePreference'] ?>" id="minAgePreference"
                   name="minAgePreference" min="18" max="99"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <div class="mb-4">
            <label for="maxAgePreference" class="block text-sm font-medium text-gray-700">Maximale Leeftijd
                Voorkeur</label>
            <input type="number" value="<?= $userProfile['maxAgePreference'] ?>" id="maxAgePreference"
                   name="maxAgePreference" min="18" max="99"
                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <div id="msg" class="hidden flex text-red-500"></div>

        <div class="flex justify-end">
            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Wijzigingen Opslaan
            </button>
        </div>
    </form>
</div>

<script>
    const queryParams = new URL(window.location.href).searchParams;
    const msg = document.getElementById("msg");

    if (queryParams.get("msg") !== null) {
        msg.innerHTML = queryParams.get("msg");
        msg.classList.toggle("hidden");
    }
</script>
