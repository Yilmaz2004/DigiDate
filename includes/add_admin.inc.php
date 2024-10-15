<body class="bg-gray-100">
<div class="flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Add Admin</h2>
        <?php
        if (isset($_SESSION['error'])) { ?>
            <div role="alert" class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Fout! <?= $_SESSION['error'] ?></span>
            </div>
            <?php unset($_SESSION['error']);
        }
        ?>
        <form action="php/add_admin.php" method="POST">
            <div class="mb-4">
                <label for="firstname" class="block text-gray-700 font-bold mb-2">First Name</label>
                <input type="text" id="firstName" name="firstName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="middlename" class="block text-gray-700 font-bold mb-2">Middle Name</label>
                <input type="text" id="middleName" name="middleName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="lastname" class="block text-gray-700 font-bold mb-2">Last Name</label>
                <input type="text" id="lastName" name="lastName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4 relative">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 pr-10 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-700 cursor-pointer" onclick="togglePassword()">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.477 0 8.268 2.943 9.542 7-.4 1.426-1.128 2.726-2.08 3.786M12 19c-4.477 0-8.268-2.943-9.542-7a9.957 9.957 0 011.048-2.408"/>
                            </svg>
                        </span>
                </div>
                <p class="text-gray-500 text-sm mt-1">Minimaal 8 tekens, inclusief hoofdletters, kleine letters, cijfers en speciale tekens.</p>

            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Add Admin</button>
            </div>
        </form>
    </div>
</div>

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
</body>
