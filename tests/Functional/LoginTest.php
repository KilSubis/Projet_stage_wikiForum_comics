<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginTest extends WebTestCase
{
    public function testIfLoginIsSuccessfull(): void
    {
        $client = static::createClient();
        
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $urlGenerator->generate('security.login'));

        

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@comicsstorage.fr",
            "_password" => "password"
        ]);

       $client->submit($form);

       $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

       $client->followRedirect();

       $this->assertRouteSame('home.index');
    }

    public function testIfLoginFailWhenPasswordIsWrong(): void
    {

        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $urlGenerator->generate('security.login'));

        

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@comicsstorage.fr",
            "_password" => "password_"
        ]);

       $client->submit($form);

       $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

       $client->followRedirect();

       $this->assertRouteSame('security.login');

       $this->assertSelectorTextContains("div.alert-dismissible.alert-warning", "Invalid credentials");
    
    }
}
