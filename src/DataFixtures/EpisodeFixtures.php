<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Season;

//Tout d'abord nous ajoutons la classe Factory de FakerPhp
use App\Entity\Episode;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private SluggerInterface $slugger)
    {
    }
    
    public static int $episodeIndex = 0;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

for ($j = 0; $j < SeasonFixtures::$seasonIndex; $j++) {
        for($i = 0; $i < 10; $i++) {
            $episode = new Episode();
            $episode->setNumber($i + 1);
            $episode->setTitle($faker->title());
            $episode->setSynopsis($faker->paragraphs(1, true));
            $episode->setDuration($faker->numberBetween(12, 99));
            $episode->setSlug($this->slugger->slug($episode->getTitle()));
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
           SeasonFixtures::class,
        ];
    }
}