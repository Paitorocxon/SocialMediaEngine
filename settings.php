<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_bio'])) {
        $newBio = $_POST['biography'];
        $newProfPic = $_POST['profile_picture'];

        // Datenbankverbindung herstellen
        $database = new SQLite3('database.db');

        // Biografie aktualisieren
        $updateQuery = "UPDATE users SET biography = '$newBio', profile_picture = '$newProfPic' WHERE username = '$username'";
        $database->exec($updateQuery);

        $success = "Deine Biografie wurde erfolgreich aktualisiert.";

        // Datenbankverbindung schließen
        $database->close();
    }
}

// Datenbankverbindung herstellen
$database = new SQLite3('database.db');

// Benutzerdaten abrufen
$query = "SELECT * FROM users WHERE username = '$username'";
$result = $database->querySingle($query, true);

// Datenbankverbindung schließen
$database->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Biografie bearbeiten</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Biografie bearbeiten</h1>
        <?php if (isset($success)) { ?>
            <p class="success"><?php echo $success; ?></p>
        <?php } ?>
        <form method="post" action="">
            <label for="biography">Biografie:</label><br>
            <textarea name="biography"><?php echo $result['biography']; ?></textarea><br>
            <label for="profile_picture">Profilbild:</label><br>
            <textarea name="profile_picture"><?php echo $result['profile_picture']; ?></textarea><br>
            <input type="submit" name="update_bio" value="Biografie aktualisieren">
        </form>
        <a href="profile.php?username=<?php echo $username; ?>">Zurück zum Profil</a>
    </div>
</body>
</html>
