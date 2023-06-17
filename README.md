# Social Media Engine

Dieses GitHub-Repository enthält eine kleine Social Media Engine, die auf SQLite basiert und unter der Mozilla License 2.0 veröffentlicht wird. Diese Engine ermöglicht es Benutzern, Beiträge zu erstellen, anderen Benutzern zu folgen, Beiträge zu liken und zu kommentieren.

## Voraussetzungen

Um die Social Media Engine zu verwenden, müssen Sie das `SQLite3`-Modul für PHP aktivieren. Hier ist eine kurze Anleitung, wie Sie dies in Ihrer `php.ini`-Datei einstellen können:

1. Suchen Sie die `php.ini`-Datei auf Ihrem Server. Sie befindet sich normalerweise im PHP-Installationsverzeichnis oder im Verzeichnis `/etc/php`.

2. Öffnen Sie die `php.ini`-Datei in einem Texteditor.

3. Suchen Sie die Zeile, die mit `;extension=sqlite3` beginnt.

4. Entfernen Sie das Semikolon am Anfang der Zeile, um die Erweiterung zu aktivieren. Die Zeile sollte dann wie folgt aussehen: `extension=sqlite3`.

5. Speichern Sie die `php.ini`-Datei und starten Sie Ihren Webserver neu, um die Änderungen zu übernehmen.

## Installation

Um die Social Media Engine zu installieren, befolgen Sie bitte die folgenden Schritte:

1. Laden Sie den gesamten Inhalt dieses Repositorys herunter.

2. Verschieben Sie alle Dateien in das Webverzeichnis Ihres Servers.

3. Erstellen Sie innerhalb des Webverzeichnisses einen neuen Ordner namens "uploads". Dieser Ordner wird für den Bild-Upload benötigt.

4. Führen Sie die Datei `GENDB.php` aus, um die Datenbank zu erstellen. Stellen Sie sicher, dass Sie über die erforderlichen Berechtigungen verfügen, um eine neue Datenbank zu erstellen.

5. Nach erfolgreicher Ausführung von `GENDB.php` können Sie auf die Social Media Engine über Ihren Webbrowser zugreifen.

Bitte beachten Sie, dass diese Anwendung nicht auf Sicherheit getestet wurde und daher bestimmte Gefahren aufweisen kann. Insbesondere der Bild-Upload und SQL-Injections können potenzielle Sicherheitsrisiken darstellen. Verwenden Sie diese Engine daher auf eigene Verantwortung und setzen Sie geeignete Sicherheitsmaßnahmen ein, bevor Sie sie in einer Produktionsumgebung einsetzen.

## Lizenz

Dieses Projekt steht unter der [Mozilla License 2.0](LICENSE). Bitte lesen Sie die Lizenzdatei für weitere Informationen.

## Autoren

Dieses Projekt wurde von Fabian Müller entwickelt. Hoffentlich bleibt es nicht nur bei einer Person!

Wir freuen uns über Beiträge und Pull Requests, um diese Social Media Engine weiter zu verbessern.
