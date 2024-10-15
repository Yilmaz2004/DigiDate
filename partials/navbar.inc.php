<?php
$isLoggedIn = isset($_SESSION['userId']);

if (isset($_SESSION['roleId'])) {
    $isAdmin = $_SESSION['roleId'] == 2;
} else {
    $isAdmin = false;
}


$adminNavItems = [
    'Beheer Labels' => ['url' => 'index.php?page=view_tags', 'method' => 'get'],
    'Beheer Admins' => ['url' => 'index.php?page=view_admin', 'method' => 'get'],
];

$loggedInUserNavItems = [
    'Profiel' => ['url' => 'index.php?page=user_profile', 'method' => 'get'],
    'Like & Dislike' => ['url' => 'index.php?page=filter', 'method' => 'get'],
    'Matches' => ['url' => 'index.php?page=match', 'method' => 'get'],
];

$notLoggedInNavItems = [
    'Voorpagina' => ['url' => 'index.php?page=home', 'method' => 'get'],
];

if ($isAdmin) {
    $navItems = $adminNavItems;
} elseif ($isLoggedIn) {
    require '../private/conn_digidate_examen.php';
    $checkUserProfile = $conn->prepare("SELECT * FROM userprofiles WHERE FKuserId = :FKuserId");
    $checkUserProfile->execute(array(':FKuserId' => $_SESSION['userId']));

    if ($checkUserProfile->rowCount() == 0) {
        $navItems = [];
    } else {
        $navItems = $loggedInUserNavItems;
    }
} else {
    $navItems = $notLoggedInNavItems;
}

$currentPage = $_GET['page'] ?? 'home';
?>

<nav class="bg-gradient-to-r from-pink-500 to-purple-600 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="index.php?page=home" class="flex items-center">
                    <svg class="h-8 w-8 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span class="text-2xl font-bold text-white">DigiDate</span>
                </a>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <?php foreach ($navItems as $item => $details): ?>
                        <a href="<?php echo $details['url']; ?>"
                           class="<?php echo strpos($details['url'], $currentPage) !== false ? 'bg-white bg-opacity-20 text-white' : 'text-white hover:bg-white hover:bg-opacity-20'; ?> px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            <?php echo $item; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <?php if ($isLoggedIn): ?>
                    <form method="post" action="php/logout.php" class="hidden sm:block">
                        <button type="submit"
                                class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            Uitloggen
                        </button>
                    </form>
                <?php else: ?>
                    <a href="index.php?page=login"
                       class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Inloggen
                    </a>
                    <a href="index.php?page=register"
                       class="bg-white text-purple-600 hover:bg-purple-100 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Registreren
                    </a>
                <?php endif; ?>

                <div class="sm:hidden">
                    <button id="mobile-menu-button" aria-label="Open menu"
                            class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-white hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="mobile-menu" class="sm:hidden hidden bg-white">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <?php foreach ($navItems as $item => $details): ?>
                <a href="<?php echo $details['url']; ?>"
                   class="<?php echo strpos($details['url'], $currentPage) !== false ? 'bg-purple-100 text-purple-600' : 'text-purple-600 hover:bg-purple-100'; ?> block px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out">
                    <?php echo $item; ?>
                </a>
            <?php endforeach; ?>

            <?php if ($isLoggedIn): ?>
                <form method="post" action="php/logout.php">
                    <button type="submit"
                            class="w-full text-left text-purple-600 hover:bg-purple-100 px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out">
                        Logout
                    </button>
                </form>
            <?php else: ?>
                <a href="index.php?page=login"
                   class="block text-purple-600 hover:bg-purple-100 px-3 py-2 rounded-md text-base font-medium transition duration-150 ease-in-out">
                    Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-button').addEventListener('click', function () {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
        this.setAttribute('aria-expanded', mobileMenu.classList.contains('hidden') ? 'false' : 'true');
    });
</script>