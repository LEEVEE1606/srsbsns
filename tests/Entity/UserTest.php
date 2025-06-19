<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testUserCreation(): void
    {
        $this->assertInstanceOf(User::class, $this->user);
        $this->assertNotNull($this->user->getCreatedAt());
        $this->assertTrue($this->user->isActive());
    }

    public function testEmailGetterAndSetter(): void
    {
        $email = 'test@example.com';
        $this->user->setEmail($email);
        
        $this->assertEquals($email, $this->user->getEmail());
        $this->assertEquals($email, $this->user->getUserIdentifier());
        $this->assertEquals($email, $this->user->getUsername());
    }

    public function testPasswordGetterAndSetter(): void
    {
        $password = 'hashedPassword123';
        $this->user->setPassword($password);
        
        $this->assertEquals($password, $this->user->getPassword());
    }

    public function testFirstNameGetterAndSetter(): void
    {
        $firstName = 'John';
        $this->user->setFirstName($firstName);
        
        $this->assertEquals($firstName, $this->user->getFirstName());
    }

    public function testLastNameGetterAndSetter(): void
    {
        $lastName = 'Doe';
        $this->user->setLastName($lastName);
        
        $this->assertEquals($lastName, $this->user->getLastName());
    }

    public function testFullName(): void
    {
        $this->user->setFirstName('John');
        $this->user->setLastName('Doe');
        
        $this->assertEquals('John Doe', $this->user->getFullName());
    }

    public function testRolesGetterAndSetter(): void
    {
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $this->user->setRoles($roles);
        
        $expectedRoles = ['ROLE_ADMIN', 'ROLE_USER'];
        $this->assertEquals($expectedRoles, $this->user->getRoles());
    }

    public function testDefaultRoles(): void
    {
        $this->user->setRoles([]);
        
        $roles = $this->user->getRoles();
        $this->assertContains('ROLE_USER', $roles);
        $this->assertCount(1, $roles);
    }

    public function testDuplicateRolesRemoved(): void
    {
        $roles = ['ROLE_ADMIN', 'ROLE_USER', 'ROLE_USER'];
        $this->user->setRoles($roles);
        
        $expectedRoles = ['ROLE_ADMIN', 'ROLE_USER'];
        $this->assertEquals($expectedRoles, $this->user->getRoles());
    }

    public function testIsActiveGetterAndSetter(): void
    {
        $this->user->setIsActive(false);
        $this->assertFalse($this->user->isActive());
        
        $this->user->setIsActive(true);
        $this->assertTrue($this->user->isActive());
    }

    public function testCreatedAtGetterAndSetter(): void
    {
        $createdAt = new \DateTimeImmutable('2023-01-01 12:00:00');
        $this->user->setCreatedAt($createdAt);
        
        $this->assertEquals($createdAt, $this->user->getCreatedAt());
    }

    public function testUpdatedAtGetterAndSetter(): void
    {
        $updatedAt = new \DateTimeImmutable('2023-01-02 12:00:00');
        $this->user->setUpdatedAt($updatedAt);
        
        $this->assertEquals($updatedAt, $this->user->getUpdatedAt());
    }

    public function testSetUpdatedAtCreatesNewDateTime(): void
    {
        $this->user->setUpdatedAt();
        
        $this->assertNotNull($this->user->getUpdatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->user->getUpdatedAt());
    }

    public function testHasRole(): void
    {
        $this->user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        
        $this->assertTrue($this->user->hasRole('ROLE_ADMIN'));
        $this->assertTrue($this->user->hasRole('ROLE_USER'));
        $this->assertFalse($this->user->hasRole('ROLE_API_USER'));
    }

    public function testIsAdmin(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $this->assertTrue($this->user->isAdmin());
        
        $this->user->setRoles(['ROLE_USER']);
        $this->assertFalse($this->user->isAdmin());
    }

    public function testEraseCredentials(): void
    {
        // This method should not throw any exceptions
        $this->user->eraseCredentials();
        $this->assertTrue(true); // Assert that no exception was thrown
    }

    public function testIdGetter(): void
    {
        // ID should be null for new entities
        $this->assertNull($this->user->getId());
    }
} 