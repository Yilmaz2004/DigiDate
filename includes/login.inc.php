<main class="bg-gradient-to-r from-pink-100 to-pink-200 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-lg overflow-hidden">
        <div class="px-8 py-10 sm:px-12 sm:py-14">
            <h2 class="text-center text-5xl font-bold text-gray-900 mb-8">
                Log in op je <span class="text-pink-600">Liefdesverhaal</span>
            </h2>
            <p class="text-center text-gray-600 mb-10">
                Voer je gegevens in om verder te gaan.
            </p>
            <form action="php/login.php" method="post" enctype="multipart/form-data" class="space-y-8">
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p>' . $_SESSION['error'] . '</p>
                    </div>';
                    unset($_SESSION['error']);
                }
                ?>

                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-2">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mailadres</label>
                        <input type="email" name="email" id="email" required
                               class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Wachtwoord</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                   class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
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
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm">
                        <a href="../index.php?page=forgot_password"
                           class="font-medium text-pink-600 hover:text-pink-500">
                            Wachtwoord vergeten?
                        </a>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit"
                            class="w-full flex justify-center py-4 px-6 border border-transparent rounded-full shadow-sm text-lg font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition duration-150 ease-in-out">
                        Log in
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Heb je nog geen account?
                <a href="index.php?page=register" class="font-medium text-pink-600 hover:text-pink-500">Registreer
                    nu</a>
            </p>
        </div>
    </div>
</main>

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