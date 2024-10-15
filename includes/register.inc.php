<main class="bg-pink-100 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="px-6 py-8 sm:p-10">
            <h2 class="text-center text-4xl font-extrabold text-gray-900 mb-8">
                Start je <span class="text-pink-600">Liefdesverhaal</span>
            </h2>
            <form action="php/register.php" method="post" enctype="multipart/form-data" class="space-y-6">
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p>' . $_SESSION['error'] . '</p>
                    </div>';
                    unset($_SESSION['error']);
                }
                ?>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
                    <div>
                        <label for="firstname" class="block text-sm font-medium text-gray-700">Voornaam</label>
                        <input type="text" name="firstName" id="firstname" value="<?php if (isset($_SESSION['data'])) {
                            echo $_SESSION['data']['firstName'];
                        } ?>" required
                               class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="middlename" class="block text-sm font-medium text-gray-700">Tussenvoegsel</label>
                        <input type="text" name="middleName" id="middlename"
                               value="<?php if (isset($_SESSION['data'])) {
                                   echo $_SESSION['data']['middleName'];
                               } ?>"
                               class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="lastname" class="block text-sm font-medium text-gray-700">Achternaam</label>
                        <input type="text" name="lastName" id="lastname" value="<?php if (isset($_SESSION['data'])) {
                            echo $_SESSION['data']['lastName'];
                        } ?>" required
                               class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mailadres</label>
                        <input type="email" name="email" id="email" value="<?php if (isset($_SESSION['data'])) {
                            echo $_SESSION['data']['email'];
                        } ?>" required
                               class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Telefoonnummer</label>
                        <input type="number" name="phonenumber" id="phone" value="<?php if (isset($_SESSION['data'])) {
                            echo $_SESSION['data']['phonenumber'];
                        } ?>" required
                               class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">

                    </div>
                    <div class="relative sm:col-span-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">Wachtwoord</label>
                        <input type="password" name="password" id="password" required
                               class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md pr-10">
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-700 cursor-pointer"
                              onclick="togglePassword()">
        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.522 5 12 5c4.477 0 8.268 2.943 9.542 7-.4 1.426-1.128 2.726-2.08 3.786M12 19c-4.477 0-8.268-2.943-9.542-7a9.957 9.957 0 011.048-2.408"/>
        </svg>
    </span>
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">Woonplaats</label>
                        <input type="text" name="residence" id="city" value="<?php if (isset($_SESSION['data'])) {
                            echo $_SESSION['data']['residence'];
                        } ?>" required
                               class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="postcode" class="block text-sm font-medium text-gray-700">Postcode</label>
                        <input type="text" name="postalCode" id="postcode" value="<?php if (isset($_SESSION['data'])) {
                            echo $_SESSION['data']['postalCode'];
                        } ?>" required
                               class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="study" class="block text-sm font-medium text-gray-700">Studie</label>
                        <input type="text" name="study" id="study" value="<?php if (isset($_SESSION['data'])) {
                            echo $_SESSION['data']['study'];
                        } ?>"
                               class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="dob" class="block text-sm font-medium text-gray-700">Geboortedatum</label>
                        <input type="date" name="dob" id="dob" value="<?php if (isset($_SESSION['data'])) {
                            echo $_SESSION['data']['dob'];
                        } ?>" required
                               class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Geslacht</label>
                        <select id="gender" name="gender" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm">
                            <option value="" disabled selected>Selecteer</option>
                            <option value="male" <?php if (isset($_SESSION['data']) && $_SESSION['data']['gender'] == 'male') echo 'selected'; ?>>
                                Man
                            </option>
                            <option value="female" <?php if (isset($_SESSION['data']) && $_SESSION['data']['gender'] == 'female') echo 'selected'; ?>>
                                Vrouw
                            </option>
                        </select>
                    </div>
                    <?php unset($_SESSION['data']); ?>

                    <!--                    <div>-->
                    <!--                        <label for="preferredGender" class="block text-sm font-medium text-gray-700">Voorkeur-->
                    <!--                            geslacht</label>-->
                    <!--                        <select id="preferredGender" name="preferredGender" required-->
                    <!--                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm">-->
                    <!--                            <option value="" disabled selected>Selecteer</option>-->
                    <!--                            <option value="male">Man</option>-->
                    <!--                            <option value="female">Vrouw</option>-->
                    <!--                            <option value="both">Beide</option>-->
                    <!--                        </select>-->
                    <!--                    </div>-->
                </div>


                <!--                <div>-->
                <!--                    <label for="pfp" class="block text-sm font-medium text-gray-700">PFP</label>-->
                <!--                    <input type="file" name="pfp" id="pfp"-->
                <!--                           class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">-->
                <!--                </div>-->


                <!--                <div class="mt-6">-->
                <!--                    <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profielfoto</label>-->
                <!--                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">-->
                <!--                        <div class="space-y-1 text-center">-->
                <!--                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"-->
                <!--                                 viewBox="0 0 48 48" aria-hidden="true">-->
                <!--                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"-->
                <!--                                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>-->
                <!--                            </svg>-->
                <!--                            <div class="flex text-sm text-gray-600">-->
                <!--                                <label for="file-upload"-->
                <!--                                       class="relative cursor-pointer bg-white rounded-md font-medium text-pink-600 hover:text-pink-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-pink-500">-->
                <!--                                    <span>Upload een foto</span>-->
                <!--                                    <input id="file-upload" name="pfp" type="file" class="sr-only">-->
                <!--                                </label>-->
                <!--                                <p class="pl-1">of sleep en zet neer</p>-->
                <!--                            </div>-->
                <!--                            <p class="text-xs text-gray-500">PNG, JPG, GIF tot 10MB</p>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->

                <div class="mt-8">
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition duration-150 ease-in-out">
                        Start je liefdesverhaal
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var eyeIcon = document.getElementById("eyeIcon");
        if (passwordField.type === "password") {
            passwordField.type = "text";
            // Change the eye icon to "eye open"
            eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.477 0 8.268 2.943 9.542 7-.4 1.426-1.128 2.726-2.08 3.786M12 19c-4.477 0-8.268-2.943-9.542-7a9.957 9.957 0 011.048-2.408"/>';
        } else {
            passwordField.type = "password";
            // Change the eye icon to "eye closed"
            eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.97 10.97 0 0112 19c-4.477 0-8.268-2.943-9.542-7A10.978 10.978 0 013.365 7.26M9.88 15.828A3.001 3.001 0 0112 12m0 0a3 3 0 01-1.88-5.828"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>';
        }
    }
</script>