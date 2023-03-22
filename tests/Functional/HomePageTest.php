<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $button = $crawler->filter('.btn.btn-primary.btn-lg');
        $this->assertEquals(1, count($button));

        $series = $crawler->filter('.series .card');
        $this->assertEquals(3, count($series));

        $this->assertSelectorTextContains('h1', 'Bienvenue sur ComicsStorage');
    }
}
