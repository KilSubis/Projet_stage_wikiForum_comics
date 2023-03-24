<?php

// namespace App\Tests\Functional;

// use App\Entity\User;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// class ContactTest extends WebTestCase
// {
//     public function testIfSubmitContactFormIsSuccessfull(): void
//     {
//         $client = static::createClient();
//         $crawler = $client->request('GET', '/contact');

//         $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

//         $user = $entityManager->find(User::class, 1);

//         $client->loginUser($user);

//         $this->assertResponseIsSuccessful();
//         $this->assertSelectorTextContains('h1', 'Formulaire de contact');

//         $submitButton = $crawler->selectButton('Envoyer');
//         $form = $submitButton->form();

//         $form["contact[fullName]"] = "Jean Dupont";
//         $form["contact[email]"] = "jd@symrecipe.com";
//         $form["contact[subject]"] = "Test";
//         $form["contact[message]"] = "Test";

//         // Soumettre le formulaire
//         $client->submit($form);

//         // Vérifier le statut HTTP
//         $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

//         // Vérifier l'envoie du mail
//         $this->assertEmailCount(1);

//         $client->followRedirect();

//         // Vérifier la présence du message de succès
//         $this->assertSelectorTextContains(
//             'div.alert.alert-success.mt-4',
//             'Votre demande a bien été prise en compte'
//         );
//     }
// }
