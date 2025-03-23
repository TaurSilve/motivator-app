<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserCategoryRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Exception;
use App\Service\AuthenticationService;
use App\Enum\RolesEnum;
use Ramsey\Uuid\Uuid;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class CategoryController extends AbstractController
{
  private UserCategoryRepository $userCategoryRepository;
  private CategoryRepository $categoryRepository;
  private UserRepository $userRepository;
  private AuthenticationService $authenticationService;
  private JWTTokenManagerInterface $JWTManager;

  public function __construct(
    UserCategoryRepository $userCategoryRepository,
    CategoryRepository $categoryRepository,
    UserRepository $userRepository,
    AuthenticationService $authenticationService,
    JWTTokenManagerInterface $JWTManager,
  ) {
    $this->userCategoryRepository = $userCategoryRepository;
    $this->categoryRepository = $categoryRepository;
    $this->userRepository = $userRepository;
    $this->authenticationService = $authenticationService;
    $this->JWTManager = $JWTManager;
  }

  public function __contstract() {}

  #[Route('/Category', name: 'create_category', methods: ['POST'])]
  public function createCategory(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $name = $data['email'] ?? null;
    $description = $data['description'] ?? null;

    $data = json_decode($request->getContent(), true);

    $name = $data['name'];
    $description = $data['description'];

    if (empty($name) || gettype($name) !== 'string') {
      throw new Exception('Please provide valid category Name.');
    }

    try {
      $categoryUuid = $this->categoryRepository->createCategory($name, $description);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }

    return new JsonResponse([$categoryUuid]);
  }

  #[Route('/GetUserCategoryes/{uuid}', name: 'get_user_categoryes', methods: ['GET'])]
  public function getUserCategories(Request $request, string $uuid): JsonResponse
  {
    if (!Uuid::isValid($uuid)) {
      throw $this->createAccessDeniedException('Please provide valid uuid!');
    }

    return new JsonResponse($this->userCategoryRepository->getUserCategoryes($uuid));
  }
}
