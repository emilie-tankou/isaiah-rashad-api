<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin', description: 'Crée un compte administrateur')]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $users,
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, "Email de l'administrateur");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = (string) $input->getArgument('email');

        if ($this->users->findOneBy(['email' => $email])) {
            $io->error('Un utilisateur avec cet email existe déjà.');

            return Command::FAILURE;
        }

        // askHidden : le mot de passe ne s'affiche pas et n'est pas gardé dans l'historique du terminal
        $password = (string) $io->askHidden('Mot de passe (12 caractères minimum)');

        if (strlen($password) < 12) {
            $io->error('Mot de passe trop court.');

            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        // On ne stocke JAMAIS le mot de passe en clair : seulement son hash
        $user->setPassword($this->hasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $io->success("Admin créé : $email");

        return Command::SUCCESS;
    }
}