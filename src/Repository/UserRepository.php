<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use phpDocumentor\Reflection\Types\Integer;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\DatabaseService;
use App\Service\AuthenticationService;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    private DatabaseService $dbService;
    private AuthenticationService $authenticationService;
    public function __construct(
        ManagerRegistry $registry,
        DatabaseService $dbService,
        AuthenticationService $authenticationService,
    ) {
        parent::__construct($registry, User::class);

        $this->dbService = $dbService;
        $this->authenticationService = $authenticationService;
    }

    public function getByUuid(string $uuid): ?User
    {
        if (null === $uuid) {
            throw new Exception('The id is required.');
        }

        return $this->createQueryBuilder('user')
            ->andWhere('user.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByName(string $name): ?User
    {
        if (null === $name) {
            throw new Exception('The name is required.');
        }

        return $this->createQueryBuilder('user')
            ->andWhere('user.username = :username')
            ->setParameter('username', $name)
            ->getQuery()
            ->getResult();
    }

    public function getByEmail(string $email): ?User
    {
        if (null === $email) {
            throw new Exception('The email is required.');
        }

        return $this->createQueryBuilder('user')
            ->andWhere('user.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function updateUser(User $user): void
    {
        try {
            $this->persist($user);
        } catch (Exception $e) {
            throw new Exception('There is some error douring the save the user info. User Id: ' . $user->getUuid() . ' ' . $e->getMessage());
        }
    }

    public function createUser($name, $email, $password, $discription = ''): string
    {
        $userUuid = Uuid::uuid4()->toString();

        $userEntity = new User();
        $userEntity->setName($name);
        $userEntity->setEmail($email);
        $userEntity->setDescription($discription);
        $userEntity->setUuid($userUuid);

        $hashedPassword = $this->authenticationService->hashPassword($userEntity, $password);
        $userEntity->setPassword($hashedPassword);

        try {
            $entityManager = $this->dbService->getEntityManger();
            $entityManager->persist($userEntity);
            $entityManager->flush();
        } catch (Exception $e) {
            throw new Exception('There is some error with create of the new user. ' . $e->getMessage());
        }

        return $userUuid;
    }
}
