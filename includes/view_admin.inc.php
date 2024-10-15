<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Admin gebruikers</h1>
    <div class="text-right mb-4">
        <button type="button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            <a href="index.php?page=add_admin" class="text-white no-underline">Toevoegen admin</a>
        </button>
    </div>
    <div class="w-full overflow-x-auto">
        <table class="w-full bg-white shadow-md rounded">
            <thead class="bg-gradient-to-r from-pink-500 to-purple-600 shadow-lg">
            <tr>
                <th class="py-3 px-4 uppercase font-semibold text-sm w-1/5 text-left">Voornaam</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm w-1/5 text-left">Tussenvoegsel</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm w-1/5 text-left">Achternaam</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm w-1/5 text-left">Email</th>
                <th class="py-3 px-4 uppercase font-semibold text-sm w-1/5 text-left">Acties</th>
            </tr>
            </thead>
            <tbody>
            <!-- Include PHP script here to populate table rows -->
            <?php include 'php/view_admin.php'; ?>
            </tbody>
        </table>
    </div>
</div>
