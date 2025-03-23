<?php

namespace App\Entity;

use App\Repository\UserRolesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRolesRepository::class)]
class UserRoles
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID, unique: true)]
    private ?string $userUuid = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $roleUuid = null;

    public function getUserUuid(): ?string
    {
        return $this->userUuid;
    }

    public function setUserUuid(string $userUuid): static
    {
        $this->userUuid = $userUuid;

        return $this;
    }

    public function getRoleUuid(): ?string
    {
        return $this->roleUuid;
    }

    public function setRoleUuid(string $roleUuid): static
    {
        $this->roleUuid = $roleUuid;

        return $this;
    }
}
