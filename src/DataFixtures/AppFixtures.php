<?php

namespace App\DataFixtures;

use App\Entity\Comics;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{

    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 50; $i++) { 
            $comics = new Comics();
            $comics->setNom($this->faker->word())
                ->setPrix(mt_rand(0, 100));

            $manager->persist($comics);   
        }

        
        
        $manager->flush();
    }
}
