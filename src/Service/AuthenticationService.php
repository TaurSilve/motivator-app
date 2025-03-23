<?php

namespace App\Service;

use App\Repository\RoleRepository;
use App\Repository\UserRolesRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Exception;
use App\Entity\User;

class AuthenticationService
{
  private UserRolesRepository $userRoleRepository;
  private RoleRepository $roleRepository;
  private UserPasswordHasherInterface $passwordHasher;

  public function __construct(
    UserRolesRepository $userRoleRepository,
    RoleRepository $roleRepository,
    UserPasswordHasherInterface $passwordHasher,
  ) {
    $this->userRoleRepository = $userRoleRepository;
    $this->roleRepository = $roleRepository;
    $this->passwordHasher = $passwordHasher;
  }

  public function hasRoles(array $requestedRoles, User $user): bool
  {
    if (null === $requestedRoles || empty($requestedRoles)) {
      throw new Exception('Please provide requested roles.');
    }

    if (null === $user) {
      throw new Exception('Please provide valid user.');
    }

    $userUuid = $user->getUuid();
    $roles = $this->userRoleRepository->getUserRoles($userUuid);
    $userRoles = [];
    foreach ($roles as $role) {
      $role = $this->roleRepository->getByName($role['name']);
      $userRoles[] = $role->getName();
    }

    if (!empty(array_diff($requestedRoles, $userRoles))) {
      throw new Exception('You dont have right permission.');
    }

    return true;
  }

  public function hashPassword(PasswordAuthenticatedUserInterface $user, string $plainPassword): string
  {
    return $this->passwordHasher->hashPassword($user, $plainPassword);
  }

  public function verifyPassword(PasswordAuthenticatedUserInterface $user, string $hashedPassword): bool
  {
    return $this->passwordHasher->isPasswordValid($user, $hashedPassword);
  }
}
