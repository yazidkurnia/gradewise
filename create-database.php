<?php

/**
 * Script untuk membuat database
 * Usage: php create-database.php
 */

try {
    // Database configuration dari .env
    $host = '127.0.0.1';
    $port = '3306';
    $username = 'root';
    $password = '';
    $database = 'coreapp';

    echo "Connecting to MySQL...\n";

    // Connect tanpa database
    $pdo = new PDO(
        "mysql:host={$host};port={$port}",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Creating database '{$database}'...\n";

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    echo "\033[32m✓ Database '{$database}' created successfully!\033[0m\n";
    echo "\nYou can now run: php artisan migrate\n";

} catch (PDOException $e) {
    echo "\033[31m✗ Error: " . $e->getMessage() . "\033[0m\n";
    exit(1);
}
