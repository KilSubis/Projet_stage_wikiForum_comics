<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Mark;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Comics;
use App\Entity\Series;
use App\Entity\Contact;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


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

    public function load(ObjectManager $manager ): void
    {
        
        // Users 
        $users = [];

        $admin = new User();
        $admin->setFullName('Administrateur de ComicsStorage')
             ->setPseudo(null)
             ->setEmail('admin@comicsstorage.fr')
             ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
             ->setPlainPassword('password');

             $users[] = $admin;
             $manager->persist($admin);


        for ($i=0; $i < 10; $i++) { 
            $user = new User();
            $user->setFullName($this->faker->name())
                 ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                 ->setEmail($this->faker->email())
                 ->setRoles(['ROLE_USER'])
                 ->setPlainPassword('password');
 
                 $users[] = $user;
                 $manager->persist($user);
                 $manager->flush();

        }

        // Comics
        $comics = [];
        for ($i = 0; $i < 50; $i++) { 
            $comic = new Comics();
            $comic->setNom($this->faker->word())
                  ->setPrix(mt_rand(0, 100))
                  ->setUser($users[mt_rand(0, count($users) - 1)]);

            $comics[] = $comic;   
            $manager->persist($comic);   
        }

        // Series
        $series = [];
        for ($j = 0; $j < 25; $j++) { 
            $serie = new Series();
            $serie->setNom($this->faker->word())
                  ->setAnnee(mt_rand(1903, 2023))
                  ->setNbComics(mt_rand(0, 1000))
                  ->setDescription($this->faker->text(300))
                  ->setisFavorite(mt_rand(0, 1) == 1 ? true : false)
                  ->setisPublic(mt_rand(0, 1) == 1 ? true : false)
                  ->setUser($users[mt_rand(0, count($users) - 1)]);

                  for ($k=0; $k < mt_rand(5, 15); $k++) { 
                    $serie->addComic($comics[mt_rand(0, count($comics) - 1)]);
                  }

                  $series[] = $serie;
                  $manager->persist($serie);
        }

        //Marks
        foreach ($series as $serie ) {
            for ($i = 0; $i < mt_rand(1, 4); $i++) {
                $mark = new Mark();
                $mark->setMark(mt_rand(1, 5))
                     ->setUser($users[mt_rand(0, count($users) - 1)])
                     ->setSeries($serie);

                $manager->persist($mark);
            }
        }
        
       //Contact
       for ($i=0; $i < 5; $i++) { 
         $contact = new Contact();
         $contact->setFullName($this->faker->name())
                 ->setEmail($this->faker->email())
                 ->setSubject('Demande n°' . ($i +1))
                 ->setMessage($this->faker->text());
         
                 $manager->persist($contact);
       }

        $manager->flush();
    }
        
    
}
