<?php
session_start();

// Datenbankverbindung herstellen
$database = new SQLite3('database.db');

// Eingabeformulardaten abrufen
$usernameOrEmail = $_POST['username'];
$password = $_POST['password'];

// Abfrage für Benutzer anhand des Benutzernamens oder der E-Mail
$query = "SELECT * FROM users WHERE username = :username OR email = :email";
$statement = $database->prepare($query);
$statement->bindValue(':username', $usernameOrEmail);
$statement->bindValue(':email', $usernameOrEmail);
$result = $statement->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

// Überprüfen, ob ein Benutzer gefunden wurde und das eingegebene Passwort korrekt ist
if ($user && password_verify($password, $user['password'])) {
    // Anmeldung erfolgreich
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    header("Location: index.php");
    exit();
} else {
    // Anmeldung fehlgeschlagen
    echo "Ungültige Anmeldeinformationen. Bitte überprüfe deine Eingabe und versuche es erneut.";
}

// Datenbankverbindung schließen
$database->close();
?>
