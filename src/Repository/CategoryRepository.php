<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\DatabaseService;
use Exception;
use Ramsey\Uuid\Uuid;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    protected DatabaseService $dbService;

    public function __construct(ManagerRegistry $registry, DatabaseService $dbService)
    {
        parent::__construct($registry, Category::class);

        $this->dbService = $dbService;
    }

    public function getByUuid(string $uuid): ?Category
    {
        if (null === $uuid) {
            throw new Exception('The id is required.');
        }

        return $this->createQueryBuilder('category')
            ->andWhere('category.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function updateCategory(Category $category): void
    {
        try {
            $this->persist($category);
        } catch (Exception $e) {
            throw new Exception('There is some error douring the save the user info. User Id: ' . $category->getUuid() . ' ' . $e->getMessage());
        }
    }

    public function createCategory(string $name, string $discription): string
    {
        $categoryUuid = Uuid::uuid4()->toString();
        $categoryEntity = new Category();
        $categoryEntity->setName($name);
        $categoryEntity->setDescription($discription);
        $categoryEntity->setUuid($categoryUuid);

        try {
            $entityManager = $this->dbService->getEntityManger();
            $entityManager->persist($categoryEntity);
            $entityManager->flush();
        } catch (Exception $e) {
            throw new Exception('There is some error with create of the new category. ' . $e->getMessage());
        }

        return $categoryUuid;
    }
}
