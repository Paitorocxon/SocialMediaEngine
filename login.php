<?php
session_start();

// Überprüfen, ob der Benutzer bereits angemeldet ist
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Überprüfen, ob das Anmeldeformular abgesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datenbankverbindung herstellen
    $database = new SQLite3('database.db');

    // Benutzername und Passwort aus dem Formular abrufen
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Benutzerdaten aus der Datenbank abrufen
    $query = "SELECT * FROM users WHERE username = :username";
    $statement = $database->prepare($query);
    $statement->bindValue(':username', $username);
    $result = $statement->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    // Überprüfen, ob der Benutzer existiert und das Passwort korrekt ist
    if ($user && password_verify($password, $user['password'])) {
        // Benutzersitzung starten
        $_SESSION['username'] = $user['username'];

        // Weiterleitung zur Startseite
        header("Location: index.php");
        exit();
    } else {
        $error = "Ungültige Anmeldeinformationen.";
    }

    // Datenbankverbindung schließen
    $database->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Anmeldung</title>
    <script>
    function loadStylesheet() {
      var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
      var stylesheet = document.createElement('link');
      stylesheet.rel = 'stylesheet';

      if (screenWidth <= 767) {
        // Pfad zum Stylesheet für Smartphones
        stylesheet.href = 'styleS.css';
      } else {
        // Pfad zum Standard-Stylesheet für Desktop
        stylesheet.href = 'style.css';
      }

      document.head.appendChild(stylesheet);
    }
    window.addEventListener('DOMContentLoaded', loadStylesheet);
  </script>
</head>
<body>
    <div class="container">
        <h1>Anmeldung</h1>
        <?php if (isset($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>
        <form method="POST" action="">
            <label for="username">Benutzername:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Passwort:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Anmelden</button>
        </form>
        <p>Noch keinen Account? <a href="register.php">Hier registrieren</a></p>
    </div>
</body>
</html>
