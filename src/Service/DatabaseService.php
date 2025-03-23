<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class DatabaseService {

  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function getEntityManger(): EntityManagerInterface
  {
    return $this->entityManager;
  }
}