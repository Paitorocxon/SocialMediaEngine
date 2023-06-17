<?php
// Datenbankverbindung herstellen
$database = new SQLite3('database.db');

session_start();
$current_user = $_SESSION['username'];
$profile_user = $_POST['profile_user'];

// Überprüfen, ob der aktuelle Benutzer dem Profilinhaber bereits folgt
$query = "SELECT * FROM follows WHERE follower_username = :current_user AND followee_username = :profile_user";
$stmt = $database->prepare($query);
$stmt->bindValue(':current_user', $current_user, SQLITE3_TEXT);
$stmt->bindValue(':profile_user', $profile_user, SQLITE3_TEXT);
$follow_result = $stmt->execute();
$followed = $follow_result->fetchArray(SQLITE3_ASSOC);

if ($followed) {
    // Der aktuelle Benutzer folgt bereits dem Profilinhaber
    echo "Du folgst diesem Profil bereits.";
} else {
    // Folgen hinzufügen
    $query = "INSERT INTO follows (follower_username, followee_username) VALUES (:current_user, :profile_user)";
    $stmt = $database->prepare($query);
    $stmt->bindValue(':current_user', $current_user, SQLITE3_TEXT);
    $stmt->bindValue(':profile_user', $profile_user, SQLITE3_TEXT);
    $stmt->execute();

    echo "Du folgst jetzt diesem Profil.";
}

// Datenbankverbindung schließen
$database->close();
?>
