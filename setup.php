<?php

require __DIR__ . '/vendor/autoload.php';

use App\Database\Connection;
use Carbon\Carbon;

$pdo = Connection::getPDO();

// Create tables
$queries = [
    "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        symbol TEXT NOT NULL,
        quantity REAL NOT NULL,
        price REAL NOT NULL,
        type TEXT NOT NULL CHECK(type IN ('buy', 'sell')),
        created_at TEXT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )"
];

foreach ($queries as $query) {
    $pdo->exec($query);
}

// Insert demo user
$pdo->exec("INSERT INTO users (name) VALUES ('Demo User')");

echo "Database setup complete.\n";
