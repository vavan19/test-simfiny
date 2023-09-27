<?php

namespace App\Validators;

use App\Entity\User;
use App\Exceptions\UserDeletedAtBeforeUpdatedAtException;
use App\Exceptions\UserEmailAlreadyTakenException;
use App\Exceptions\UserEmailDomainIsUntrustedException;
use App\Exceptions\UserEmailIncorrectException;
use App\Exceptions\UserNameAlreadyTakenException;
use App\Exceptions\UserNameContainsBannedWordsException;
use App\Exceptions\UserNotValidNameException;
use App\Interfaces\UserValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserValidator implements UserValidatorInterface
{
  private array $forbiddenWords = ['badword1', 'badword2'];  // Замените на свой список запрещенных слов
  private array $untrustedDomains = ['untrusted.com', 'spam.com'];  // Замените на свой список ненадежных доменов
  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function validate(User $user): array
  {
    $errors = [];

    // Проверка имени
    if (!preg_match('/^[a-z0-9]{8,}$/', $user->getName())) {
      $errors[] = 'User name is not valid';
    }

    foreach ($this->forbiddenWords as $word) {
      if (strpos($user->getName(), $word) !== false) {
        $errors[] = 'User name contains forbidden words';
        break;
      }
    }

    // Проверка уникальности имени
    $existingUserWithName = $this->entityManager->getRepository(User::class)->findOneBy(['name' => $user->getName()]);
    if ($existingUserWithName && $existingUserWithName->getId() !== $user->getId()) {
      $errors[] = 'User name already taken';
    }

    // Проверка электронной почты
    if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'User email is incorrect';
    }

    $emailDomain = substr(strrchr($user->getEmail(), "@"), 1);
    if (in_array($emailDomain, $this->untrustedDomains)) {
      $errors[] = 'User email domain is untrusted';
    }

    // Проверка уникальности электронной почты
    $existingUserWithEmail = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
    if ($existingUserWithEmail && $existingUserWithEmail->getId() !== $user->getId()) {
      $errors[] = 'User email already taken';
    }

    // Проверка удаления
    if ($user->getDeleted() !== null && $user->getDeleted() < $user->getCreated()) {
      $errors[] = 'User deletedAt before createdAt';
    }

    var_dump($errors);
    return $errors;
  }

  function getBannedWords()
  {
    return $this->forbiddenWords;
  }

  function getUntrustedDomains()
  {
    return $this->untrustedDomains;
  }
}
