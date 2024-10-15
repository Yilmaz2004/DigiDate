<?php
if (!isset($_SESSION['userId']) || $_SESSION['roleId'] !== 2) {
    exit('Access denied.');
}

include '../private/conn_digidate_examen.php';

?>

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Labels</h1>
    <div class="text-right mb-4">
        <button type="button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                onclick="openModal('addTagModal')">
            Label toevoegen
        </button>
    </div>
    <div class="w-full overflow-x-auto">
        <table class="w-full bg-white shadow-md rounded">
            <thead>
            <tr>
                <th class="py-3 px-4 border-b border-gray-200 text-left">Label naam</th>
                <th class="py-3 px-4 border-b border-gray-200 text-left">Status</th>
                <th class="py-3 px-4 border-b border-gray-200 text-center">Acties</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $query = "SELECT tagId, tagName, enabled FROM tags";
            $stmt = $conn->prepare($query);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) === 0) {
                echo "<tr><td colspan='3' class='py-3 px-4 text-center'>Geen labels gevonden.</td></tr>";
            } else {
                foreach ($results as $row) {
                    echo "<tr class='hover:bg-gray-100'>";
                    echo "<td class='py-3 px-4 border-b border-gray-200 text-left'>" . htmlspecialchars($row['tagName']) . "</td>";
                    echo "<td class='py-3 px-4 border-b border-gray-200 text-left'>" . ($row['enabled'] ? 'Aan' : 'Uit') . "</td>";
                    echo "<td class='py-3 px-4 border-b border-gray-200 text-center'>";
                    if ($row['enabled']) {
                        echo "<button class='bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded' onclick='openModal(\"deleteTagModal\", " . htmlspecialchars($row['tagId']) . ")'>Verwijderen</button>";
                    } else {
                        echo "<button class='bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded' onclick='openModal(\"enableTagModal\", " . htmlspecialchars($row['tagId']) . ")'>Inschakelen</button>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Tag Modal -->
<div id="addTagModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Toevoegen Labels</h3>
            <div class="mt-2 px-7 py-3">
                <form action="php/add_tag.php" method="POST">
                    <input type="text" name="tagName" placeholder="Tag Name"
                           class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <div class="items-center px-4 py-3">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600">
                            Voeg toe
                        </button>
                    </div>
                </form>
            </div>
            <div class="items-center px-4 py-3">
                <button type="button"
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600"
                        onclick="closeModal('addTagModal')">Annuleren
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Tag Modal -->
<div id="deleteTagModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Verwijderen Labels</h3>
            <div class="mt-2 px-7 py-3">
                <p>Weet je zeker dat je deze Label wilt verwijderen?</p>
                <form action="php/delete_tag.php" method="POST">
                    <input type="hidden" name="tagId" id="deleteTagId">
                    <div class="items-center px-4 py-3">
                        <button type="submit"
                                class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-600">
                            Verwijderen
                        </button>
                    </div>
                </form>
            </div>
            <div class="items-center px-4 py-3">
                <button type="button"
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600"
                        onclick="closeModal('deleteTagModal')">Annuleren
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Enable Tag Modal -->
<div id="enableTagModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Inschakelen label</h3>
            <div class="mt-2 px-7 py-3">
                <p>Weet je zeker dat je deze tag wilt inschakelen?</p>
                <form action="php/enable_tag.php" method="POST">
                    <input type="hidden" name="tagId" id="enableTagId">
                    <div class="items-center px-4 py-3">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600">
                            Inschakelen
                        </button>
                    </div>
                </form>
            </div>
            <div class="items-center px-4 py-3">
                <button type="button"
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600"
                        onclick="closeModal('enableTagModal')">Annuleren
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(modalId, tagId = null) {
        document.getElementById(modalId).classList.remove('hidden');
        if (tagId) {
            if (modalId === 'deleteTagModal') {
                document.getElementById('deleteTagId').value = tagId;
            } else if (modalId === 'enableTagModal') {
                document.getElementById('enableTagId').value = tagId;
            }
        }
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>