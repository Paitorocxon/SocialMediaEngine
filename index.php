<?php
session_start();

// Datenbankdatei öffnen
$database = new SQLite3('database.db');

// Prüfen, ob Benutzer angemeldet ist
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    // Beiträge des angemeldeten Benutzers abrufen
    $query = "SELECT p.*, u.profile_picture FROM posts p JOIN users u ON p.username = u.username WHERE p.username = :username ORDER BY p.created_at DESC";
    $statement = $database->prepare($query);
    $statement->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $statement->execute();
    $ownPosts = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $ownPosts[] = $row;
    }

    // Beiträge der Benutzer abrufen, denen der eingeloggte Benutzer folgt
    $followedUsersQuery = "SELECT f.followee_username, u.profile_picture
    FROM follows f
    JOIN users u ON f.followee_username = u.username
    LEFT JOIN blocks b ON f.followee_username = b.username AND b.blocked_username = :username
    LEFT JOIN blocks b2 ON f.follower_username = :username AND b2.blocked_username = b2.username
    WHERE f.follower_username = :username AND (b.blocked_username IS NULL OR b.username IS NULL) AND b2.blocked_username IS NULL
    ";
    $followedUsersStatement = $database->prepare($followedUsersQuery);
    $followedUsersStatement->bindValue(':username', $username, SQLITE3_TEXT);
    $followedUsersResult = $followedUsersStatement->execute();
    $followedUsers = [];
    while ($followedUserRow = $followedUsersResult->fetchArray(SQLITE3_ASSOC)) {
    $followedUsers[] = $followedUserRow['followee_username'];
    }

    $followedPostsQuery = "SELECT p.*, u.profile_picture FROM posts p JOIN users u ON p.username = u.username WHERE p.username IN (" . implode(',', array_fill(0, count($followedUsers), '?')) . ") ORDER BY p.created_at DESC";    $followedPostsStatement = $database->prepare($followedPostsQuery);
    foreach ($followedUsers as $index => $followedUser) {
        $followedPostsStatement->bindValue($index + 1, $followedUser, SQLITE3_TEXT);
    }
    $followedPostsResult = $followedPostsStatement->execute();
    $followedPosts = [];
    while ($followedPostRow = $followedPostsResult->fetchArray(SQLITE3_ASSOC)) {
        $followedPosts[] = $followedPostRow;
    }

    // Kombiniere eigene Beiträge und Beiträge der gefolgten Benutzer
    $posts = array_merge($ownPosts, $followedPosts);
    // Sortiere die Beiträge nach dem Erstellungsdatum absteigend
    usort($posts, function ($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
} else {
    header("Location: login.php");
}

// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Überprüfen, ob ein Beitragstext eingegeben wurde
    if (!empty($_POST['post'])) {
        $postContent = $_POST['post'];
        $image = $_FILES['image']['name'];
        $tmp_image = $_FILES['image']['tmp_name'];

        // Den Beitrag in die Datenbank einfügen
        $insertQuery = "INSERT INTO posts (username, content, image_path, like_count) VALUES (:username, :content, \"$image\" , 0)";
        $insertStatement = $database->prepare($insertQuery);
        $insertStatement->bindValue(':username', $username, SQLITE3_TEXT);
        $insertStatement->bindValue(':content', $postContent, SQLITE3_TEXT);
        $insertStatement->execute();

        if (!empty($image)) {
            move_uploaded_file($tmp_image, "uploads/" . $image);
        }
        // Die Seite neu laden, um die neuen Beiträge anzuzeigen
        header('Location: index.php');
        exit();
    }

    // Überprüfen, ob ein Kommentar abgegeben wurde
    if (!empty($_POST['comment_text'])) {
        $postId = $_POST['post_id'];
        $commentText = $_POST['comment_text'];

        // Den Kommentar in die Datenbank einfügen
        $commentQuery = "INSERT INTO comments (post_id, username, comment_text) VALUES (:post_id, :username, :comment_text)";
        $commentStatement = $database->prepare($commentQuery);
        $commentStatement->bindValue(':post_id', $postId, SQLITE3_INTEGER);
        $commentStatement->bindValue(':username', $username, SQLITE3_TEXT);
        $commentStatement->bindValue(':comment_text', $commentText, SQLITE3_TEXT);
        $commentStatement->execute();

        // Die Seite neu laden, um den neuen Kommentar anzuzeigen
        header("Location: index.php#post-$postId");
        exit();
    }

    // Überprüfen, ob ein Like abgegeben wurde
    if (!empty($_POST['like'])) {
        $postId = $_POST['post_id'];

        // Überprüfen, ob der Benutzer den Beitrag bereits geliked hat
        $checkLikeQuery = "SELECT COUNT(*) FROM likes WHERE post_id = :post_id AND username = :username";
        $checkLikeStatement = $database->prepare($checkLikeQuery);
        $checkLikeStatement->bindValue(':post_id', $postId, SQLITE3_INTEGER);
        $checkLikeStatement->bindValue(':username', $username, SQLITE3_TEXT);
        $existingLikes = $checkLikeStatement->execute()->fetchArray(SQLITE3_NUM);

        if ($existingLikes > 0) {
            // Benutzer hat bereits geliked, daher den Like entfernen
            $deleteLikeQuery = "DELETE FROM likes WHERE post_id = :post_id AND username = :username";
            $deleteLikeStatement = $database->prepare($deleteLikeQuery);
            $deleteLikeStatement->bindValue(':post_id', $postId, SQLITE3_INTEGER);
            $deleteLikeStatement->bindValue(':username', $username, SQLITE3_TEXT);
            $deleteLikeStatement->execute();
        } else {
            // Benutzer hat noch nicht geliked, daher einen neuen Like hinzufügen
            $addLikeQuery = "INSERT INTO likes (post_id, username) VALUES (:post_id, :username)";
            $addLikeStatement = $database->prepare($addLikeQuery);
            $addLikeStatement->bindValue(':post_id', $postId, SQLITE3_INTEGER);
            $addLikeStatement->bindValue(':username', $username, SQLITE3_TEXT);
            $addLikeStatement->execute();
        }

        // Die Seite neu laden, um die Änderungen anzuzeigen
        header("Location: index.php#post-$postId");
        exit();
    }
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Startseite</title>
    <!--<link rel="stylesheet" type="text/css" href="style.css">-->
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
    <div class="top-menu">
        <a href="profile.php">Profil</a>
        <a href="search.php">Suche</a>
        <a href="logout.php">Logout</a>
    </div>
    <h1>Startseite</h1>
    <form class="createpost" method="post" action="" enctype="multipart/form-data">
        <textarea name="post" placeholder="Schreibe deinen Beitrag"  rows="5" cols="40"></textarea>
        <input type="file" name="image">
        <button type="submit">Beitrag veröffentlichen</button>
    </form>
    <div class="posts">
        <?php foreach ($posts as $post): ?>
            <div class="post" id="post-<?php echo $post['post_id']; ?>">
                <div class="post-header">
                    <span class="post-profilepicture"><img src="<?php echo (isset($post['profile_picture'])&& $post['profile_picture'] != "" ?  $post['profile_picture'] : './pb.png') ; ?>"></span>
                    <span class="post-username"><a href="profile.php?username=<?php echo $post['username']; ?>"><?php echo $post['username']; ?></a></span>
                    <span class="post-date"><?php echo $post['created_at']; ?></span>
                </div>
                <div class="post-content">
                    <?php echo $post['content']; ?>
                <?php if (!empty($post['image_path'])): ?>
                    <div class="post-image">
                        <img src="uploads/<?php echo $post['image_path']; ?>" alt="Beitragsbild">
                    </div>
                <?php endif; ?>
                
                </div>
                <div class="post-actions">
                    <?php
                    // Überprüfen, ob der Benutzer den Beitrag geliked hat
                    // $checkLikeQuery = "SELECT COUNT(*) FROM likes WHERE post_id = :post_id AND username = :username";
                    // $checkLikeStatement = $database->prepare($checkLikeQuery);
                    // $checkLikeStatement->bindValue(':post_id', $post['post_id'], SQLITE3_INTEGER);
                    // $checkLikeStatement->bindValue(':username', $username, SQLITE3_TEXT);
                    // $existingLikes = $checkLikeStatement->execute()->fetchArray(SQLITE3_NUM)[0];

                    // if ($existingLikes > 0) {
                    //     echo '<form method="post" action="">';
                    //     echo '<input type="hidden" name="post_id" value="' . $post['post_id'] . '">';
                    //     echo '<button type="submit" name="like" class="unlike-button">Unlike</button>';
                    //     echo '</form>';
                    // } else {
                    //     echo '<form method="post" action="">';
                    //     echo '<input type="hidden" name="post_id" value="' . $post['post_id'] . '">';
                    //     echo '<button type="submit" name="like" class="like-button">Like</button>';
                    //     echo '</form>';
                    // }
                    ?>
                    <a href="#comment-section-<?php echo $post['post_id']; ?>">Kommentieren</a>
                </div>
                <div class="post-likes">
                    <?php
                    // Anzahl der Likes für den Beitrag abrufen
                    $likesQuery = "SELECT COUNT(*) FROM likes WHERE post_id = :post_id";
                    $likesStatement = $database->prepare($likesQuery);
                    $likesStatement->bindValue(':post_id', $post['post_id'], SQLITE3_INTEGER);
                    $likesCount = $likesStatement->execute()->fetchArray(SQLITE3_NUM)[0];

                    echo $likesCount . ' Likes';
                    ?>
                </div>
                <div class="comment-section" id="comment-section-<?php echo $post['post_id']; ?>">
                    <?php
                    // Kommentare für den Beitrag abrufen
                    $commentsQuery = "SELECT * FROM comments WHERE post_id = :post_id";
                    $commentsStatement = $database->prepare($commentsQuery);
                    $commentsStatement->bindValue(':post_id', $post['post_id'], SQLITE3_INTEGER);
                    $commentsResult = $commentsStatement->execute();

                    while ($comment = $commentsResult->fetchArray(SQLITE3_ASSOC)) {
                        echo '<div class="comment">';
                        echo '<span class="comment-username">' . $comment['username'] . ':</span>';
                        echo '<span class="comment-text"> ' . $comment['comment_text'] . '</span>';
                        echo '</div>';
                    }
                    ?>

                    <form method="post" action="">
                        <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                        <input type="text" class="givecomment" name="comment_text" placeholder="Kommentar schreiben">
                        <button type="submit">Kommentieren</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
