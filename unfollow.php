<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['unfollow_username']) && !empty($_POST['unfollow_username'])) {
        $follower_username = $_SESSION['username'];
        $followee_username = $_POST['unfollow_username'];

        $database = new SQLite3('database.db');

        $query = "DELETE FROM follows WHERE follower_username = '$follower_username' AND followee_username = '$followee_username'";
        $database->exec($query);

        $database->close();

        $redirect_url = "profile.php?username=" . urlencode($followee_username);
        header("Location: $redirect_url");
        exit();
    }
}
?>
