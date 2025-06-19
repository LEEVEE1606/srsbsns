<?php

namespace App\Tests\Entity;

use App\Entity\Contact;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    private Contact $contact;

    protected function setUp(): void
    {
        $this->contact = new Contact();
    }

    public function testContactCreation(): void
    {
        $this->assertInstanceOf(Contact::class, $this->contact);
        $this->assertNotNull($this->contact->getCreatedAt());
    }

    public function testNameGetterAndSetter(): void
    {
        $name = 'John Doe';
        $this->contact->setName($name);
        
        $this->assertEquals($name, $this->contact->getName());
    }

    public function testEmailGetterAndSetter(): void
    {
        $email = 'john.doe@example.com';
        $this->contact->setEmail($email);
        
        $this->assertEquals($email, $this->contact->getEmail());
    }

    public function testTelephoneGetterAndSetter(): void
    {
        $telephone = '+1234567890';
        $this->contact->setTelephone($telephone);
        
        $this->assertEquals($telephone, $this->contact->getTelephone());
    }

    public function testMessageGetterAndSetter(): void
    {
        $message = 'This is a test message for the contact form.';
        $this->contact->setMessage($message);
        
        $this->assertEquals($message, $this->contact->getMessage());
    }

    public function testCreatedAtGetterAndSetter(): void
    {
        $createdAt = new \DateTime('2023-01-01 12:00:00');
        $this->contact->setCreatedAt($createdAt);
        
        $this->assertEquals($createdAt, $this->contact->getCreatedAt());
    }

    public function testIdGetter(): void
    {
        // ID should be null for new entities
        $this->assertNull($this->contact->getId());
    }

    public function testContactWithAllFields(): void
    {
        $name = 'Jane Smith';
        $email = 'jane.smith@example.com';
        $telephone = '+0987654321';
        $message = 'Hello, I would like to inquire about your services.';
        $createdAt = new \DateTime('2023-01-15 14:30:00');

        $this->contact->setName($name);
        $this->contact->setEmail($email);
        $this->contact->setTelephone($telephone);
        $this->contact->setMessage($message);
        $this->contact->setCreatedAt($createdAt);

        $this->assertEquals($name, $this->contact->getName());
        $this->assertEquals($email, $this->contact->getEmail());
        $this->assertEquals($telephone, $this->contact->getTelephone());
        $this->assertEquals($message, $this->contact->getMessage());
        $this->assertEquals($createdAt, $this->contact->getCreatedAt());
    }

    public function testDefaultCreatedAtOnConstruction(): void
    {
        $contact = new Contact();
        
        $this->assertInstanceOf(\DateTimeInterface::class, $contact->getCreatedAt());
        $this->assertGreaterThanOrEqual(
            new \DateTime('-1 second'),
            $contact->getCreatedAt()
        );
    }
} 