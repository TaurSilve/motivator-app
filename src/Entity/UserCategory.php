<?php

namespace App\Entity;

use App\Repository\UserCategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCategoryRepository::class)]
class UserCategory
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID, unique: true)]
    private ?string $userUuid = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $categoryUuid = null;

    public function getCategoryUuid(): ?string
    {
        return $this->categoryUuid;
    }

    public function setCategoryUuid(string $categoryUuid): static
    {
        $this->categoryUuid = $categoryUuid;

        return $this;
    }

    public function getUserUuid(): ?string
    {
        return $this->userUuid;
    }

    public function setUserUuid(string $userUuid): static
    {
        $this->userUuid = $userUuid;

        return $this;
    }
}
