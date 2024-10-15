<?php
include '../private/conn_digidate_examen.php';
$userId = $_SESSION['userId'];

$fetchTags = $conn->prepare("SELECT * FROM tags");
$fetchTags->execute();
$tags = $fetchTags->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4">Profielen Filteren</h2>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p>' . $_SESSION['error'] . '</p>
                    </div>';
        unset($_SESSION['error']);
    }
    ?>
    <form action="php/filter.php" method="post">

        <!-- Bestaande Tags Filter -->
        <div class="mb-4">
            <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
            <?php foreach ($tags as $tag) { ?>
                <div class="flex items-center">
                    <input type="checkbox" id="tag_<?= htmlspecialchars($tag['tagName']) ?>" name="tags[]"
                           value="<?= htmlspecialchars($tag['tagName']) ?>"
                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="tag_<?= htmlspecialchars($tag['tagName']) ?>"
                           class="ml-2 block text-sm text-gray-700">
                        <?= htmlspecialchars($tag['tagName']) ?>
                    </label>
                </div>
            <?php } ?>
        </div>

        <!-- Age Filter -->
        <div class="mb-4">
            <label for="minAge" class="block text-sm font-medium text-gray-700">Minimale Leeftijd</label>
            <input type="number" id="minAge" name="minAge"
                   class="block w-full p-2 pl-10 text-sm text-gray-700 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="maxAge" class="block text-sm font-medium text-gray-700">Maximale Leeftijd</label>
            <input type="number" id="maxAge" name="maxAge"
                   class="block w-full p-2 pl-10 text-sm text-gray-700 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- Gender Filter -->
        <div class="mb-4">
            <label for="gender" class="block text-sm font-medium text-gray-700">Geslacht</label>
            <select id="gender" name="gender"
                    class="block w-full p-2 pl-10 text-sm text-gray-700 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Alle</option>
                <option value="Male">Man</option>
                <option value="Female">Vrouw</option>
            </select>
        </div>

        <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Filters Toepassen
        </button>
    </form>
</div>