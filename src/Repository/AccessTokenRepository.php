<?php

namespace App\Repository;

use App\Entity\AccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Ramsey\Uuid\Uuid;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

/**
 * @extends ServiceEntityRepository<AccessToken>
 */
class AccessTokenRepository extends ServiceEntityRepository
{
    private JWTTokenManagerInterface $JWTManager;

    public function __construct(ManagerRegistry $registry, JWTTokenManagerInterface $JWTManager)
    {
        parent::__construct($registry, AccessToken::class);

        $this->JWTManager = $JWTManager;
    }

    public function getAccessTokenByUserUuid($uuid): ?AccessToken
    {
        if (null === $uuid) {
            throw new Exception('The user uuid is required.');
        }

        if (!Uuid::isValid($uuid)) {
            throw new Exception('Please provide valid uuid.');
        }

        return $this->createQueryBuilder('access_token')
        ->andWhere('a.user_uuid = :uuid')
        ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAccessTokenByToken($token): ?AccessToken
    {
        if (null === $token) {
            throw new Exception('The user uuid is required.');
        }

        if (!$this->JWTManager->decode($token)) {
            throw new Exception('Please provide valid token.');
        }

        return $this->createQueryBuilder('access_token')
        ->andWhere('a.access_token = :token')
        ->setParameter('token', $token)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
