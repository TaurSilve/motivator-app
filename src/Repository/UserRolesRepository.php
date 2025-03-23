<?php

namespace App\Repository;

use App\Entity\UserRoles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use App\Repository\RoleRepository;
use Ramsey\Uuid\Validator\GenericValidator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Service\DatabaseService;

/**
 * @extends ServiceEntityRepository<UserRoles>
 */
class UserRolesRepository extends ServiceEntityRepository
{
    private RoleRepository $roleRepository;
    private GenericValidator $validator;
    private DatabaseService $dbService;

    public function __construct(
        ManagerRegistry $registry,
        RoleRepository $roleRepository,
        GenericValidator $validator,
        DatabaseService $dbService,
    ) {
        parent::__construct($registry, UserRoles::class);

        $this->roleRepository = $roleRepository;
        $this->validator = $validator;
        $this->dbService = $dbService;
    }

    public function getUserRoles(string $userUuid): array
    {
        if (null === $userUuid) {
            throw new Exception('The id is required.');
        }

        if (!$this->validator->validate($userUuid)) {
            throw new Exception('Please provide valid Uuid.');
        }

        $userRoles = $this->createQueryBuilder('user_roles')
            ->andWhere('user_roles.userUuid = :userUuid')
            ->setParameter('userUuid', $userUuid)
            ->getQuery()
            ->getResult();

        if (null === $userRoles) {
            throw new NotFoundHttpException('No roles found for this user.');
        }

        $roles = [];
        foreach ($userRoles as $key => $role) {
            $userRole = $this->roleRepository->getByUuid($role->getRoleUuid());
            $roles[$key]['name'] = $userRole->getName();
        }

        return $roles;
    }

    public function saveUserRole(string $userUuid, array $userRoles): void
    {
        if (empty($userRoles)) {
            throw new Exception('Please provide the roles.');
        }

        if (null === $userUuid) {
            throw new Exception('Please provide the user uuid.');
        }

        if (!$this->validator->validate($userUuid)) {
            throw new Exception('Please provide the valid user uuid.');
        }

        foreach ($userRoles as $role) {
            $userRoles = new UserRoles();
            $userRoles->setUserUuid($userUuid);
            $userRoles->setRoleUuid($role);

            $entityManager = $this->dbService->getEntityManger();
            $entityManager->persist($userRoles);
            $entityManager->flush();
        }
    }
}
