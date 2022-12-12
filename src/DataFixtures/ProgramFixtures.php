<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{

    public const PROGRAMLIST = [
        [
            "Title" => "Mercredy",
            "Synopsis" => 'Mercredy enquête sur d\'etranges meutres à l\'école de nevermore',
            "Category" => "category_Fantastique"
        ],
        [
            "Title" => "Walking dead",
            "Synopsis" => "Des zombies envahissent la terre",
            "Category" => "category_Horreur"
        ],
        [
            "Title" => "Les anneaux de pouvoir",
            "Synopsis" => "La création du Mordor",
            "Category" => "category_Aventure"
        ],
        [
            "Title" => "Blue Lock",
            "Synopsis" => "300 attaquants passent des épreuves extenuantes. Qui deviendra l\'attaquant du Japon ?",
            "Category" => "category_Animation"
        ],
        [
            "Title" => "The Boys",
            "Synopsis" => "Des super Heroes, pas si héroïque que ça",
            "Category" => "category_Action"
        ],


    ];

    public static int $programIndex = 0;

    public function load(ObjectManager $manager)
    {
        foreach (self::PROGRAMLIST as $key => $ProgramInfo) {
            $program = new Program();
            $program->setTitle($ProgramInfo["Title"]);
            $program->setSynopsis($ProgramInfo["Synopsis"]);
            $program->setCategory($this->getReference($ProgramInfo["Category"]));
            $manager->persist($program);
            $this->addReference('program_' . $key, $program);
            self::$programIndex++;
        }

        $manager->flush();
    }


    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
          CategoryFixtures::class,
        ];
    }


}