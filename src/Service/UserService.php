<?php

namespace App\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserService
{
  private UserPasswordHasherInterface $passwordHasher;

  public function __construct(UserPasswordHasherInterface $passwordHasher)
  {
    $this->passwordHasher = $passwordHasher;
  }

  public function hashPassword(PasswordAuthenticatedUserInterface $user, string $plainPassword): string
  {

    return '';
  }

  public function verifyPassword(PasswordAuthenticatedUserInterface $user, string $plainPassword, string $hashedPassword): bool
  {
    return $this->passwordHasher->isPasswordValid($user, $plainPassword, $hashedPassword);
  }
}
