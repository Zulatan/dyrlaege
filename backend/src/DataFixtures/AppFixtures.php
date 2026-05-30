<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\User;
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
    
        // create 20 products with random prices
        for ($i = 0; $i < 5; $i++) {

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

        $manager->flush();
    }
}
