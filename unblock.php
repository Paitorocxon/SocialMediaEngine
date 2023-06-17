<?php
// Datenbankverbindung herstellen
$database = new SQLite3('database.db');

// Prüfen, ob ein Nutzer angemeldet ist
session_start();
if (!isset($_SESSION['username'])) {
    die("Sie sind nicht angemeldet.");
}

// Überprüfen, ob das Formular übermittelt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob der übergebene Nutzername gültig ist
    if (isset($_POST['profile_user'])) {
        $profile_user = $_POST['profile_user'];
    } else {
        die("Ungültiger Nutzername.");
    }

    // Aktuellen Benutzer und blockierten Nutzer aus der Session abrufen
    $current_user = $_SESSION['username'];

    // Den blockierten Nutzer aus der Tabelle 'blocks' entfernen
    $query = "DELETE FROM blocks WHERE username = :current_user AND blocked_username = :profile_user";
    $stmt = $database->prepare($query);
    $stmt->bindValue(':current_user', $current_user, SQLITE3_TEXT);
    $stmt->bindValue(':profile_user', $profile_user, SQLITE3_TEXT);
    $stmt->execute();

    // Erfolgsnachricht anzeigen
    echo "Der Nutzer $profile_user wurde entsperrt.";

} else {
    die("Ungültige Anfrage.");
}

// Datenbankverbindung schließen
$database->close();
?>
