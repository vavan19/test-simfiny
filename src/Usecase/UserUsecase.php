<?php

namespace App\Usecase;

use App\Entity\User;
use App\Exceptions\UserNotFoundException;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserValidatorInterface;

class UserUsecase
{
  private UserRepositoryInterface $userRepository;
  private UserValidatorInterface $userValidator;

  public function __construct(UserRepositoryInterface $userRepository, UserValidatorInterface $userValidator)
  {
    $this->userRepository = $userRepository;
    $this->userValidator = $userValidator;
  }

  public function select(int $id): ?User
  {
    return $this->userRepository->select($id);
  }

  public function create(User $user): ?User
  {
    $errors = $this->userValidator->validate($user);
    if (!empty($errors)) {
      // обработать ошибки валидации
      return $errors;
    }

    return $this->userRepository->create($user);
  }

  public function update($user): ?User
  {
    $user = $this->userRepository->select($user->getId());
    if (!$user) {
      // пользователь не найден
      return throw new UserNotFoundException('User not found');
    }

    $user = $this->userRepository->update($user);

    $errors = $this->userValidator->validate($user);
    if (!empty($errors)) {
      // обработать ошибки валидации
      return null;
    }

    return $user;
  }

  public function delete(int $id): bool
  {
    $user = $this->userRepository->select($id);
    if (!$user) {
      // пользователь не найден
      return false;
    }

    $this->userRepository->delete($user);
    return true;
  }
}
