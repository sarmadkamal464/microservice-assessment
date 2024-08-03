<?php

namespace App\Controller;

use App\Entity\User;
use App\Message\UserCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends AbstractController
{
    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function createUser(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        MessageBusInterface $messageBus
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            throw new BadRequestHttpException('Invalid JSON');
        }

        $user = new User();
        $user->setEmail($data['email'] ?? '')
            ->setFirstName($data['firstName'] ?? '')
            ->setLastName($data['lastName'] ?? '');

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            return $this->json(['errors' => ['email' => 'This email is already in use.']], 400);
        }

        $messageBus->dispatch(new UserCreatedEvent($user->getId(), $user->getEmail(), $user->getFirstName(), $user->getLastName()));

        return $this->json(['message' => 'User created successfully', 'id' => $user->getId()], 201);
    }
}