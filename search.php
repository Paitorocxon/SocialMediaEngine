<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search']) && !empty($_POST['search'])) {
        $search_query = $_POST['search'];
        $redirect_url = "search.php?q=" . urlencode($search_query);
        header("Location: $redirect_url");
        exit();
    }
}

$username = $_SESSION['username'];

// Datenbankverbindung herstellen
$database = new SQLite3('database.db');

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_query = $_GET['q'];

    // Benutzer suchen
    $searchQuery = "SELECT username FROM users WHERE username LIKE '%$search_query%'";
    $searchResult = $database->query($searchQuery);
} else {
    // Alle Benutzer auflisten
    $searchQuery = "SELECT username FROM users";
    $searchResult = $database->query($searchQuery);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Suche</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Suche</h1>
        <form action="search.php" method="post">
            <input type="text" name="search" placeholder="Suche nach Benutzern">
            <input type="submit" value="Suchen">
        </form>
        <h2>Ergebnisse</h2>
        <?php while ($row = $searchResult->fetchArray(SQLITE3_ASSOC)) {
            $searchedUser = $row['username'];
            
            // Überprüfen, ob der Benutzer bereits diesem Profil folgt
            $checkFollowQuery = "SELECT COUNT(*) as count FROM follows WHERE follower_username = '$username' AND followee_username = '$searchedUser'";
            $checkFollowResult = $database->querySingle($checkFollowQuery);

            echo "<div class='user'>
                    <a href='profile.php?username=$searchedUser' class='username'>$searchedUser</a>";
            
            if ($searchedUser !== $username) {
                if ($checkFollowResult > 0) {
                    echo "<a href='profile.php?unfollow=$searchedUser' class='button'>Entfolgen</a>";
                } else {
                    echo "<a href='profile.php?follow=$searchedUser' class='button'>Folgen</a>";
                }
            }
            
            echo "</div>";
        } ?>
    </div>
</body>
</html>

<?php
// Datenbankverbindung schließen
$database->close();
?>
