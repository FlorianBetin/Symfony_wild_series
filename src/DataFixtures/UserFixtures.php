<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public static int $userIndex = 0;

    public const USER_INFOS = [
        [
            'email' => 'contributor@monsite.com',
            'pass' => 'mdp',
            'role' => 'ROLE_CONTRIBUTOR'
        ],
        [
            'email' => 'contributor2@monsite.com',
            'pass' => 'mdp',
            'role' => 'ROLE_CONTRIBUTOR'
        ],
        [
            'email' => 'admin@monsite',
            'pass' => 'admin',
            'role' => 'ROLE_ADMIN',
        ]
    ];


    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // // Création d’un utilisateur de type “contributeur” (= auteur)
        // $contributor = new User();
        // $contributor->setEmail('contributor@monsite.com');
        // $contributor->setRoles(['ROLE_CONTRIBUTOR']);
        // $hashedPassword = $this->passwordHasher->hashPassword(
        //     $contributor,
        //     'contributorpassword'
        // );

        // $contributor->setPassword($hashedPassword);
        // $manager->persist($contributor);

        // // Création d’un utilisateur de type “administrateur”
        // $admin = new User();
        // $admin->setEmail('admin@monsite.com');
        // $admin->setRoles(['ROLE_ADMIN']);
        // $hashedPassword = $this->passwordHasher->hashPassword(
        //     $admin,
        //     'adminpassword'
        // );
        // $admin->setPassword($hashedPassword);
        // $manager->persist($admin);

        foreach (self::USER_INFOS as $userInfo) {
            self::$userIndex++;
            $user = new User();
            $user->setEmail($userInfo['email']);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $userInfo['pass']
            );
            $user->setPassword($hashedPassword);

            $user->setRoles(array($userInfo['role']));
            $manager->persist($user);
            $this->addReference('user_' . self::$userIndex, $user);
        }

        // Sauvegarde des 2 nouveaux utilisateurs :
        $manager->flush();
    }
}
