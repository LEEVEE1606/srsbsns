<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:test-auth',
    description: 'Test user authentication',
)]
class TestAuthCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'User email');
        $this->addArgument('password', InputArgument::REQUIRED, 'User password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        // Find user by email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        
        if (!$user) {
            $io->error("User with email '{$email}' not found!");
            return Command::FAILURE;
        }

        $io->success("User found: {$user->getFullName()} ({$user->getEmail()})");
        $io->table(['Property', 'Value'], [
            ['ID', $user->getId()],
            ['Email', $user->getEmail()],
            ['Active', $user->isActive() ? 'Yes' : 'No'],
            ['Roles', implode(', ', $user->getRoles())],
            ['Password Hash', substr($user->getPassword(), 0, 50) . '...'],
        ]);

        // Test password verification
        $isValid = $this->passwordHasher->isPasswordValid($user, $password);
        
        if ($isValid) {
            $io->success("Password is VALID!");
        } else {
            $io->error("Password is INVALID!");
        }

        return Command::SUCCESS;
    }
} 