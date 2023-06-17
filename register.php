<!DOCTYPE html>
<html>
<head>
    <title>Registrierung</title>
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
