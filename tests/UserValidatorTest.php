<?php

namespace App\Tests\Validator;

use App\Entity\User;
use App\Exceptions\UserDeletedAtBeforeUpdatedAtException;
use App\Exceptions\UserEmailDomainIsUntrustedException;
use App\Exceptions\UserEmailIncorrectException;
use App\Exceptions\UserNameContainsBannedWordsException;
use App\Exceptions\UserNotValidNameException;
use App\Repositories\UserRepository;
use App\Validators\UserValidator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UserValidatorTest extends TestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = \Mockery::mock(EntityManagerInterface::class);
    }

    public function testValidate(): void
    {
        $user = new User();
        $user->setName('validname123');
        $user->setEmail('valid@example.com');
        $user->setCreated(new \DateTimeImmutable('2023-01-01 00:00:00'));
        $user->setDeleted(new \DateTimeImmutable('2023-12-31 23:59:59'));

        $userRepository = \Mockery::mock(UserRepository::class);
        $userRepository->shouldReceive('findOneBy')->with(['name' => $user->getName()])->andReturn(null);
        $userRepository->shouldReceive('findOneBy')->with(['email' => $user->getEmail()])->andReturn(null);
        $this->entityManager->shouldReceive('getRepository')->with(User::class)->andReturn($userRepository);

        $userValidator = new UserValidator($this->entityManager);
        $errors = $userValidator->validate($user);

        $this->assertEmpty($errors);
    }

    public function testValidateInvalidName(): void
    {
        $user = new User();
        $user->setName('inv@lid');
        $user->setEmail('valid@example.com');
        $user->setCreated(new \DateTimeImmutable('2023-01-01 00:00:00'));
        $user->setDeleted(new \DateTimeImmutable('2023-12-31 23:59:59'));


        $userRepository = \Mockery::mock(UserRepository::class);
        $userRepository->shouldReceive('findOneBy')->with(['name' => $user->getName()])->andReturn(null);
        $userRepository->shouldReceive('findOneBy')->with(['email' => $user->getEmail()])->andReturn(null);
        $this->entityManager->shouldReceive('getRepository')->with(User::class)->andReturn($userRepository);
        
        $userValidator = new UserValidator($this->entityManager);
        $errors = $userValidator->validate($user);

        $this->assertContains('User name is not valid', $errors);
    }

    public function testValidateBannedName(): void
    {
        $user = new User();
        $user->setName('badword1');
        $user->setEmail('valid@example.com');
        $user->setCreated(new \DateTimeImmutable('2023-01-01 00:00:00'));
        $user->setDeleted(new \DateTimeImmutable('2023-12-31 23:59:59'));

        $userRepository = \Mockery::mock(UserRepository::class);
        $userRepository->shouldReceive('findOneBy')->with(['name' => $user->getName()])->andReturn(null);
        $userRepository->shouldReceive('findOneBy')->with(['email' => $user->getEmail()])->andReturn(null);
        $this->entityManager->shouldReceive('getRepository')->with(User::class)->andReturn($userRepository);
        
        $userValidator = new UserValidator($this->entityManager);
        $errors = $userValidator->validate($user);

        $this->assertContains('User name contains forbidden words', $errors);
    }

    public function testValidateInvalidEmail(): void
    {
        $user = new User();
        $user->setName('validname123');
        $user->setEmail('invalid-email');
        $user->setCreated(new \DateTimeImmutable('2023-01-01 00:00:00'));
        $user->setDeleted(new \DateTimeImmutable('2023-12-31 23:59:59'));

        $userRepository = \Mockery::mock(UserRepository::class);
        $userRepository->shouldReceive('findOneBy')->with(['name' => $user->getName()])->andReturn(null);
        $userRepository->shouldReceive('findOneBy')->with(['email' => $user->getEmail()])->andReturn(null);
        $this->entityManager->shouldReceive('getRepository')->with(User::class)->andReturn($userRepository);
        
        $userValidator = new UserValidator($this->entityManager);
        $errors = $userValidator->validate($user);

        $this->assertContains('User email is incorrect', $errors);
    }

    public function testValidateDisallowedDomain(): void
    {
        $user = new User();
        $user->setName('validname123');
        $user->setEmail('valid@untrusted.com');
        $user->setCreated(new \DateTimeImmutable());
        $user->setCreated(new \DateTimeImmutable('2023-01-01 00:00:00'));
        $user->setDeleted(new \DateTimeImmutable('2023-12-31 23:59:59'));

        $exception = 'User email domain is untrusted';

        $userRepository = \Mockery::mock(UserRepository::class);
        $userRepository->shouldReceive('findOneBy')->with(['name' => $user->getName()])->andReturn(null);
        $userRepository->shouldReceive('findOneBy')->with(['email' => $user->getEmail()])->andReturn(null);
        $this->entityManager->shouldReceive('getRepository')->with(User::class)->andReturn($userRepository);
        
        $userValidator = new UserValidator($this->entityManager);
        $errors = $userValidator->validate($user);

        $this->assertContains($exception, $errors);
    }

    public function testValidateDeletedBeforeCreated(): void
    {
        $user = new User();
        $user->setName('validname123');
        $user->setEmail('valid@example.com');
        $user->setCreated(new \DateTimeImmutable('2023-01-01 00:00:00'));
        $user->setDeleted(new \DateTimeImmutable('2022-12-31 23:59:59'));

        $userRepository = \Mockery::mock(UserRepository::class);
        $userRepository->shouldReceive('findOneBy')->with(['name' => $user->getName()])->andReturn(null);
        $userRepository->shouldReceive('findOneBy')->with(['email' => $user->getEmail()])->andReturn(null);
        $this->entityManager->shouldReceive('getRepository')->with(User::class)->andReturn($userRepository);
        
        $userValidator = new UserValidator($this->entityManager);
        $errors = $userValidator->validate($user);

        $this->assertContains('User deletedAt before createdAt', $errors);
    }
}
