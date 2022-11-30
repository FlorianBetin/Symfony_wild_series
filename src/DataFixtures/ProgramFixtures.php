<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $program = new Program();
        $program->setTitle('Walking dead');
        $program->setSynopsis('Des zombies envahissent la terre');
        $program->setCategory($this->getReference('category_Horreur'));
        $program1 = new Program();
        $program1->setTitle('Mercredy');
        $program1->setSynopsis('Mercredy enquête sur d\'etranges meutres à l\'école de nevermore');
        $program1->setCategory($this->getReference('category_Fantastique'));
        $program2 = new Program();
        $program2->setTitle('Les anneaux de pouvoir');
        $program2->setSynopsis('La création du Mordor');
        $program2->setCategory($this->getReference('category_Aventure'));
        $program3 = new Program();
        $program3->setTitle('Blue Lock');
        $program3->setSynopsis('300 attaquants passent des épreuves extenuantes. Qui deviendra l\'attaquant du Japon ?');
        $program3->setCategory($this->getReference('category_Animation'));
        $program4 = new Program();
        $program4->setTitle('The Boys');
        $program4->setSynopsis('Des super Heroes, pas si héroïque que ça');
        $program4->setCategory($this->getReference('category_Action'));
        $manager->persist($program);
        $manager->persist($program1);
        $manager->persist($program2);
        $manager->persist($program3);
        $manager->persist($program4);
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