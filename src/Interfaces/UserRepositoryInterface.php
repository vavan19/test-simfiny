<?php

namespace App\Interfaces;

use App\Entity\User;

interface UserRepositoryInterface
{
    public function select(int $id): ?User;

    public function create(User $user): User;

    public function update(User $user): User;

    public function delete(User $user): void;
}