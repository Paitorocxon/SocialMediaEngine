<?php
session_start();

// Datenbankverbindung herstellen
$database = new SQLite3('database.db');

// Eingabeformulardaten abrufen
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];

// Überprüfen, ob der Benutzername bereits existiert
$query = "SELECT COUNT(*) as count FROM users WHERE username = :username";
$statement = $database->prepare($query);
$statement->bindValue(':username', $username);
$result = $statement->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);

if ($row['count'] > 0) {
    // Benutzername ist bereits vergeben
    echo "Der Benutzername ist bereits vergeben. Bitte wähle einen anderen Benutzernamen.";
    exit();
}

// Passwort hashen
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Benutzer in die Datenbank einfügen
$insertQuery = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
$insertStatement = $database->prepare($insertQuery);
$insertStatement->bindValue(':username', $username);
$insertStatement->bindValue(':password', $hashedPassword);
$insertStatement->bindValue(':email', $email);
$insertResult = $insertStatement->execute();

if ($insertResult) {
    echo "Registrierung erfolgreich. Du kannst dich jetzt <a href='login.php'>hier anmelden</a>.";
} else {
    echo "Bei der Registrierung ist ein Fehler aufgetreten. Bitte versuche es erneut.";
}

// Datenbankverbindung schließen
$database->close();
?>
