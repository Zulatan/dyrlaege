<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $users = [
            $this->getReference(UserFixtures::USER_REFERENCE, User::class),
            $this->getReference(UserFixtures::ADMIN_REFERENCE, User::class),
            $this->getReference(UserFixtures::VET_REFERENCE, User::class),
        ];

        $priorities = [
            TaskPriority::Minor,
            TaskPriority::Major,
            TaskPriority::Moderate,
        ];

        for ($i = 0; $i < 3; $i++) {
            $task = new Task();
            $task->setTitle("Task {$i}");
            $task->setDescription("Beskrivelse {$i}");
            $task->setStatus(TaskStatus::TODO);
            $task->setPriority($priorities[$i]);

            // Sæt relation direkte
            $task->setAssignedTo($users[$i]);

            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}