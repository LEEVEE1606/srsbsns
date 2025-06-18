<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:update-api-password',
    description: 'Update API user password',
)]
class UpdateApiPasswordCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'apiuser@srsbsns.com']);
        
        if (!$user) {
            $output->writeln('<error>API user not found!</error>');
            return Command::FAILURE;
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'api123');
        $user->setPassword($hashedPassword);
        
        $this->entityManager->flush();
        
        $output->writeln('<info>API user password updated successfully!</info>');
        $output->writeln('Email: apiuser@srsbsns.com');
        $output->writeln('Password: api123');
        
        return Command::SUCCESS;
    }
} 