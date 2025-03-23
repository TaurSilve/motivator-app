<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
  public function onAuthenticationSuccess(Request $request, $token): JsonResponse
  {
    /** @var UserInterface $user */
    $user = $token->getUser();
    return new JsonResponse([
      'message' => 'Authentication successful',
      'user' => [
        'id' => $user->getUud(),
        'username' => $user->getUsername(),
      ],
    ]);
  }
}
