<?php

namespace App\Tests\Usecase;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Usecase\UserUsecase;
use App\Validators\UserValidator;
use PHPUnit\Framework\TestCase;

class UserUsecaseTest extends TestCase
{
    private $userRepository;
    private $userValidator;

    protected function setUp(): void
    {
        $this->userRepository = \Mockery::mock(UserRepository::class);
        $this->userValidator = \Mockery::mock(UserValidator::class);
    }

    public function testSelect(): void
    {
        $user = new User();
        $this->userRepository->shouldReceive('select')->with(1)->andReturn($user);

        $userUsecase = new UserUsecase($this->userRepository, $this->userValidator);

        $result = $userUsecase->select(1);

        $this->assertSame($user, $result);
    }

    public function testCreate(): void
    {
        $user = new User();
        $user->setName('testuser123');
        $user->setEmail('test@example.com');

        $this->userRepository->shouldReceive('create')
            ->with($user)
            ->andReturn($user);

        $this->userValidator->shouldReceive('validate')
            ->with(\Mockery::type(User::class))
            ->andReturn([]);

        $userUsecase = new UserUsecase($this->userRepository, $this->userValidator);
        $result = $userUsecase->create($user);

        $this->assertSame($user, $result);
    }

    public function testUpdate(): void
    {
        $user = new User();
        $user->setId(1);
        $user->setName('updatedname123');
        $user->setEmail('updated@example.com');

        $this->userRepository->shouldReceive('select')
            ->with($user->getId())
            ->andReturn($user);

        $this->userRepository->shouldReceive('update')
            ->with($user)
            ->andReturn($user);

        $this->userValidator->shouldReceive('validate')
            ->with(\Mockery::type(User::class))
            ->andReturn([]);

        $userUsecase = new UserUsecase($this->userRepository, $this->userValidator);
        $result = $userUsecase->update($user);

        $this->assertSame($user, $result);
    }

    public function testDelete(): void
    {
        $id = 1;
        $user = new User();

        $this->userRepository->shouldReceive('select')
            ->with($id)
            ->andReturn($user);

        $this->userRepository->shouldReceive('delete')
            ->with($user)
            ->once();

        $userUsecase = new UserUsecase($this->userRepository, $this->userValidator);
        $result = $userUsecase->delete($id);

        $this->assertTrue($result);
    }
}
