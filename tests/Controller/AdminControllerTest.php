<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Entity\ApiCall;
use App\Repository\UserRepository;
use App\Repository\ContactRepository;
use App\Repository\ApiCallRepository;
use App\Service\ContactService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $userRepository;
    private $contactRepository;
    private $apiCallRepository;
    private $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->contactRepository = static::getContainer()->get(ContactRepository::class);
        $this->apiCallRepository = static::getContainer()->get(ApiCallRepository::class);
        $this->passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
    }

    public function testAdminLoginPage(): void
    {
        $this->client->request('GET', '/admin/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
    }

    public function testAdminLoginWithValidCredentials(): void
    {
        // Create a test admin user
        $adminUser = $this->createAdminUser();

        $this->client->request('GET', '/admin/login');
        $this->client->submitForm('Sign In', [
            '_username' => 'admin@test.com',
            '_password' => 'admin123',
        ]);

        $this->assertResponseRedirects('/admin/');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testAdminLoginWithInvalidCredentials(): void
    {
        $this->client->request('GET', '/admin/login');
        $this->client->submitForm('Sign In', [
            '_username' => 'invalid@test.com',
            '_password' => 'wrongpassword',
        ]);

        $this->assertResponseRedirects('/admin/login');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert-danger');
    }

    public function testAdminDashboardRequiresAuthentication(): void
    {
        $this->client->request('GET', '/admin/');

        $this->assertResponseRedirects('/admin/login');
    }

    public function testAdminDashboardWithAuthentication(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $this->client->request('GET', '/admin/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.dashboard');
    }

    public function testAdminContactsPage(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $this->client->request('GET', '/admin/contacts');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.contacts-list');
    }

    public function testAdminContactsPageWithPagination(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        // Create multiple contacts
        for ($i = 0; $i < 15; $i++) {
            $this->createTestContact($i);
        }

        $this->client->request('GET', '/admin/contacts?page=2');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.pagination');
    }

    public function testAdminContactView(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $contact = $this->createTestContact(1);

        $this->client->request('GET', '/admin/contact/' . $contact->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Contact Details');
    }

    public function testAdminSettingsPage(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $this->client->request('GET', '/admin/settings');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="admin_email"]');
    }

    public function testAdminSettingsUpdate(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $this->client->request('POST', '/admin/settings', [
            'admin_email' => 'newadmin@test.com',
            'cc_email' => 'cc@test.com',
            'recaptcha_site_key' => 'test_site_key',
            'recaptcha_secret_key' => 'test_secret_key',
        ]);

        $this->assertResponseRedirects('/admin/settings');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }

    public function testAdminApiSetupPage(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $this->client->request('GET', '/admin/api-setup');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'API Setup');
    }

    public function testAdminUsersPage(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $this->client->request('GET', '/admin/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.users-list');
    }

    public function testAdminUserCreatePage(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $this->client->request('GET', '/admin/user/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="email"]');
    }

    public function testAdminUserCreate(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $this->client->request('POST', '/admin/user/create', [
            'email' => 'newuser@test.com',
            'first_name' => 'New',
            'last_name' => 'User',
            'password' => 'password123',
            'roles' => ['ROLE_USER'],
            'is_active' => true,
        ]);

        $this->assertResponseRedirects('/admin/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }

    public function testAdminUserEdit(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $user = $this->createTestUser();

        $this->client->request('GET', '/admin/user/' . $user->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testAdminUserUpdate(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $user = $this->createTestUser();

        $this->client->request('POST', '/admin/user/' . $user->getId() . '/edit', [
            'email' => 'updated@test.com',
            'first_name' => 'Updated',
            'last_name' => 'User',
            'password' => 'newpassword123',
            'roles' => ['ROLE_API_USER'],
            'is_active' => true,
        ]);

        $this->assertResponseRedirects('/admin/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }

    public function testAdminUserToggleStatus(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $user = $this->createTestUser();

        $this->client->request('POST', '/admin/user/' . $user->getId() . '/toggle-status');

        $this->assertResponseRedirects('/admin/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }

    public function testAdminUserDelete(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $user = $this->createTestUser();

        $this->client->request('POST', '/admin/user/' . $user->getId() . '/delete');

        $this->assertResponseRedirects('/admin/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }

    public function testAdminUserCannotDeleteSelf(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $this->client->request('POST', '/admin/user/' . $adminUser->getId() . '/delete');

        $this->assertResponseRedirects('/admin/users');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert-danger');
    }

    public function testAdminLogout(): void
    {
        $adminUser = $this->createAdminUser();
        $this->loginAsAdmin($adminUser);

        $this->client->request('GET', '/admin/logout');

        $this->assertResponseRedirects('/');
    }

    private function createAdminUser(): User
    {
        $user = new User();
        $user->setEmail('admin@test.com');
        $user->setFirstName('Admin');
        $user->setLastName('User');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsActive(true);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'admin123');
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function createTestUser(): User
    {
        $user = new User();
        $user->setEmail('testuser@test.com');
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setRoles(['ROLE_USER']);
        $user->setIsActive(true);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function createTestContact(int $index): Contact
    {
        $contact = new Contact();
        $contact->setName('Test Contact ' . $index);
        $contact->setEmail('contact' . $index . '@test.com');
        $contact->setTelephone('+123456789' . $index);
        $contact->setMessage('Test message ' . $index);

        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        return $contact;
    }

    private function loginAsAdmin(User $adminUser): void
    {
        $this->client->request('GET', '/admin/login');
        $this->client->submitForm('Sign In', [
            '_username' => $adminUser->getEmail(),
            '_password' => 'admin123',
        ]);
        $this->client->followRedirect();
    }
} 