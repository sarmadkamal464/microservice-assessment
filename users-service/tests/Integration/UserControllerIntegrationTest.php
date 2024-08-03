<?php
namespace App\Tests\Integration;

use App\Entity\User;
use App\Message\UserCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Symfony\Component\Messenger\Envelope;

class UserControllerIntegrationTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->clearDatabase();
    }

    private function clearDatabase(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    public function testCreateUser(): void
    {
        $userData = [
            'email' => 'test@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
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

        // Check if the user was persisted in the database
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
        $this->assertNotNull($user);
        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());

        // Check if the message was dispatched
        /** @var InMemoryTransport $transport */
        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $this->assertCount(1, $transport->getSent());

        /** @var Envelope $envelope */
        $envelope = $transport->get()[0];
        $message = $envelope->getMessage();

        $this->assertInstanceOf(UserCreatedEvent::class, $message);
        $this->assertEquals($user->getId(), $message->getId());
        $this->assertEquals('test@example.com', $message->getEmail());
    }

    public function testCreateUserWithInvalidData(): void
    {
        $userData = [
            'email' => 'invalid-email',
            'firstName' => '',
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

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}

