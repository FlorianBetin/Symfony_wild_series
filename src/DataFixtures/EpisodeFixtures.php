<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Season;

//Tout d'abord nous ajoutons la classe Factory de FakerPhp
use App\Entity\Episode;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{

    public static int $episodeIndex = 0;
    public function load(ObjectManager $manager): void
    {
        //Puis ici nous demandons à la Factory de nous fournir un Faker
        $faker = Factory::create();

        /**
        * L'objet $faker que tu récupère est l'outil qui va te permettre 
        * de te générer toutes les données que tu souhaites
        */
for ($j = 0; $j < SeasonFixtures::$seasonIndex; $j++) {
        for($i = 0; $i < 10; $i++) {
            $episode = new Episode();
            //Ce Faker va nous permettre d'alimenter l'instance de Season que l'on souhaite ajouter en base
            $episode->setNumber($i + 1);
            $episode->setTitle($faker->title());
            $episode->setSynopsis($faker->paragraphs(1, true));

            $episode->setSeason($this->getReference('season_' . $j));

            $manager->persist($episode);
            $this->addReference('episode_' . self::$episodeIndex, $episode);
            self::$episodeIndex++;
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