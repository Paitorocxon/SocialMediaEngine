<?php
session_start();

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Prüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dateiupload überprüfen
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        $targetDirectory = 'uploads/';
        $targetFile = $targetDirectory . basename($image['name']);

        // Datei in den Zielordner verschieben
        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            // Erfolgreicher Upload
            $message = "Bild erfolgreich hochgeladen.";
            // Weitere Verarbeitung, z. B. Speichern des Bildpfads in der Datenbank
        } else {
            $error = "Beim Hochladen des Bildes ist ein Fehler aufgetreten.";
        }
    }

    // Beitrag verarbeiten
    $content = $_POST['content'];
    // Weitere Verarbeitung, z. B. Speichern des Beitrags in der Datenbank

    // Erfolgsmeldung anzeigen
    $message = "Beitrag erfolgreich erstellt.";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Beitrag erstellen</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Beitrag erstellen</h1>
        <?php if (isset($message)) : ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (isset($error)) : ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <textarea name="content" placeholder="Schreibe deinen Beitrag hier..." required></textarea>
            <input type="file" name="image" accept="image/*">
            <input type="submit" value="Beitrag erstellen">
        </form>
    </div>
</body>
</html>
