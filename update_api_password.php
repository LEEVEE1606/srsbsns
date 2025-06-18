<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel;
use App\Kernel as AppKernel;

// Load environment variables
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');

// Create kernel and boot it
$kernel = new AppKernel('dev', true);
$kernel->boot();

// Get the container
$container = $kernel->getContainer();

// Get the password hasher
$passwordHasher = $container->get('security.user_password_hasher');

// Create a temporary user object to hash the password
$user = new \App\Entity\User();
$user->setEmail('apiuser@srsbsns.com');

// Hash the password
$hashedPassword = $passwordHasher->hashPassword($user, 'api123');

echo "Generated hash: " . $hashedPassword . "\n";

// Update the database
$entityManager = $container->get('doctrine')->getManager();
$connection = $entityManager->getConnection();

$sql = "UPDATE users SET password = ? WHERE email = 'apiuser@srsbsns.com'";
$stmt = $connection->prepare($sql);
$result = $stmt->executeStatement([$hashedPassword]);

if ($result) {
    echo "Password updated successfully!\n";
} else {
    echo "Failed to update password.\n";
}

$kernel->shutdown(); 