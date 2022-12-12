<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{

    public static int $actorIndex = 0;

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();


            for($i = 0; $i < 10; $i++) {  
            $actor = new Actor();  
            $actor->setName($faker->name());
            for ($j = 0; $j < 3; $j++){
            $actor->addProgram($this->getReference('program_' . $j));
        }
            $manager->persist($actor);
            // Pas compris ça
            // $this->addReference('actor_' . self::$actorIndex, $actor); 
            self::$actorIndex++; 
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
           ProgramFixtures::class,
        ];
    }
}