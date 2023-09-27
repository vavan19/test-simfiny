<?php

namespace App\Interfaces;

use App\Entity\User;

interface UserValidatorInterface
{
    public function validate(User $user): array;
}