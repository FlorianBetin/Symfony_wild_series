<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{

    public static int $commentIndex = 0;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($j = 0; $j < EpisodeFixtures::$episodeIndex; $j++) {
            for($i = 0; $i < 5; $i++) {
                $comment = new Comment();
                $comment->setComment($faker->paragraphs(2, true));
                $comment->setRate($faker->numberBetween(0,5));
                $comment->setEpisode($this->getReference('episode_' . $j));
                $comment->setAuthor($this->getReference('user_' . rand(1, 3)));
                $manager->persist($comment);
                $this->addReference('comment_' . self::$commentIndex, $comment);
                self::$commentIndex++;
            }
        }
    
            $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
           EpisodeFixtures::class,
           UserFixtures::class,
        ];
    }

}