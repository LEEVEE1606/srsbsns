<?php

namespace App\Tests\Controller;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HomeControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
    }

    public function testHomePage(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'SRSBSNS Test App');
        $this->assertSelectorExists('.hero-section');
        $this->assertSelectorExists('.feature-card');
    }

    public function testAboutPage(): void
    {
        $this->client->request('GET', '/about');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'About');
    }

    public function testApiDocsPage(): void
    {
        $this->client->request('GET', '/api-docs');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'API Documentation');
    }

    public function testContactPage(): void
    {
        $this->client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Contact Us');
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="contact[name]"]');
        $this->assertSelectorExists('input[name="contact[email]"]');
        $this->assertSelectorExists('input[name="contact[telephone]"]');
        $this->assertSelectorExists('textarea[name="contact[message]"]');
    }

    public function testContactFormSubmission(): void
    {
        $this->client->request('GET', '/contact');
        $this->client->submitForm('Send Message', [
            'contact[name]' => 'John Doe',
            'contact[email]' => 'john.doe@example.com',
            'contact[telephone]' => '+1234567890',
            'contact[message]' => 'This is a test message from the contact form.',
        ]);

        $this->assertResponseRedirects('/contact');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }

    public function testContactFormValidation(): void
    {
        $this->client->request('GET', '/contact');
        $this->client->submitForm('Send Message', [
            'contact[name]' => '',
            'contact[email]' => 'invalid-email',
            'contact[telephone]' => '',
            'contact[message]' => '',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-danger');
    }

    public function testContactFormWithValidData(): void
    {
        $this->client->request('GET', '/contact');
        $this->client->submitForm('Send Message', [
            'contact[name]' => 'Jane Smith',
            'contact[email]' => 'jane.smith@example.com',
            'contact[telephone]' => '+0987654321',
            'contact[message]' => 'Hello, I would like to inquire about your services.',
        ]);

        $this->assertResponseRedirects('/contact');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert-success');

        // Verify contact was saved to database
        $contact = $this->entityManager->getRepository(Contact::class)->findOneBy([
            'email' => 'jane.smith@example.com'
        ]);
        $this->assertNotNull($contact);
        $this->assertEquals('Jane Smith', $contact->getName());
    }

    public function testContactFormWithLongData(): void
    {
        $this->client->request('GET', '/contact');
        $this->client->submitForm('Send Message', [
            'contact[name]' => str_repeat('A', 101), // Exceeds 100 character limit
            'contact[email]' => 'test@example.com',
            'contact[telephone]' => '+1234567890',
            'contact[message]' => 'Test message',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-danger');
    }

    public function testContactFormWithInvalidEmail(): void
    {
        $this->client->request('GET', '/contact');
        $this->client->submitForm('Send Message', [
            'contact[name]' => 'Test User',
            'contact[email]' => 'not-an-email',
            'contact[telephone]' => '+1234567890',
            'contact[message]' => 'Test message',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-danger');
    }

    public function testContactFormWithLongTelephone(): void
    {
        $this->client->request('GET', '/contact');
        $this->client->submitForm('Send Message', [
            'contact[name]' => 'Test User',
            'contact[email]' => 'test@example.com',
            'contact[telephone]' => str_repeat('1', 21), // Exceeds 20 character limit
            'contact[message]' => 'Test message',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-danger');
    }

    public function testHomePageLinks(): void
    {
        $this->client->request('GET', '/');

        // Test that all important links exist
        $this->assertSelectorExists('a[href="/contact"]');
        $this->assertSelectorExists('a[href="/api-docs"]');
        $this->assertSelectorExists('a[href="/admin/login"]');
    }

    public function testHomePageFeatures(): void
    {
        $this->client->request('GET', '/');

        // Test that feature cards are present
        $this->assertSelectorExists('.feature-card');
        $this->assertSelectorTextContains('.feature-card', 'Contact System');
        $this->assertSelectorTextContains('.feature-card', 'REST API');
        $this->assertSelectorTextContains('.feature-card', 'Security');
        $this->assertSelectorTextContains('.feature-card', 'Admin Panel');
    }

    public function testHomePageTechnologyStack(): void
    {
        $this->client->request('GET', '/');

        // Test that technology stack section is present
        $this->assertSelectorTextContains('h2', 'Technology Stack');
        $this->assertSelectorTextContains('li', 'Symfony 7.3');
        $this->assertSelectorTextContains('li', 'Bootstrap 5');
        $this->assertSelectorTextContains('li', 'API Platform');
        $this->assertSelectorTextContains('li', 'JWT Authentication');
    }

    public function testHomePageCallToAction(): void
    {
        $this->client->request('GET', '/');

        // Test that call-to-action buttons are present
        $this->assertSelectorExists('.btn-custom');
        $this->assertSelectorTextContains('.btn-custom', 'Contact Us');
        $this->assertSelectorTextContains('a', 'API Documentation');
    }

    public function testPageTitles(): void
    {
        $this->client->request('GET', '/');
        $this->assertSelectorTextContains('title', 'Home - SRSBSNS Test App');

        $this->client->request('GET', '/about');
        $this->assertSelectorTextContains('title', 'About - SRSBSNS Test App');

        $this->client->request('GET', '/contact');
        $this->assertSelectorTextContains('title', 'Contact Us - SRSBSNS Test App');

        $this->client->request('GET', '/api-docs');
        $this->assertSelectorTextContains('title', 'API Documentation - SRSBSNS Test App');
    }

    public function testResponsiveDesign(): void
    {
        $this->client->request('GET', '/');

        // Test that Bootstrap classes are present for responsive design
        $this->assertSelectorExists('.container');
        $this->assertSelectorExists('.row');
        $this->assertSelectorExists('.col-lg-8');
        $this->assertSelectorExists('.col-md-6');
    }

    public function testNavigationStructure(): void
    {
        $this->client->request('GET', '/');

        // Test that navigation elements are present
        $this->assertSelectorExists('nav');
        $this->assertSelectorExists('.navbar');
    }
} 