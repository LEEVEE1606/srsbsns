<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

// Load environment variables
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');

// Test password hashing
$password = 'api123';
$hash = '$2y$10$uYDq4Zm2JFY3KjSLOl/uMOoSCvHZuG3mJ8hcuvyjk6jVLxDdK1df.';

echo "Testing password verification:\n";
echo "Password: $password\n";
echo "Hash: $hash\n";
echo "Verification result: " . (password_verify($password, $hash) ? 'TRUE' : 'FALSE') . "\n";

// Generate a new hash with different cost
$newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);
echo "New hash (cost 13): $newHash\n";
echo "Verification with new hash: " . (password_verify($password, $newHash) ? 'TRUE' : 'FALSE') . "\n"; 