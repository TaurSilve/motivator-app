<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Role>
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function getByUuid(string $uuid): ?Role
    {
        if (null === $uuid) {
            throw new Exception('The id is required.');
        }

        return $this->createQueryBuilder('role')
            ->andWhere('role.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByName(string $name): ?Role
    {
        if (null === $name) {
            throw new Exception('The name is required.');
        }

        return $this->createQueryBuilder('role')
            ->andWhere('role.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function updateUser(Role $role): void
    {
        try {
            $this->persist($role);
        } catch (Exception $e) {
            throw new Exception('There is some error douring the save the user info. User Id: ' . $role->getUuid() . ' ' . $e->getMessage());
        }
    }

    public function createRole($name, $discription): void
    {
        $userUuid = Uuid::uuid4();

        $userEntity = new Role();
        $userEntity->setUuid($userUuid);
        $userEntity->setName($name);
        $userEntity->setDescription($discription);

        try {
            $this->persist($userEntity);
        } catch (Exception $e) {
            throw new Exception('There is some error with create of the new role. ' . $e->getMessage());
        }
    }
}
