<?php

namespace App\Enum;

enum RolesEnum: string
{
  case REGULAR_USER = 'regular_user';
  case ADMIN_USER = 'Administrator';

  public function getUserRoleName(): string
  {
    return match ($this) {
      self::ADMIN_USER => 'Administrator',
      self::REGULAR_USER => 'Regular User',
    };
  }
}
