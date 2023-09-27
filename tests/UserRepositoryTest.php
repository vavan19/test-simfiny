<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Mockery;

class UserRepositoryTest extends TestCase
{
  private $entityManager;
  private $managerRegistry;

  protected function setUp(): void
  {
    $this->entityManager = \Mockery::mock(EntityManagerInterface::class);
    $this->managerRegistry = \Mockery::mock(ManagerRegistry::class);
  }

  public function testSelect(): void
  {
    $user = new User();
    $this->entityManager->shouldReceive('find')->with(User::class, 1)->andReturn($user);

    $userRepository = new UserRepository($this->managerRegistry, $this->entityManager);

    $result = $userRepository->select(1);

    $this->assertSame($user, $result);
  }

  public function testCreate(): void
  {
    $user = new User();
    $user->setName('username123');
    $user->setEmail('user@example.com');

    $this->entityManager->shouldReceive('persist')->with($user)->once();
    $this->entityManager->shouldReceive('flush')->once();

    $userRepository = new UserRepository($this->managerRegistry, $this->entityManager);

    $result = $userRepository->create($user);

    $this->assertInstanceOf(User::class, $result);
  }
}
