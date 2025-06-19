<?php

namespace App\Tests\Service;

use App\Entity\Contact;
use App\Entity\AdminConfig;
use App\Service\ContactService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;

class ContactServiceTest extends TestCase
{
    private ContactService $contactService;
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        
        $this->contactService = new ContactService(
            $this->entityManager,
            $this->mailer
        );
    }

    public function testCreateContact(): void
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'telephone' => '+1234567890',
            'message' => 'Test message'
        ];

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($contact) use ($contactData) {
                return $contact instanceof Contact
                    && $contact->getName() === $contactData['name']
                    && $contact->getEmail() === $contactData['email']
                    && $contact->getTelephone() === $contactData['telephone']
                    && $contact->getMessage() === $contactData['message'];
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $contact = $this->contactService->createContact($contactData);

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals($contactData['name'], $contact->getName());
        $this->assertEquals($contactData['email'], $contact->getEmail());
        $this->assertEquals($contactData['telephone'], $contact->getTelephone());
        $this->assertEquals($contactData['message'], $contact->getMessage());
    }

    public function testSendNotificationEmail(): void
    {
        $contact = new Contact();
        $contact->setName('John Doe');
        $contact->setEmail('john.doe@example.com');
        $contact->setTelephone('+1234567890');
        $contact->setMessage('Test message');

        $adminEmail = 'admin@example.com';
        $ccEmail = 'cc@example.com';

        // Mock the admin config repository
        $adminConfigRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        
        $adminConfigRepo
            ->method('findOneBy')
            ->willReturnMap([
                [['key' => 'admin_email'], (new AdminConfig())->setValue($adminEmail)],
                [['key' => 'cc_email'], (new AdminConfig())->setValue($ccEmail)]
            ]);

        $this->entityManager
            ->method('getRepository')
            ->with(AdminConfig::class)
            ->willReturn($adminConfigRepo);

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($email) use ($adminEmail, $ccEmail) {
                return $email instanceof Email
                    && in_array($adminEmail, $email->getTo())
                    && in_array($ccEmail, $email->getCc());
            }));

        $this->contactService->sendNotificationEmail($contact);
    }

    public function testSendNotificationEmailWithoutCc(): void
    {
        $contact = new Contact();
        $contact->setName('John Doe');
        $contact->setEmail('john.doe@example.com');
        $contact->setTelephone('+1234567890');
        $contact->setMessage('Test message');

        $adminEmail = 'admin@example.com';

        // Mock the admin config repository
        $adminConfigRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        
        $adminConfigRepo
            ->method('findOneBy')
            ->willReturnMap([
                [['key' => 'admin_email'], (new AdminConfig())->setValue($adminEmail)],
                [['key' => 'cc_email'], null]
            ]);

        $this->entityManager
            ->method('getRepository')
            ->with(AdminConfig::class)
            ->willReturn($adminConfigRepo);

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($email) use ($adminEmail) {
                return $email instanceof Email
                    && in_array($adminEmail, $email->getTo())
                    && empty($email->getCc());
            }));

        $this->contactService->sendNotificationEmail($contact);
    }

    public function testGetAdminConfig(): void
    {
        $configKey = 'admin_email';
        $configValue = 'admin@example.com';

        $adminConfig = new AdminConfig();
        $adminConfig->setKey($configKey);
        $adminConfig->setValue($configValue);

        $adminConfigRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $adminConfigRepo
            ->method('findOneBy')
            ->with(['key' => $configKey])
            ->willReturn($adminConfig);

        $this->entityManager
            ->method('getRepository')
            ->with(AdminConfig::class)
            ->willReturn($adminConfigRepo);

        $result = $this->contactService->getAdminConfig($configKey);

        $this->assertEquals($configValue, $result);
    }

    public function testGetAdminConfigReturnsNullWhenNotFound(): void
    {
        $configKey = 'nonexistent_key';

        $adminConfigRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $adminConfigRepo
            ->method('findOneBy')
            ->with(['key' => $configKey])
            ->willReturn(null);

        $this->entityManager
            ->method('getRepository')
            ->with(AdminConfig::class)
            ->willReturn($adminConfigRepo);

        $result = $this->contactService->getAdminConfig($configKey);

        $this->assertNull($result);
    }

    public function testSetAdminConfig(): void
    {
        $configKey = 'admin_email';
        $configValue = 'newadmin@example.com';

        $adminConfigRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $adminConfigRepo
            ->method('findOneBy')
            ->with(['key' => $configKey])
            ->willReturn(null);

        $this->entityManager
            ->method('getRepository')
            ->with(AdminConfig::class)
            ->willReturn($adminConfigRepo);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($config) use ($configKey, $configValue) {
                return $config instanceof AdminConfig
                    && $config->getKey() === $configKey
                    && $config->getValue() === $configValue;
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->contactService->setAdminConfig($configKey, $configValue);
    }

    public function testSetAdminConfigUpdatesExisting(): void
    {
        $configKey = 'admin_email';
        $oldValue = 'oldadmin@example.com';
        $newValue = 'newadmin@example.com';

        $existingConfig = new AdminConfig();
        $existingConfig->setKey($configKey);
        $existingConfig->setValue($oldValue);

        $adminConfigRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $adminConfigRepo
            ->method('findOneBy')
            ->with(['key' => $configKey])
            ->willReturn($existingConfig);

        $this->entityManager
            ->method('getRepository')
            ->with(AdminConfig::class)
            ->willReturn($adminConfigRepo);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->contactService->setAdminConfig($configKey, $newValue);

        $this->assertEquals($newValue, $existingConfig->getValue());
    }

    public function testSetAdminConfigWithNullValue(): void
    {
        $configKey = 'cc_email';
        $configValue = null;

        $adminConfigRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $adminConfigRepo
            ->method('findOneBy')
            ->with(['key' => $configKey])
            ->willReturn(null);

        $this->entityManager
            ->method('getRepository')
            ->with(AdminConfig::class)
            ->willReturn($adminConfigRepo);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($config) use ($configKey, $configValue) {
                return $config instanceof AdminConfig
                    && $config->getKey() === $configKey
                    && $config->getValue() === $configValue;
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->contactService->setAdminConfig($configKey, $configValue);
    }

    public function testProcessContactForm(): void
    {
        $contactData = [
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'telephone' => '+0987654321',
            'message' => 'Hello, I would like to inquire about your services.'
        ];

        $adminEmail = 'admin@example.com';

        // Mock the admin config repository
        $adminConfigRepo = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $adminConfigRepo
            ->method('findOneBy')
            ->willReturnMap([
                [['key' => 'admin_email'], (new AdminConfig())->setValue($adminEmail)],
                [['key' => 'cc_email'], null]
            ]);

        $this->entityManager
            ->method('getRepository')
            ->with(AdminConfig::class)
            ->willReturn($adminConfigRepo);

        $this->entityManager
            ->expects($this->once())
            ->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->mailer
            ->expects($this->once())
            ->method('send');

        $result = $this->contactService->processContactForm($contactData);

        $this->assertTrue($result);
    }
} 