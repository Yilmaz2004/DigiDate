<?php
$randomMessages = [
    "Het lijkt erop dat je perfecte match op een andere pagina is!",
    "Oeps! Deze date heeft je laten zitten.",
    "Liefde op het eerste gezicht? Niet op deze pagina!",
    "404: Romantiek niet gevonden op deze pagina.",
    "Deze pagina is niet beschikbaar voor een date.",
    "Het lijkt erop dat deze pagina je niet leuk vindt.",
    "Deze pagina is niet jouw type."
];
$message = $randomMessages[array_rand($randomMessages)];

if (isset($_SESSION['error'])) {
    ?>


    <div class="bg-gradient-to-r from-pink-300 via-purple-300 to-indigo-400 min-h-screen flex items-center justify-center relative">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-pink-300 via-purple-300 to-indigo-400 opacity-75"></div>

        <div class="relative z-10 text-center bg-white bg-opacity-70 p-10 rounded-lg shadow-lg max-w-lg mx-auto">
            <h1 class="text-8xl font-extrabold text-pink-500 mb-4"><?= $_SESSION['error'] ?></h1>
            <p class="text-lg text-gray-600 mb-6">
                Stuur opnieuw een verzoek. </p>
        </div>
    </div>
    <?php unset($_SESSION['error']);
} else { ?>

    <div class="bg-gradient-to-r from-pink-300 via-purple-300 to-indigo-400 min-h-screen flex items-center justify-center relative">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-pink-300 via-purple-300 to-indigo-400 opacity-75"></div>

        <div class="relative z-10 text-center bg-white bg-opacity-70 p-10 rounded-lg shadow-lg max-w-lg mx-auto">
            <h1 class="text-8xl font-extrabold text-pink-500 mb-4">404</h1>
            <p class="text-2xl text-gray-800 mb-6 font-semibold"><?php echo htmlspecialchars($message); ?></p>

            <p class="text-lg text-gray-600 mb-6">
                De pagina die je probeert te bereiken bestaat niet of is verplaatst, laten we samen op zoek gaan naar
                een andere match!
            </p>

            <a href="/"
               class="inline-block bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 px-8 rounded-lg transition duration-300 ease-in-out shadow-lg">
                Terug naar de Homepagina
            </a>
        </div>
    </div>

<?php } ?>


