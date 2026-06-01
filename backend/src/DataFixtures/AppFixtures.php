<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\User;
use App\Enum\UserRole;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
    
        // create 2 users with random values
        for ($i = 0; $i < 2; $i++) {

            // Users for testing
            $user = new User();
            $user->setEmail($i . 'email@test.dk');

            $password = $this->hasher->hashPassword($user, 'pass_1234');
            $user->setPassword($password);

            $task = new Task();
            $task->setTitle('Ny task nr.:' . $i);
            $task->setDescription('Beskrivelse af task nr.:' . $i);
            $task->setPriority(TaskPriority::Minor);
            $task->setStatus(TaskStatus::TODO);
            $task->setAssignedTo($user->getId());

            $manager->persist($user);
            $manager->persist($task);
        }
        $password = $this->hasher->hashPassword($user, 'pass_1234');

        // create users with roles
        $vet = new User();
        $vet->setEmail('vet@test.dk');
        $vet->setRoles([UserRole::VET->value]);
        $vet->setPassword($password);

        $admin = new User();
        $admin->setEmail('admin@test.dk');
        $admin->setRoles([UserRole::ADMIN->value]);
        $admin->setPassword($password);

        $manager->persist($vet);
        $manager->persist($admin);

        $manager->flush();
    }
}
