<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Enum\RolesEnum;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\AuthenticationService;
use App\Repository\UserRepository;

class AccessMiddleware extends AbstractController
{
  private JWTTokenManagerInterface $jwtManager;
  private UserProviderInterface $userProvider;
  private AuthenticationService $authService;
  private UserRepository $userRepository;
  const POSITION_FOR_SUBSTRING = 7;

  public function __construct(
    JWTTokenManagerInterface $jwtManager,
    UserProviderInterface $userProvider,
    AuthenticationService $authService,
    UserRepository $userRepository,
  ) {
    $this->jwtManager = $jwtManager;
    $this->userProvider = $userProvider;
    $this->authService = $authService;
    $this->userRepository = $userRepository;
  }

  #[AsEventListener(event: 'kernel.request', priority: 10)]
  public function isAuthorizedRequest(RequestEvent $event)
  {
    $request = $event->getRequest();
    $path = $request->getPathInfo();

    $excludedPaths = [
      '/api/login_check' // TO DO: move to the propper place
    ];

    if (in_array($path, $excludedPaths, true)) {
      return;
    }

    $token = $request->headers->get('Authorization');
    $authHeader = $request->headers->get('Authorization');
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
      throw new UnauthorizedHttpException('Bearer', 'JWT Token not found');
    }

    $userUuid = $this->jwtManager->parse(substr($token, self::POSITION_FOR_SUBSTRING))['username'];
    if (!$userUuid) {
      throw $this->createAccessDeniedException('Request Denied!');
    }

    $user = $this->userRepository->getByUuid($userUuid);
    $this->authService->hasRoles([RolesEnum::REGULAR_USER->value], $user);
  }
}
