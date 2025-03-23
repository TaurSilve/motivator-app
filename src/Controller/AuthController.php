<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\AuthenticationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;
    private PasswordHasherFactoryInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private AuthenticationService $authenticationService;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        PasswordHasherFactoryInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        AuthenticationService $authenticationService,
    ) {
        $this->jwtManager = $jwtManager;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->authenticationService = $authenticationService;
    }

    #[Route('/api/login_check', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Invalid credentials'], 400);
        }

        $user = $this->userRepository->getByEmail($email);
        if (!$user) {
            return new JsonResponse(['error' => 'There is no user found!'], 404);
        }

        $isValid = $this->authenticationService->verifyPassword($user, $password);
        if (!$isValid) {
            return new JsonResponse(['error' => 'Invalid credentials'], 400);
        }

        if (!$user || !$this->passwordHasher->getPasswordHasher($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        $token = $this->jwtManager->create($user);

        return new JsonResponse(['token' => $token]);
    }
}
