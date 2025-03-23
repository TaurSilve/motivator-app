<?php

namespace App\Repository;

use App\Entity\UserCategory;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;
use Ramsey\Uuid\Validator\GenericValidator;

/**
 * @extends ServiceEntityRepository<UserCategory>
 */
class UserCategoryRepository extends ServiceEntityRepository
{
    private GenericValidator $validator;
    private CategoryRepository $categoryRepository;

    public function __construct(ManagerRegistry $registry, GenericValidator $validator, CategoryRepository $categoryRepository)
    {
        parent::__construct($registry, UserCategory::class);

        $this->validator = $validator;
        $this->categoryRepository = $categoryRepository;
    }

    public function getUserCategoryes(string $userUuid): array
    {
        if (null === $userUuid) {
            throw new Exception('The user uuid is required.');
        }

        if (!$this->validator->validate($userUuid)) {
            throw new Exception('Please provide valid Uuid.');
        }

        $userCategory = $this->createQueryBuilder('user_category')
            ->andWhere('user_category.userUuid = :userUuid')
            ->setParameter('userUuid', $userUuid)
            ->getQuery()
            ->getResult();

        if (null === $userCategory) {
            throw new NotFoundHttpException('No categories found for this user.');
        }

        $categoryes = [];
        foreach ($userCategory as $key => $category) {
            $categoryEntity = $this->categoryRepository->getByUuid($category->getCategoryUuid());
            $categoryes[] = $categoryEntity->getName();
        }

        return $categoryes;
    }

    public function saveUserCategory(string $userUuid, string $categoryUuid): void
    {
        if (empty($userCategory)) {
            throw new Exception('Please provide the category.');
        }

        if (null === $userUuid) {
            throw new Exception('Please provide the user uuid.');
        }

        if (!$this->validator->validate($userUuid)) {
            throw new Exception('Please provide the valid user uuid.');
        }

        $userCategory = new UserCategory();
        $userCategory->setUserUuid($userUuid);
        $userCategory->setCategoryUuid($categoryUuid);

        $entityManager = $this->dbService->getEntityManger();
        $entityManager->persist($userCategory);
        $entityManager->flush();
    }
}
