<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Repository\UserRolesRepository;
use Exception;
use Ramsey\Uuid\Validator\GenericValidator;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\RoleRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Service\AuthenticationService;
use App\Enum\RolesEnum;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

use function PHPSTORM_META\type;

class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private GenericValidator $validator;
    private UserRolesRepository $userRolesRepository;
    private RoleRepository $roleRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private AuthenticationService $authenticationService;
    private JWTTokenManagerInterface $JWTManager;

    public function __construct(
        UserRepository $userRepository,
        GenericValidator $validator,
        UserRolesRepository $userRolesRepository,
        RoleRepository $roleRepository,
        UserPasswordHasherInterface $passwordHasher,
        AuthenticationService $authenticationService,
        JWTTokenManagerInterface $JWTManager,
    ) {
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->userRolesRepository = $userRolesRepository;
        $this->roleRepository = $roleRepository;
        $this->passwordHasher = $passwordHasher;
        $this->authenticationService = $authenticationService;
        $this->JWTManager = $JWTManager;
    }

    #[Route('/User/{uuid}', name: 'get_user', methods: ['GET'])]
    public function getUserByUuid(string $uuid): JsonResponse
    {
        if (null === $uuid) {
            throw new Exception('The Uuid is required param.');
        }

        if (!$this->validator->validate($uuid)) {
            throw new Exception('Please provide valid uuid.');
        }

        $user = $this->userRepository->getByUuid($uuid);

        if (null === $user) {
            throw new NotFoundHttpException('Please provide valid uuid.');
        }

        $data = [];
        $data['name'] = $user->getName();
        $data['role'] = $this->userRolesRepository->getUserRoles($uuid);
        $data['description'] = $user->getDescription();

        return new JsonResponse($data);
    }

    #[Route('/User', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $roles = $data['roles'];
        $email = $data['email'];
        $password = $data['password'];

        if (empty($name) || gettype($name) !== 'string') {
            throw new Exception('Please provide valid user Name.');
        }

        if (empty($email) || gettype($email) !== 'string') {
            throw new Exception('Please provide valid user Email.');
        }

        if ($this->userRepository->getByEmail($email)) {
            throw new Exception('The user already exist!');
        }

        if (empty($roles)) {
            throw new Exception('Please provide valid user Roles.');
        }

        $rolesData = [];
        foreach ($roles as $role) {
            $role = $this->roleRepository->getByName($role);
            if (!$role) {
                throw new Exception('Please provide the valid user roles.');
            }
            $rolesData[] = $role->getUuid();
        }

        try {
            $userUuid = $this->userRepository->createUser($name, $email, $password);
            $this->userRolesRepository->saveUserRole($userUuid, $rolesData);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return new JsonResponse([$userUuid]);
    }
}
