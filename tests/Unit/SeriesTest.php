<?php

namespace App\Tests\Unit;

use App\Entity\Mark;
use App\Entity\User;
use App\Entity\Series;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SeriesTest extends KernelTestCase
{

    public function getEntity(): Series 
    {
        return (new Series())
        ->setNom('Series #1')
        ->setDescription('Description #1')
        ->setIsFavorite(true)
        ->setCreatedAt(new \DateTimeImmutable())
        ->setUpdatedAt(new \DateTimeImmutable());

    }

    public function testEntityIsValid(): void
    {
         self::bootKernel();
         $container = static::getContainer();

         $series = $this->getEntity();

         $errors = $container->get('validator')->validate($series);

         $this->assertCount(1, $errors);
    }

    public function testInvalidName()
    {
        self::bootKernel();
        $container = static::getContainer();

        $series = $this->getEntity();
        $series->setNom('');

            $errors = $container->get('validator')->validate($series);

         $this->assertCount(3, $errors);

    }

    public function testGetAverage() 
    {
        $series = $this->getEntity();
        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 1);

        for ($i=0; $i < 5; $i++) { 
            $mark = new Mark();
            $mark->setMark(2)
               ->setUser($user)
               ->setSeries($series);

            $series->addMark($mark);
        }

        $this->assertTrue(2.0 === $series->getAverage());
    }
}
