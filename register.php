<!DOCTYPE html>
<html>
<head>
    <title>Registrierung</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Registrierung</h1>
        <form action="register_process.php" method="POST">
            <label for="username">Benutzername:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Passwort:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <label for="email">E-Mail:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <input type="submit" value="Registrieren">
        </form>
        <p>Bereits registriert? <a href="login.php">Hier anmelden</a></p>
    </div>
</body>
</html>
