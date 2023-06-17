<?php
// Datenbankverbindung herstellen
$database = new SQLite3('database.db');

session_start();
$current_user = $_SESSION['username'];
$profile_user = $_POST['profile_user'];

// Überprüfen, ob der aktuelle Benutzer das Profil bereits blockiert hat
$query = "SELECT * FROM blocks WHERE username = :current_user AND blocked_username = :profile_user";
$stmt = $database->prepare($query);
$stmt->bindValue(':current_user', $current_user, SQLITE3_TEXT);
$stmt->bindValue(':profile_user', $profile_user, SQLITE3_TEXT);
$result = $stmt->execute();
$blocked = $result->fetchArray(SQLITE3_ASSOC);

if ($blocked) {
    // Der aktuelle Benutzer hat das Profil bereits blockiert
    echo "Du hast dieses Profil bereits blockiert.";
} else {
    // Blockierung hinzufügen
    $query = "INSERT INTO blocks (username, blocked_username) VALUES (:current_user, :profile_user)";
    $stmt = $database->prepare($query);
    $stmt->bindValue(':current_user', $current_user, SQLITE3_TEXT);
    $stmt->bindValue(':profile_user', $profile_user, SQLITE3_TEXT);
    $stmt->execute();

    echo "Das Profil wurde erfolgreich blockiert.";
}

// Datenbankverbindung schließen
$database->close();
?>
