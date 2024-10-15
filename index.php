<?php
ob_start(); // Output buffering | Dit zorgt ervoor dat de output niet direct naar de browser wordt gestuurd zodat we nog headers kunnen aanpassen
session_start();
$page = $_GET['page'] ?? 'home';

// A quick check to see if the page is in the includes folder
// this prevents directory traversal and avoids having to manually add each page to the array
// Also makes sure to fall back to 404 if the page is not found
if (!in_array($page, array_map(function ($p) {
    $filename = pathinfo($p, PATHINFO_FILENAME);
    return str_replace('.inc', '', $filename);
}, scandir("includes")))) {
    $page = '404';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo ucfirst($page); ?> | DigiDate</title>
    <link rel="stylesheet" href="css/output.css">

</head>

<body>
        <?php
            include "partials/navbar.inc.php";
            include "includes/$page.inc.php";
        ob_end_flush();
        ?>
    </body>
</html>
