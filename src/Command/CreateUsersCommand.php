<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-users',
    description: 'Create default admin and API users',
)]
class CreateUsersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Create Admin User
        $adminUser = new User();
        $adminUser->setEmail('admin@srsbsns.com');
        $adminUser->setFirstName('Admin');
        $adminUser->setLastName('User');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setIsActive(true);
        
        $hashedPassword = $this->passwordHasher->hashPassword($adminUser, 'admin123');
        $adminUser->setPassword($hashedPassword);

        // Create API User
        $apiUser = new User();
        $apiUser->setEmail('apiuser@srsbsns.com');
        $apiUser->setFirstName('API');
        $apiUser->setLastName('User');
        $apiUser->setRoles(['ROLE_API_USER']);
        $apiUser->setIsActive(true);
        
        $hashedApiPassword = $this->passwordHasher->hashPassword($apiUser, 'api123');
        $apiUser->setPassword($hashedApiPassword);

        // Check if users already exist
        $existingAdmin = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@srsbsns.com']);
        $existingApi = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'apiuser@srsbsns.com']);

        if (!$existingAdmin) {
            $this->entityManager->persist($adminUser);
            $io->success('Admin user created: admin@srsbsns.com / admin123');
        } else {
            $io->warning('Admin user already exists: admin@srsbsns.com');
        }

        if (!$existingApi) {
            $this->entityManager->persist($apiUser);
            $io->success('API user created: apiuser@srsbsns.com / api123');
        } else {
            $io->warning('API user already exists: apiuser@srsbsns.com');
        }

        $this->entityManager->flush();

        $io->success('User creation completed!');
        $io->note('You can now use these credentials:');
        $io->table(
            ['Type', 'Email', 'Password', 'Roles'],
            [
                ['Admin', 'admin@srsbsns.com', 'admin123', 'ROLE_ADMIN'],
                ['API', 'apiuser@srsbsns.com', 'api123', 'ROLE_API_USER'],
            ]
        );

        return Command::SUCCESS;
    }
} 