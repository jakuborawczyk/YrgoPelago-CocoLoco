<?php
declare(strict_types=1);
// Set up a PDO connection to your SQLite database
try {
    $pdo = new PDO('sqlite:../hotel_database.sqlite3');  // Path to your SQLite database
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Uncomment the line below if you want to log queries or errors
    // error_log('Connected to database successfully');
} catch (PDOException $e) {
    // Handle connection error
    die("Could not connect to the database. Error: " . $e->getMessage());
}
