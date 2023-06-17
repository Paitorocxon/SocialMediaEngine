<?php
session_start();
session_destroy();

if (file_exists('database.db')) {
    unlink('database.db');
}

// Datenbankdatei erstellen bzw. öffnen
$database = new SQLite3('database.db');

// Benutzertabelle erstellen
$query = "CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    full_name TEXT,
    profile_picture TEXT,
    biography TEXT,
    follower_count INTEGER DEFAULT 0,
    post_count INTEGER DEFAULT 0
)";
$database->exec($query);

// Beiträge-Tabelle erstellen
$query = "CREATE TABLE IF NOT EXISTS posts (
    post_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    content TEXT NOT NULL,
    like_count INTEGER ,
    image_path TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (username) REFERENCES users(username)
)";
$database->exec($query);

// Likes-Tabelle erstellen
$query = "CREATE TABLE IF NOT EXISTS likes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER NOT NULL,
    username TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(post_id),
    FOREIGN KEY (username) REFERENCES users(username),
    UNIQUE (post_id, username) ON CONFLICT IGNORE
)";
$database->exec($query);

// Kommentare-Tabelle erstellen
$query = "CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER NOT NULL,
    username TEXT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(post_id),
    FOREIGN KEY (username) REFERENCES users(username)
)";
$database->exec($query);

// Follower-Tabelle erstellen
$query = "CREATE TABLE IF NOT EXISTS follows (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    follower_username TEXT NOT NULL,
    followee_username TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (follower_username) REFERENCES users(username),
    FOREIGN KEY (followee_username) REFERENCES users(username),
    UNIQUE (follower_username, followee_username) ON CONFLICT IGNORE
)";
$database->exec($query);

// Block-Tabelle erstellen
$query = "CREATE TABLE IF NOT EXISTS blocks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    blocked_username TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (username) REFERENCES users(username),
    FOREIGN KEY (blocked_username) REFERENCES users(username)
)";
$database->exec($query);

// own_posts-Tabelle erstellen
$query = "CREATE TABLE IF NOT EXISTS own_posts (
    post_id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    content TEXT NOT NULL,
    image_path TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (username) REFERENCES users(username)
)";
$database->exec($query);

// Spalte followed_username zur Tabelle follows hinzufügen
$query = "ALTER TABLE follows ADD COLUMN followed_username TEXT";
$database->exec($query);

// Beispielbenutzer und Beiträge einfügen (optional)
$query = "INSERT OR IGNORE INTO users (username, password, email, full_name) VALUES
    ('user1', 'pass1', 'user1@example.com', 'User 1'),
    ('user2', 'pass2', 'user2@example.com', 'User 2')";
$database->exec($query);

$query = "INSERT OR IGNORE INTO posts (username, content) VALUES
    ('user1', 'Hallo, ich bin User 1!'),
    ('user2', 'Hallo, ich bin User 2!')";
$database->exec($query);

// Datenbankverbindung schließen
$database->close();
?>
