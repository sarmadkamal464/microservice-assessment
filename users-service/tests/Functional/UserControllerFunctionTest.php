<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Envelope;

class UserControllerFunctionalTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $bus;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->bus = $this->client->getContainer()->get('messenger.default_bus');
        $this->clearDatabase();
    }

    private function clearDatabase(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    public function testCreateUserFlow(): void
    {
        $userData = [
            'email' => 'functional@example.com',
            'firstName' => 'Functional',
            'lastName' => 'Test',
        ];

        $this->client->request(
            'POST',
            '/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('message', $responseData);

        // Verify user in database
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'functional@example.com']);
        $this->assertNotNull($user);
        $this->assertEquals('Functional', $user->getFirstName());
        $this->assertEquals('Test', $user->getLastName());

        // Verify message dispatch
        /** @var InMemoryTransport $transport */
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $this->assertCount(1, $transport->getSent());

        /** @var Envelope $envelope */
        $envelope = $transport->get()[0];
        // Here you can further assert on the message contents if needed
    }

    public function testCreateUserWithExistingEmail(): void
    {
        // Create a user first
        $existingUser = new User();
        $existingUser->setEmail('existing@example.com')
            ->setFirstName('Existing')
            ->setLastName('User');
        $this->entityManager->persist($existingUser);
        $this->entityManager->flush();

        // Try to create another user with the same email
        $userData = [
            'email' => 'existing@example.com',
            'firstName' => 'Another',
            'lastName' => 'User',
        ];

        $this->client->request(
            'POST',
            '/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $errorResponse = json_decode($this->client->getResponse()->getContent())->errors;
        $this->assertEquals('This email is already in use.', 
            $errorResponse->email);

    }

    public function testCreateUserWithBlankFirstName(): void
    {

        // Try to create another user with the blank first name
        $userData = [
            'email' => 'random@example.com',
            'firstName' => '',
            'lastName' => 'User',
        ];

        $this->client->request(
            'POST',
            '/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $errorResponse = json_decode($this->client->getResponse()->getContent())->errors;
        $this->assertEquals('First name should not be blank.', 
            $errorResponse->firstName);

    }    

    public function testCreateUserWithBlankLastName(): void
    {

        // Try to create another user with the blank last name
        $userData = [
            'email' => 'random@example.com',
            'firstName' => 'Another',
            'lastName' => '',
        ];

        $this->client->request(
            'POST',
            '/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $errorResponse = json_decode($this->client->getResponse()->getContent())->errors;
        $this->assertEquals('Last name should not be blank.', 
            $errorResponse->lastName);

    }    


    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
