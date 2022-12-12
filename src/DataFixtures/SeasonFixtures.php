<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Persistence\ObjectManager;

//Tout d'abord nous ajoutons la classe Factory de FakerPhp
use Faker\Factory;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{

    public static int $seasonIndex = 0;
    public function load(ObjectManager $manager): void
    {
        //Puis ici nous demandons à la Factory de nous fournir un Faker
        $faker = Factory::create();

        /**
        * L'objet $faker que tu récupère est l'outil qui va te permettre 
        * de te générer toutes les données que tu souhaites
        */
for ($j = 0; $j < ProgramFixtures::$programIndex; $j++) {
        for($i = 0; $i < 5; $i++) {
            $season = new Season();
            $season->setNumber($i + 1);
            $season->setYear($faker->year());
            $season->setDescription($faker->paragraphs(3, true));

            $season->setProgram($this->getReference('program_' . $j)); 
            $manager->persist($season);
            $this->addReference('season_' . self::$seasonIndex, $season);
            self::$seasonIndex++;
        }
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
