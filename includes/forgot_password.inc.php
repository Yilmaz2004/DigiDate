<?php
// Start output buffering at the very beginning of your script
ob_start();

// Include necessary files
include '../private/conn_digidate_examen.php';

if (!isset($_GET['token'])) {
    ?>
    <main class="bg-pink-100 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-8 sm:p-10">
                <h2 class="text-center text-4xl font-extrabold text-gray-900 mb-8">
                    Wachtwoord <span class="text-pink-600">Vergeten</span>
                </h2>
                <form action="php/send_password_reset.php" method="post" class="space-y-6">
                    <?php
                    if (isset($_GET['error'])) {
                        echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            <p>' . $_GET['error'] . '</p>
                        </div>';
                    }
                    ?>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mailadres</label>
                        <input type="email" name="email" id="email" required
                               class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                               placeholder="Voer je e-mailadres in">
                    </div>

                    <div class="mt-8">
                        <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition duration-150 ease-in-out">
                            Verstuur Wachtwoord Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <?php
} else {
    $query = "SELECT * FROM users WHERE resetToken = :resetToken";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':resetToken', $_GET['token']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $redirect = '../index.php?page=register';
    } else {
        $expiry = new DateTime($user['resetTokenExpiresAt']);
        $currentDate = new DateTime();

        if ($expiry < $currentDate) {
            $query = "UPDATE users SET resetToken = null, resetTokenExpiresAt = null WHERE resetToken = :resetToken";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':resetToken', $_GET['token']);
            $stmt->execute();

            $redirect = '../index.php?page=register';
        } else {
            ?>
            <main class="bg-pink-100 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-6 py-8 sm:p-10">
                        <h2 class="text-center text-4xl font-extrabold text-gray-900 mb-8">
                            Wachtwoord <span class="text-pink-600">Resetten</span>
                        </h2>
                        <form action="php/reset_password.php" method="post" class="space-y-6">
                            <?php
                            if (isset($_GET['error'])) {
                                echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                                    <p>' . $_GET['error'] . '</p>
                                </div>';
                            }
                            ?>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Nieuw
                                    Wachtwoord</label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" required
                                           class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                           placeholder="Voer je nieuwe wachtwoord in">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                                          onclick="togglePasswordVisibility('password')">
                                        <svg class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15.89 9A3 3 0 1020 9m-9 0a3 3 0 105.11 2.86M12 14c-2.67 0-5.33-1-8-3 0 0 3.67-4 8-4s8 4 8 4c-2.67 2-5.33 3-8 3z"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Bevestig
                                    Nieuw Wachtwoord</label>
                                <div class="relative">
                                    <input type="password" name="confirmPassword" id="confirmPassword" required
                                           class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                           placeholder="Bevestig je nieuwe wachtwoord">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                                          onclick="togglePasswordVisibility('confirmPassword')">
                                        <svg class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15.89 9A3 3 0 1020 9m-9 0a3 3 0 105.11 2.86M12 14c-2.67 0-5.33-1-8-3 0 0 3.67-4 8-4s8 4 8 4c-2.67 2-5.33 3-8 3z"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">

                            <div class="mt-8">
                                <button type="submit"
                                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition duration-150 ease-in-out">
                                    Reset Wachtwoord
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
            <?php
        }
    }
}

// Perform redirection if necessary
if (isset($redirect)) {
    // Clear the output buffer without sending its contents
    ob_end_clean();

    // Now it's safe to send headers
    header("Location: $redirect");
    exit();
}

// If no redirection, flush the output buffer
ob_end_flush();
?>
<script>
    function togglePasswordVisibility(id) {
        const element = document.getElementById(id);
        if (element.type === 'password') {
            element.type = 'text';
        } else {
            element.type = 'password';
        }
    }
</script>