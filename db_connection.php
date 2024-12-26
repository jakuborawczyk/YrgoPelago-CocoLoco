<?php
declare(strict_types=1);

try {
    $pdo = new PDO('sqlite:' . getenv('DB_PATH'));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
