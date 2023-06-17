<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['follow_username']) && !empty($_POST['follow_username'])) {
        $follower_username = $_SESSION['username'];
        $followee_username = $_POST['follow_username'];

        $database = new SQLite3('database.db');

        $query = "INSERT INTO follows (follower_username, followee_username) VALUES ('$follower_username', '$followee_username')";
        $database->exec($query);

        $database->close();

        $redirect_url = "profile.php?username=" . urlencode($followee_username);
        header("Location: $redirect_url");
        exit();
    } elseif (isset($_POST['unfollow_username']) && !empty($_POST['unfollow_username'])) {
        $follower_username = $_SESSION['username'];
        $followee_username = $_POST['unfollow_username'];

        $database = new SQLite3('database.db');

        $query = "DELETE FROM follows WHERE follower_username = '$follower_username' AND followee_username = '$followee_username'";
        $database->exec($query);

        $database->close();

        $redirect_url = "profile.php?username=" . urlencode($followee_username);
        header("Location: $redirect_url");
        exit();
    } elseif (isset($_POST['block_username']) && !empty($_POST['block_username'])) {
        $username = $_SESSION['username'];
        $blocked_username = $_POST['block_username'];

        $database = new SQLite3('database.db');

        $query = "INSERT INTO blocks (username, blocked_username) VALUES ('$username', '$blocked_username')";
        $database->exec($query);

        $database->close();

        $redirect_url = "profile.php?username=" . urlencode($blocked_username);
        header("Location: $redirect_url");
        exit();
    } elseif (isset($_POST['unblock_username']) && !empty($_POST['unblock_username'])) {
        $username = $_SESSION['username'];
        $unblocked_username = $_POST['unblock_username'];

        $database = new SQLite3('database.db');

        $query = "DELETE FROM blocks WHERE username = '$username' AND blocked_username = '$unblocked_username'";
        $database->exec($query);

        $database->close();

        $redirect_url = "profile.php?username=" . urlencode($unblocked_username);
        header("Location: $redirect_url");
        exit();
    }
}

if (isset($_GET['username']) && !empty($_GET['username'])) {
    $profile_username = $_GET['username'];
} else {
    $profile_username = $_SESSION['username'];
}

$database = new SQLite3('database.db');

$query = "SELECT * FROM users WHERE username = '$profile_username'";
$result = $database->querySingle($query, true);

if (!$result) {
    header("Location: profile.php");
    exit();
}

$profile_user = $result;

$query = "SELECT * FROM posts WHERE username = '$profile_username' ORDER BY created_at DESC";
$result = $database->query($query);

$posts = [];
while ($post = $result->fetchArray(SQLITE3_ASSOC)) {
    $posts[] = $post;
}

$database->close();

// Überprüfen, ob der angemeldete Benutzer blockiert ist
$blocked = isBlocked($profile_username, $_SESSION['username']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profilansicht</title>
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
        
    <a href="index.php">Zurück zur Hauptseite</a>
        <?php if ($blocked) { ?>
            <h2>Sie wurden von diesem Konto ausgeschlossen!</h2>
            <form method="post" action="">
                <input type="hidden" name="unblock_username" value="<?php echo $profile_user['username']; ?>">
                <input type="submit" value="Entblocken">
            </form>
        <?php } else { ?>
            <h2>Profil von <a href="settings.php"><?php echo $profile_user['username']; ?></a></h2>
            <div class="profile-info">
                <?php if (!empty($profile_user['profile_picture'])) { ?>
                    <img src="<?php echo $profile_user['profile_picture']; ?>" alt="Profilbild">
                <?php } ?>
                <p><strong>Name:</strong> <?php echo $profile_user['full_name']; ?></p>
                <p><strong>E-Mail:</strong> <?php echo $profile_user['email']; ?></p>
                <p><strong>Biografie:</strong> <?php echo $profile_user['biography']; ?></p>
                <p><strong>Anzahl Follower:</strong> <?php echo $profile_user['follower_count']; ?></p>
                <form method="post" action="">
                    <?php if ($_SESSION['username'] !== $profile_user['username']) { ?>
                        <?php if (isFollowing($_SESSION['username'], $profile_user['username'])) { ?>
                            <input type="hidden" name="unfollow_username" value="<?php echo $profile_user['username']; ?>">
                            <input type="submit" value="Entfolgen">
                        <?php } else { ?>
                            <input type="hidden" name="follow_username" value="<?php echo $profile_user['username']; ?>">
                            <input type="submit" value="Folgen">
                        <?php } ?>
                    <?php } ?>
                </form>
                <?php if ($_SESSION['username'] !== $profile_user['username']) { ?>
                    <?php if (isBlocked($_SESSION['username'], $profile_user['username'])) { ?>
                        <form method="post" action="">
                            <input type="hidden" name="unblock_username" value="<?php echo $profile_user['username']; ?>">
                            <input type="submit" value="Entblocken">
                        </form>
                    <?php } else { ?>
                        <form method="post" action="">
                            <input type="hidden" name="block_username" value="<?php echo $profile_user['username']; ?>">
                            <input type="submit" value="Blockieren">
                        </form>
                    <?php } ?>
                <?php } ?>
            </div>
            
            <h3>Beiträge</h3>
            
            <?php if (empty($posts)) { ?>
                <p>Es wurden noch keine Beiträge veröffentlicht.</p>
            <?php } else { ?>
                <?php foreach ($posts as $post) { ?>
                    <div class="post">
                        <div class="post-content">
                            <h3><?php echo $profile_user['username']; ?></h3>
                            <p><?php echo $post['content']; ?></p>
                            <?php if (!empty($post['image_path'])) { ?>
                                <img src="uploads/<?php echo $post['image_path']; ?>" alt="Beitragsbild">
                            <?php } ?>
                            <p class="timestamp"><?php echo $post['created_at']; ?></p>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </div>
</body>
</html>

<?php
function isFollowing($follower_username, $followee_username)
{
    $database = new SQLite3('database.db');
    $query = "SELECT COUNT(*) FROM follows WHERE follower_username = '$follower_username' AND followee_username = '$followee_username'";
    $result = $database->querySingle($query);
    $database->close();
    return ($result > 0);
}

function isBlocked($username, $blocked_username)
{
    $database = new SQLite3('database.db');
    
    $query = "SELECT COUNT(*) FROM blocks WHERE username = '$username' AND blocked_username = '$blocked_username'";
    $result = $database->querySingle($query);
    
    $database->close();
    
    return ($result > 0);
}
?>
