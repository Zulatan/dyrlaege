<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user';
    public const ADMIN_REFERENCE = 'admin';
    public const VET_REFERENCE = 'vet';

    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('user@test.dk');
        $user->setRoles([UserRole::RECEPTIONIST->value]);
        $user->setPassword(
            $this->hasher->hashPassword($user, 'pass123')
        );

        $admin = new User();
        $admin->setEmail('admin@test.dk');
        $admin->setRoles([UserRole::ADMIN->value]);
        $admin->setPassword(
            $this->hasher->hashPassword($admin, 'pass123')
        );

        $vet = new User();
        $vet->setEmail('vet@test.dk');
        $vet->setRoles([UserRole::VET->value]);
        $vet->setPassword(
            $this->hasher->hashPassword($vet, 'pass123')
        );

        $manager->persist($user);
        $manager->persist($admin);
        $manager->persist($vet);

        $manager->flush();

        // Save references. This is the way fixtures shares the user, so that it can be used in TaskFixtures.php
        $this->addReference(self::USER_REFERENCE, $user);
        $this->addReference(self::ADMIN_REFERENCE, $admin);
        $this->addReference(self::VET_REFERENCE, $vet);
    }
}