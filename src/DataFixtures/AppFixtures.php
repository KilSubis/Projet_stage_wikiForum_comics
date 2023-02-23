<?php

namespace App\DataFixtures;

use App\Entity\Comics;
use App\Entity\Series;
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
        // Comics
        $comics = [];
        for ($i = 0; $i < 50; $i++) { 
            $comic = new Comics();
            $comic->setNom($this->faker->word())
                  ->setPrix(mt_rand(0, 100));

            $comics[] = $comic;   
            $manager->persist($comic);   
        }

        // Series
        for ($j = 0; $j < 25; $j++) { 
            $serie = new Series();
            $serie->setNom($this->faker->word())
                  ->setAnnée(mt_rand(1903, 2023))
                  ->setNbComics(mt_rand(0, 1000))
                  ->setDescription($this->faker->text(300))
                  ->setIsFavorite(mt_rand(0,1) == 1 ? true : false);

                  for ($k=0; $k < mt_rand(5, 15); $k++) { 
                    $serie->addComic($comics[mt_rand(0, count($comics) - 1)]);
                  }

                  $manager->persist($serie);
        }
        
        $manager->flush();
    }
}
