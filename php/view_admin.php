<?php

if (!isset($_SESSION['userId']) || $_SESSION['roleId'] !== 2) {
    exit('Access denied.');
}
include '../private/conn_digidate_examen.php';


if (isset($_GET['message'])) {
    echo "<div class='text-green-500 font-bold mb-4'>" . htmlspecialchars($_GET['message']) . "</div>";
}
if (isset($_GET['error'])) {
    echo "<div class='text-red-500 font-bold mb-4'>" . htmlspecialchars($_GET['error']) . "</div>";
}

try {
    // Prepare the query to fetch users with roleId = 2 (Admins)
    $query = "SELECT userId, firstName, middleName, lastName, email FROM users WHERE roleId = 2 AND activated = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if there are any results
    if (count($results) === 0) {
        echo "<tr><td colspan='5' class='py-3 px-4 text-center'>No admin users found.</td></tr>";
    } else {
        foreach ($results as $row) {
            if ($_SESSION['userId'] == $row['userId']) {
                continue;
            }
            echo "<tr class='hover:bg-gray-100'>";
            echo "<td class='py-3 px-4 border-b border-gray-200 w-1/5'>" . htmlspecialchars($row['firstName']) . "</td>";
            echo "<td class='py-3 px-4 border-b border-gray-200 w-1/5'>" . htmlspecialchars($row['middleName']) . "</td>";
            echo "<td class='py-3 px-4 border-b border-gray-200 w-1/5'>" . htmlspecialchars($row['lastName']) . "</td>";
            echo "<td class='py-3 px-4 border-b border-gray-200 w-1/5'>" . htmlspecialchars($row['email']) . "</td>";

            // Adding a delete button with improvements
            echo "<td class='py-3 px-4 border-b border-gray-200 w-1/5 text-center'>";
            echo "<form action='php/delete_admin.php' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this user?\");'>";
            echo "<input type='hidden' name='userId' value='" . htmlspecialchars($row['userId']) . "' />";
            echo "<button type='submit' aria-label='Delete User' class='bg-red-500 hover:bg-red-600  font-bold py-2 px-4 rounded shadow-lg transition duration-300 ease-in-out transform hover:scale-105'>";
            echo "Delete";
            echo "</button>";
            echo "</form>";
            echo "</td>";

            echo "</tr>";
        }

    }

} catch (PDOException $e) {
    echo "<tr><td colspan='4' class='py-3 px-4 text-center text-red-500'>";
    echo "Error: " . htmlspecialchars($e->getMessage());
    echo "</td></tr>";
}
?>