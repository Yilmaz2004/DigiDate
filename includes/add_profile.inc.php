<?php
require '../private/conn_digidate_examen.php';
// Haal de beschikbare tags op
$sqlTags = "SELECT tagId, tagName FROM tags WHERE enabled = 1";
$stmtTags = $conn->prepare($sqlTags);
$stmtTags->execute();
$availableTags = $stmtTags->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="max-w-lg mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">Profiel Toevoegen</h2>
    <form id="profileForm" action="php/add_profile.php" method="POST" enctype="multipart/form-data">

        <!-- Profile Picture Upload -->
        <div class="mb-6">
            <label for="profilePicture" class="block text-sm font-semibold text-gray-800">Profiel Foto</label>
            <div class="mt-2 relative">
                <input type="file" id="profilePicture" name="profilePicture" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" onchange="previewImage(event, 'profilePicturePreview')" />
                <button class="block w-full text-black rounded-lg shadow-md focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 sm:text-sm transition duration-150 ease-in-out hover:shadow-lg">
                    Bestand kiezen
                </button>
            </div>
            <div id="profilePicturePreview" class="mt-4"></div>
        </div>

        <!-- Extra Photos Upload -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-800">Extra foto's</label>
            <div class="flex space-x-4 mt-2">
                <!-- Create 5 image upload buttons with preview -->
                <template id="fileInputTemplate">
                    <div class="relative w-1/5">
                        <input type="file" name="extraPhotos[]" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" onchange="previewImage(event, this.nextElementSibling)" />
                        <button type="button" onclick="" class="mt-4 text-blue-600">Selecteer foto</button>
                        <div class="mt-2"></div> <!-- Placeholder for image preview -->
                    </div>
                </template>
                <div id="extraPhotosPreview" class="flex space-x-4"></div>
            </div>
            <div id="photoNotification" class="mt-2 text-red-500 text-sm hidden">Je kunt maximaal 5 extra foto's uploaden.</div> <!-- Notification text -->
        </div>


        <!-- Add a button to generate more file input fields dynamically -->
        <button type="button" onclick="addMoreFiles()" class="mt-4 text-blue-600">Voeg toe</button>

        <!-- Other Profile Fields -->
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Beschrijving</label>
            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
        </div>

        <!-- Gender and Age Preferences -->
        <div class="mb-4">
            <label for="genderPreference" class="block text-sm font-medium text-gray-700">Geslachts voorkeur</label>
            <select id="genderPreference" name="genderPreference" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="minAgePreference" class="block text-sm font-medium text-gray-700">Minimale Leeftijd</label>
            <input type="number" id="minAgePreference" name="minAgePreference" min="18" max="99" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <div class="mb-4">
            <label for="maxAgePreference" class="block text-sm font-medium text-gray-700">Maximale Leeftijd</label>
            <input type="number" id="maxAgePreference" name="maxAgePreference" min="18" max="99" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <!-- Tags -->
        <div class="mb-6">
            <label for="tags" class="block text-sm font-semibold text-gray-800">Selecteer Labels</label>
            <div id="tags" class="mt-2 grid grid-cols-4 gap-2">
                <?php foreach ($availableTags as $tag): ?>
                    <div class="flex items-center p-3 border border-gray-300 rounded-lg shadow-md transition duration-150 ease-in-out hover:bg-indigo-50">
                        <input type="checkbox" id="tag-<?= $tag['tagId']; ?>" name="tags[]" value="<?= $tag['tagId']; ?>" class="mr-2 rounded border-gray-300 focus:ring-indigo-500">
                        <label for="tag-<?= $tag['tagId']; ?>" class="text-sm text-gray-700"><?= htmlspecialchars($tag['tagName']); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Error Message -->
        <div id="msg" class="hidden text-red-500"></div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Profiel toevoegen</button>
        </div>
    </form>
</div>

<script>
    // Track the number of extra photo inputs
    let extraPhotoCount = 0;
    const maxPhotos = 5;

    // Preview selected images before upload
    function previewImage(event, previewElement) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewElement.innerHTML = `<img src="${e.target.result}" alt="Image Preview" class="rounded-lg" width="100">`;
            };
            reader.readAsDataURL(file);
        }
    }

    // Add more file inputs dynamically, but limit to maxPhotos
    function addMoreFiles() {
        const notification = document.getElementById('photoNotification');

        if (extraPhotoCount < maxPhotos) {
            const template = document.getElementById('fileInputTemplate').content.cloneNode(true);
            document.getElementById('extraPhotosPreview').appendChild(template);
            extraPhotoCount++; // Increment count
            notification.classList.add('hidden'); // Hide notification if under the limit
        } else {
            notification.classList.remove('hidden'); // Show notification when limit is reached
        }
    }

    // Display error message if present
    const queryParams = new URL(window.location.href).searchParams;
    const msg = document.getElementById("msg");
    if (queryParams.get("msg") !== null) {
        msg.textContent = queryParams.get("msg");
        msg.classList.remove("hidden");
    }

</script>

